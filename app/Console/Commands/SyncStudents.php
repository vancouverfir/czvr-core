<?php

namespace App\Console\Commands;

use App\Helpers\CreateNote;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\Teacher;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\TrainingProgressionService;
use App\Services\VatcanClient;
use App\Support\Authorization\SystemRoleCatalog;
use App\Support\VatcanFacilities;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncStudents extends Command
{
    protected $signature = 'vancouver:sync-students';

    protected $description = 'Syncs student from roster without certifications';

    public function handle(TrainingProgressionService $progression): int
    {
        $this->info('Starting Student Sync '.now().'');
        Log::info('Student Sync Started.');

        $apiKey = config('services.vatcan.key');

        if (! $apiKey) {
            $this->error('VATCAN_API_KEY Missing!');

            return self::FAILURE;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders(['Authorization' => 'Token '.$apiKey])
                ->connectTimeout(5)
                ->timeout(35)
                ->retry(2, 500, throw: false)
                ->get(VatcanClient::url('facility/roster'));

            if ($response->failed()) {
                $this->error('API Request Failed. Status: '.$response->status());

                return self::FAILURE;
            }

            $data = $response->json('data');

            if (! is_array($data['controllers'] ?? null) || ! is_array($data['visitors'] ?? null)) {
                $this->error('VATCAN roster response was malformed!');

                return self::FAILURE;
            }

            $this->info('Processing '.count($data['controllers']).' home and '.count($data['visitors']).' visiting controllers.');

            $members = [];

            foreach ($data['controllers'] as $controller) {
                $members[] = ['member' => $controller, 'visitor' => false, 'joined' => $controller['facility_join'] ?? null];
            }

            foreach ($data['visitors'] as $visitor) {
                $joined = collect($visitor['visiting_facilities'] ?? [])
                    ->firstWhere('fir.name_long', 'Vancouver FIR')['created_at'] ?? null;
                $members[] = ['member' => $visitor, 'visitor' => true, 'joined' => $joined];
            }

            // Entries without a usable CID cannot be matched to anyone
            $members = array_filter($members, fn ($entry) => is_array($entry['member']) && (int) ($entry['member']['cid'] ?? 0) > 0);

            Cache::forever(VatcanFacilities::HOME_FACILITY_CACHE_KEY, collect($members)
                ->filter(fn ($entry) => is_numeric($entry['member']['facility'] ?? null))
                ->mapWithKeys(fn ($entry) => [(int) $entry['member']['cid'] => (int) $entry['member']['facility']])
                ->all());

            foreach ($members as $entry) {
                $this->syncMemberRating($entry['member'], $progression);
                $this->syncStudentStatus((int) $entry['member']['cid'], $entry['joined'], $entry['visitor']);
            }

            // Staff first, so a newly synced instructor exists before students are assigned to them
            $roleIds = [
                'instructor' => Role::where('slug', SystemRoleCatalog::INSTRUCTOR)->value('id'),
                'mentor' => Role::where('slug', SystemRoleCatalog::MENTOR)->value('id'),
            ];

            foreach ($members as $entry) {
                $this->syncTrainingStaff($entry['member'], $roleIds);
            }

            foreach ($members as $entry) {
                $this->syncStudentInstructor($entry['member']);
            }

            $this->info('Sync Students Done');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Student sync failed!');
            Log::critical('Student sync raised an exception!', ['message' => $e->getMessage()]);

            return self::FAILURE;
        }
    }

    public function syncStudentStatus($cid, $facilityJoin, $isVisitor = false)
    {
        $type = $isVisitor ? 'Visitor' : 'Home';

        $roster = DB::table('roster')->where('cid', $cid)->first();
        if (! $roster) {
            $this->warn("[$type] CID $cid: No local roster record. Skipping.");

            return;
        }

        $hasCerts = ($roster->delgnd || $roster->delgnd_t2 || $roster->twr || $roster->twr_t2 || $roster->dep || $roster->app || $roster->app_t2 || $roster->ctr || $roster->fss);

        if ($hasCerts) {
            $this->line("[$type] CID $cid: Certified controller. Skipping.");

            return;
        }

        if (Student::where('user_id', $cid)->exists()) {
            $this->line("[$type] CID $cid: Already in training queue.");

            return;
        }

        DB::transaction(function () use ($cid, $facilityJoin, $isVisitor, $type) {
            $status = $isVisitor ? Student::STATUS_VISITOR_WAITLIST : Student::STATUS_WAITLIST;
            $position = Student::nextQueuePosition($status);

            $student = new Student([
                'user_id' => $cid,
                'position' => $position,
                'status' => $status,
                'renewed_at' => now(),
            ]);
            // Created_at is not fillable, so the join date has to be set directly
            $student->created_at = $facilityJoin ? Carbon::parse($facilityJoin) : now();
            $student->save();

            $labels = $isVisitor
                ? ['Visitor Waitlist', User::find($cid)?->division_code === 'CAN' ? 'Visitor VATCAN' : 'Visitor Non-VATCAN']
                : ['Waitlist', 'Availability Requested'];

            foreach ($labels as $labelName) {
                if (! $student->addLabel($labelName)) {
                    throw new \RuntimeException("Training label '{$labelName}' does not exist!");
                }
            }

            CreateNote::newNote($student->id, 'Created', 'Student created automatically by System');

            $this->info("[$type] CID $cid: Added to queue at position {$position}");
        });
    }

    private function syncMemberRating(array $member, TrainingProgressionService $progression): void
    {
        if (! array_key_exists('rating', $member)) {
            return;
        }

        $cid = (int) $member['cid'];
        $rating = $member['rating'];

        if (! is_int($rating) && ! (is_string($rating) && ctype_digit($rating))) {
            Log::warning('VATCAN roster contained an invalid rating!', ['cid' => $cid]);

            return;
        }

        $user = User::find($cid);
        if (! $user) {
            $this->warn("CID {$cid}: Cannot synchronize rating before the local user exists!");

            return;
        }

        $progression->synchronizeRating($user, (int) $rating);
    }

    // VATCAN decides who is an instructor or mentor! Members whose entry has neither field are left alone!
    private function syncTrainingStaff(array $member, array $roleIds): void
    {
        if (! array_key_exists('is_instructor', $member) && ! array_key_exists('is_mentor', $member)) {
            return;
        }

        $cid = (int) $member['cid'];
        $user = User::find($cid);

        if (! $user) {
            $this->warn("CID {$cid}: Cannot synchronize instructor status before the local user exists!");

            return;
        }

        $isInstructor = filter_var($member['is_instructor'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isMentor = filter_var($member['is_mentor'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $profile = Instructor::firstOrNew(['user_id' => $cid]);
        $this->syncRosterInstructorStatus($cid, $isInstructor);

        if (! $isInstructor && ! $isMentor) {
            if ($profile->exists) {
                $profile->update(['active' => false]);
            }

            Teacher::where('user_cid', $cid)->delete();
            $user->roles()->detach(array_filter($roleIds));

            return;
        }

        $profile->qualification = $isInstructor ? 'Instructor' : 'Mentor';
        if (blank($profile->email)) {
            $profile->email = (string) ($member['email'] ?? $user->email ?? '');
        }
        $profile->active = true;
        $profile->save();

        // Mentor badges follow the VATCAN rating, instructors already cover every position so they get none
        $rating = (int) ($member['rating'] ?? 0);
        $teacher = Teacher::firstOrNew(['user_cid' => $cid]);
        $teacher->is_instructor = $isInstructor;
        $teacher->is_local = ! $isInstructor && $rating >= 3;
        $teacher->is_radar = ! $isInstructor && $rating >= 4;
        $teacher->is_enroute = ! $isInstructor && $rating >= 5;
        $teacher->save();

        $role = $isInstructor ? 'instructor' : 'mentor';
        $otherRole = $isInstructor ? 'mentor' : 'instructor';

        if ($roleIds[$otherRole]) {
            $user->roles()->detach($roleIds[$otherRole]);
        }
        if ($roleIds[$role]) {
            $user->roles()->syncWithoutDetaching([$roleIds[$role]]);
        }
    }

    private function syncRosterInstructorStatus(int $cid, bool $isInstructor): void
    {
        RosterMember::where('cid', $cid)
            ->where('status', $isInstructor ? 'home' : 'instructor')
            ->update(['status' => $isInstructor ? 'instructor' : 'home']);
    }

    private function syncStudentInstructor(array $member): void
    {
        if (! array_key_exists('instructor', $member)) {
            return;
        }

        $student = Student::where('user_id', (int) $member['cid'])->first();
        if (! $student) {
            return;
        }

        // VATCAN is the source of truth the local instructor always follows it
        $instructorCid = (int) ($member['instructor'] ?? 0);
        $instructorId = $instructorCid > 0
            ? Instructor::where('user_id', $instructorCid)->where('active', true)->value('id')
            : null;

        if ($instructorCid > 0 && ! $instructorId) {
            Log::warning('VATCAN assigned an instructor who is not active in the local roster!', [
                'student_cid' => $student->user_id,
                'instructor_cid' => $instructorCid,
            ]);
        }

        if ((int) $student->instructor_id === (int) $instructorId) {
            return;
        }

        $student->update(['instructor_id' => $instructorId]);
        CreateNote::newNote(
            $student->id,
            $instructorId ? 'Instructor Synchronized' : 'Instructor Unassigned',
            match (true) {
                (bool) $instructorId => "VATCAN synchronized instructor CID {$instructorCid} to this student!",
                $instructorCid > 0 => "VATCAN lists instructor CID {$instructorCid}, who is not an active instructor here, so the local assignment was removed!",
                default => 'VATCAN removed the instructor assignment from this student!',
            },
        );
    }
}
