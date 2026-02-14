<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use App\Helpers\CreateNote;

class SyncStudents extends Command
{
    protected $signature = 'vancouver:sync-students';
    protected $description = 'Syncs student from roster without certifications';

    public function handle()
    {
        $this->info('Starting Student Sync ' . now() . '');
        Log::info('Student Sync Started.');

        $apiKey = env('VATCAN_API_KEY');
        $apiUrl = "https://vatcan.ca/api/v2/facility/roster?api_key=";

        if (!$apiKey) {
            $this->error('VATCAN_API_KEY Missing!');
            return;
        }

        try {
            $response = Http::timeout(35)->get($apiUrl . $apiKey);

            if ($response->failed()) {
                $this->error("API Request Failed. Status: " . $response->status());
                return;
            }

            $data = $response->json()['data'];
            $this->info('Processing ' . count($data['controllers']) . ' home and ' . count($data['visitors']) . ' visiting controllers.');

            foreach ($data['controllers'] as $controller) {
                $this->syncStudentStatus($controller['cid'], $controller['facility_join'], false);
            }

            foreach ($data['visitors'] as $visitor) {
                $facilityJoin = null;
                if (isset($visitor['visiting_facilities'])) {
                    foreach ($visitor['visiting_facilities'] as $vf) {
                        if ($vf['fir']['name_long'] === 'Vancouver FIR') {
                            $facilityJoin = $vf['created_at'];
                            break;
                        }
                    }
                }
                $this->syncStudentStatus($visitor['cid'], $facilityJoin, true);
            }

            $this->info('Sync Students Done');

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            Log::critical("Student Sync Exception: " . $e->getMessage());
        }
    }

    public function syncStudentStatus($cid, $facilityJoin, $isVisitor = false)
    {
        $type = $isVisitor ? 'Visitor' : 'Home';

        $roster = DB::table('roster')->where('cid', $cid)->first();
        if (!$roster) {
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
            $maxPos = Student::max('position') ?? 0;
            $createdAt = $facilityJoin ? Carbon::parse($facilityJoin) : now();

            $student = Student::create([
                'user_id' => $cid,
                'position' => $maxPos + 1,
                'status' => $isVisitor ? 3 : 0,
                'renewed_at' => now(),
                'created_at' => $createdAt,
                'updated_at' => now(),
            ]);

            if (!$isVisitor) {
                DB::table('student_interactive_labels')->insert([
                    ['student_label_id' => 8, 'student_id' => $student->id, 'created_at' => now(), 'updated_at' => now()],
                    ['student_label_id' => 7, 'student_id' => $student->id, 'created_at' => now(), 'updated_at' => now()],
                ]);
            } else {
                DB::table('student_interactive_labels')->insert([
                    'student_label_id' => 9, 'student_id' => $student->id, 'created_at' => now(), 'updated_at' => now()
                ]);

                $user = User::find($cid);
                $divLabel = ($user && $user->division_code === 'CAN') ? 17 : 16;
                DB::table('student_interactive_labels')->insert([
                    'student_label_id' => $divLabel, 'student_id' => $student->id, 'created_at' => now(), 'updated_at' => now()
                ]);
            }

            CreateNote::newNote($student->id, 'Created', 'Student created automatically by System');

            $this->info("[$type] CID $cid: Added to queue at position " . ($maxPos + 1));
        });
    }
}
