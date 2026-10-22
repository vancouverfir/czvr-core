<?php

namespace App\Http\Controllers\AtcTraining;

use App\Console\Commands\RenewNotification;
use App\Helpers\CreateNote;
use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentNote;
use App\Models\AtcTraining\TrainingWaittime;
use App\Models\Publications\AtcResource;
use App\Models\Users\User;
use App\Notifications\RenewalSuccessfulNotification;
use App\Services\TrainingProgressionService;
use App\Services\VatcanInstructorClient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class TrainingController extends Controller
{
    public function __construct(private readonly VatcanInstructorClient $vatcan)
    {
    }

    public function index(VatcanController $vatcanController): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->guest(route('auth.connect.login'));
        }

        $student = Student::with('user.rosterProfile')->where('user_id', Auth::id())->first();
        $instructor = Auth::user()->activeInstructorProfile;

        $yourStudents = $instructor
            ? Student::with([
                'user.rosterProfile',
                'instructingSessions' => fn ($query) => $query
                    ->where('start_time', '>=', now())
                    ->orderBy('start_time'),
                'instructingSessions.student.user',
            ])->where('instructor_id', $instructor->id)->get()
            : collect();

        $upcomingSessions = $yourStudents
            ->flatMap(fn (Student $assignedStudent) => $assignedStudent->instructingSessions)
            ->sortBy('start_time')
            ->take(5)
            ->values();

        return view('training.indexinstructor', [
            'yourStudents' => $yourStudents,
            'upcomingSessions' => $upcomingSessions,
            'student' => $student,
            'preview' => false,
            ...$this->studentDashboard($student, $vatcanController),
        ]);
    }

    public function previewStudent($id, VatcanController $vatcanController): View
    {
        $student = Student::with('user.rosterProfile')->findOrFail($id);

        return view('training.indexinstructor', [
            'yourStudents' => collect(),
            'upcomingSessions' => collect(),
            'student' => $student,
            'preview' => true,
            ...$this->studentDashboard($student, $vatcanController),
        ]);
    }

    private function studentDashboard(?Student $student, VatcanController $vatcanController): array
    {
        if (! $student) {
            return ['waitlistPosition' => null, 'studentChecklistGroups' => null, 'training_time' => null, 'vatcanNotes' => []];
        }

        $waitlistPosition = null;

        if ($student->isWaitlisted()) {
            $waitlistIds = Student::where('status', $student->status)
                ->orderBy('position')
                ->pluck('id')
                ->toArray();
            $index = array_search($student->id, $waitlistIds, true);
            $waitlistPosition = $index !== false ? $index + 1 : null;
        }

        $student->load('checklistItems.checklistItem.checklist');

        return [
            'waitlistPosition' => $waitlistPosition,
            'studentChecklistGroups' => $student->checklistItems->groupBy(fn ($item) => $item->checklistItem->checklist->name),
            'training_time' => TrainingWaittime::find(1),
            'vatcanNotes' => $this->latestVatcanNotes($vatcanController, $student),
        ];
    }

    public function joinvancouver(): View
    {
        return view('joinvancouver');
    }

    public function viewResources(): View
    {
        $atcResources = AtcResource::all()->sortBy('title');

        return view('training.resources', compact('atcResources'));
    }

    public function newNoteView($id): View
    {
        $student = Student::findOrFail($id);

        return view('training.students.newnote', compact('student'));
    }

    public function addNote(Request $request, $id): RedirectResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        StudentNote::create([
            'student_id' => $student->id,
            'author_cid' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'created_at' => now(),
        ]);

        return redirect()->route('training.students.view', ['id' => $student->id])->withSuccess('Staff Comment added for '.$student->user->fullName('FLC').'!');
    }

    public function completeTraining(
        Request $request,
        Student $student,
        TrainingProgressionService $progression,
    ): RedirectResponse {
        $student->loadMissing('labels.label', 'user');
        $isRecertification = $student->isRecertification();

        if ($isRecertification && (int) $student->user->rating_id < 5) {
            $nextStage = $progression->completeRecertification($student, Auth::user());

            if ($nextStage === false) {
                return back()->withError('Recertification could not be completed because the next normal training package is missing!');
            }

            return redirect()->route('training.students.view', ['id' => $student->id])->with(
                'success',
                'Recertification completed for '.$student->user->fullName('FLC').'. '.$nextStage.' is now active as normal training!',
            );
        }

        if ($student->instructor_id) {
            try {
                $this->vatcan->unassign((int) $student->user_id);
            } catch (Throwable $exception) {
                return $this->vatcanFailure($exception, (int) $student->user_id, 'complete training');
            }
        }

        $student->instructor_id = null;

        if ($isRecertification) {
            $progression->completeRecertification($student, Auth::user());
        } else {
            $student->markTrainingCompleted();

            CreateNote::newNote(
                $student->id,
                'Completed Training',
                Auth::user()->fullName('FLC').' marked training as completed',
            );
        }

        return redirect()->route('training.students.completed')->with(
            'success',
            ($isRecertification ? 'Completed recertification for ' : 'Completed training for ').$student->user->fullName('FLC').'!',
        );
    }

    public function trainingTime(): View
    {
        $training_time = TrainingWaittime::find(1);
        $waitlist = Student::where('status', Student::STATUS_WAITLIST)->get();
        $visitorWaitlist = Student::where('status', Student::STATUS_VISITOR_WAITLIST)->get();

        return view('trainingtimes', compact('training_time', 'waitlist', 'visitorWaitlist'));
    }

    public function editTrainingTime(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'waitTime' => ['required', 'string', 'max:100'],
            'trainingTimeColour' => ['required', 'string', 'max:30'],
        ]);

        TrainingWaittime::findOrFail(1)->update([
            'wait_length' => $validated['waitTime'],
            'colour' => $validated['trainingTimeColour'],
        ]);

        return back()->withSuccess('Waittime updated successfully!');
    }

    public function instructorsIndex(): View
    {
        $instructors = Instructor::where('active', true)->get();

        return view('training.instructors.index', compact('instructors'));
    }

    public function newStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id', 'unique:students,user_id'],
            'instructor' => ['nullable', 'integer', Rule::exists('instructors', 'id')->where('active', true)],
            'is_visitor' => ['required', 'boolean'],
            'visitor_type' => ['nullable', 'required_if:is_visitor,1', 'in:vatcan,non_vatcan'],
        ]);

        $isVisitor = (bool) $validated['is_visitor'];
        $status = $isVisitor ? Student::STATUS_VISITOR_WAITLIST : Student::STATUS_WAITLIST;
        $position = Student::nextQueuePosition($status);

        if (! empty($validated['instructor'])) {
            $instructor = Instructor::where('active', true)->findOrFail($validated['instructor']);

            try {
                $this->vatcan->assign((int) $validated['student_id'], (int) $instructor->user_id, (int) Auth::id());
            } catch (Throwable $exception) {
                return $this->vatcanFailure($exception, (int) $validated['student_id'], 'assign the instructor');
            }
        }

        $student = Student::create([
            'user_id' => $validated['student_id'],
            'instructor_id' => $validated['instructor'] ?? null,
            'position' => $position,
            'status' => $status,
        ]);

        $labels = $isVisitor
            ? ['Visitor Waitlist', $validated['visitor_type'] === 'vatcan' ? 'Visitor VATCAN' : 'Visitor Non-VATCAN']
            : ['Waitlist'];

        foreach ($labels as $labelName) {
            $student->addLabel($labelName);
        }

        return redirect()->route('training.students.view', ['id' => $student->id])->withSuccess('Added New Visitor/Student - '.$student->user->fullName('FLC').'!');
    }

    public function AllStudents(): View
    {
        $lists = StudentLabel::where('visible_home', true)
            ->with([
                'students.student.user.rosterProfile',
                'students.student.labels.label',
                'students.student.instructor.user',
            ])
            ->get();

        return view('training.students.allstudents', compact('lists'));
    }

    public function completedStudents(): View
    {
        $students = Student::where('status', Student::STATUS_COMPLETED)->get();

        return view('training.students.completed', compact('students'));
    }

    public function newStudents(): View
    {
        $waitlistStudents = Student::with(['user.rosterProfile', 'latestWaitlistContactEvent.author'])
            ->where('status', Student::STATUS_WAITLIST)
            ->orderBy('position')
            ->get();
        $visitorWaitlist = Student::with(['user.rosterProfile', 'latestWaitlistContactEvent.author'])
            ->where('status', Student::STATUS_VISITOR_WAITLIST)
            ->orderBy('position')
            ->get();

        $potentialstudent = User::all();
        $instructors = Instructor::where('active', true)->get();

        return view('training.students.waitlist', compact('waitlistStudents', 'visitorWaitlist', 'potentialstudent', 'instructors'));
    }

    public function updateWaitlistContact(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'contacted' => ['required', 'boolean'],
        ]);

        return DB::transaction(function () use ($student, $validated): RedirectResponse {
            $student = Student::with('latestWaitlistContactEvent.author')
                ->lockForUpdate()
                ->findOrFail($student->id);

            if (! $student->isWaitlisted()) {
                return back()->withError('Contact tracking is only available for waitlisted students and visitors!');
            }

            $latestEvent = $student->latestWaitlistContactEvent;
            $currentlyContacted = $latestEvent?->title === 'Waitlist Contacted';

            if ((bool) $validated['contacted']) {
                if ($currentlyContacted) {
                    $contactName = $latestEvent->author?->fullName('FLC') ?? 'another staff member';

                    return back()->with('info', 'This person was already marked as contacted by '.$contactName.'!');
                }

                StudentNote::create([
                    'student_id' => $student->id,
                    'author_cid' => Auth::id(),
                    'title' => 'Waitlist Contacted',
                    'content' => '<u>'.e(Auth::user()->fullName('FLC')).'</u> marked as contacted',
                    'created_at' => now(),
                ]);

                return back()->withSuccess('Marked as contacted!');
            }

            if (! $currentlyContacted) {
                return back()->with('info', 'This person is not currently marked as contacted!');
            }

            StudentNote::create([
                'student_id' => $student->id,
                'author_cid' => Auth::id(),
                'title' => 'Waitlist Contact Cleared',
                'content' => '<u>'.e(Auth::user()->fullName('FLC')).'</u> unmarked as contacted',
                'created_at' => now(),
            ]);

            return back()->withSuccess('Contact status cleared!');
        });
    }

    public function viewStudent($id, VatcanController $vatcanController, TrainingProgressionService $progression): View
    {
        $student = Student::with([
            'checklistItems.checklistItem.checklist',
            'labels.label',
            'user.rosterProfile',
        ])->findOrFail($id);

        $instructors = Instructor::where('active', true)->get();
        $studentChecklistGroups = $student->checklistItems->groupBy(fn ($item) => $item->checklistItem->checklist->name);

        $interactiveLabelIds = StudentInteractiveLabels::where('student_id', $student->id)
            ->pluck('student_label_id')
            ->toArray();

        $isVisitor = $student->isVisitor();
        $labels = StudentLabel::assignableTo($student)
            ->whereNotIn('id', $interactiveLabelIds)
            ->get();

        $canStartRecertification = $student->isWaitlisted()
            && (int) $student->user->rating_id >= 2
            && ! $student->isRecertification();

        $recertificationPackages = $canStartRecertification
            ? $progression->recertificationPackages($student)
            : collect();

        $nextLabel = $student->nextTrainingLabel($isVisitor ? Student::trainingOrder(true) : Student::RATING_TRAINING_LABELS);

        $vatcanNotes = $this->latestVatcanNotes($vatcanController, $student);

        return view('training.students.viewstudent', compact('student', 'instructors', 'labels', 'studentChecklistGroups', 'isVisitor', 'nextLabel', 'vatcanNotes', 'canStartRecertification', 'recertificationPackages'));
    }

    public function waiveConsolidation(Student $student, TrainingProgressionService $progression): RedirectResponse
    {
        if (! $progression->waiveConsolidation($student, Auth::user())) {
            return back()->with('info', 'This student has no active consolidation requirement!');
        }

        return back()->withSuccess('The consolidation requirement was waived! Training is now available!');
    }

    public function sort(Request $request): JsonResponse
    {
        return $this->reorderQueue($request, Student::STATUS_WAITLIST);
    }

    public function sortVisitor(Request $request): JsonResponse
    {
        return $this->reorderQueue($request, Student::STATUS_VISITOR_WAITLIST);
    }

    private function reorderQueue(Request $request, int $status): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer', 'exists:students,id'],
            'order.*.position' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['order'] as $item) {
            Student::where('id', $item['id'])->where('status', $status)->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    public function editTimes(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeStudentAccess($student);

        $validated = $this->validateAvailability($request);

        $student->availability = $validated['availability'];
        $student->times = $validated['availability_comment'];
        $availabilityChanged = $student->isDirty(['availability', 'times']);
        $student->save();

        $this->clearWaitlistContactAfterAvailabilityChange($student, $availabilityChanged);

        $student->labels()->whereHas('label', fn ($query) => $query->where('name', 'Availability Requested'))->delete();

        return redirect()->back()->with('success', 'Availability updated successfully!');
    }

    public function showRenewTraining(string $token): View|RedirectResponse
    {
        $student = Student::where('renewal_token', $token)
            ->whereNotNull('renewal_notified_at')
            ->whereIn('status', Student::WAITLIST_STATUSES)
            ->where('user_id', Auth::id())
            ->first();

        if (! $student) {
            return redirect()->route('training.index')
                ->withError('Invalid link!');
        }

        if ($student->renewalWindowExpired()) {
            return redirect()->route('training.index')->withError('Your renewal period has expired and training cannot be renewed!');
        }

        return view('training.renew', compact('student', 'token'));
    }

    public function renewTraining(Request $request, string $token): RedirectResponse
    {
        return DB::transaction(function () use ($request, $token): RedirectResponse {
            $student = Student::where('renewal_token', $token)
                ->whereNotNull('renewal_notified_at')
                ->whereIn('status', Student::WAITLIST_STATUSES)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            if (! $student) {
                return redirect()->route('training.index')
                    ->withError('Invalid or already-used renewal link!');
            }

            if ($student->renewalWindowExpired()) {
                $student->update(['renewal_token' => null]);

                return redirect()->route('training.index')->withError('Your renewal period has expired and training cannot be renewed!');
            }

            $validated = $this->validateAvailability($request);
            $student->availability = $validated['availability'];
            $student->times = $validated['availability_comment'];
            $availabilityChanged = $student->isDirty(['availability', 'times']);

            $student->renewed_at = now();
            $student->renewal_token = null;
            $student->renewal_notified_at = null;
            $student->renewal_reminded_at = null;
            $student->save();
            $this->clearWaitlistContactAfterAvailabilityChange($student, $availabilityChanged);

            if ($student->user && filled($student->user->email)) {
                $student->user->notify(new RenewalSuccessfulNotification($student));
            }

            StudentNote::create([
                'student_id' => $student->id,
                'title' => 'Renewal Successful',
                'content' => sprintf(
                    'Student successfully renewed their training within the %d-day response window!',
                    RenewNotification::RENEWAL_GRACE_DAYS,
                ),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('training.index')->withSuccess('Your waitlist request has been renewed!');
        });
    }

    /**
     * @return array{availability: array<int, array{days: array<int, string>, start: string, end: string}>, availability_comment: ?string}
     */
    private function validateAvailability(Request $request): array
    {
        $validated = $request->validate([
            'availability' => ['required', 'array', 'min:1', 'max:21'],
            'availability.*.days' => ['required', 'array', 'min:1', 'max:7'],
            'availability.*.days.*' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'availability.*.start' => ['required', 'date_format:H:i'],
            'availability.*.end' => ['required', 'date_format:H:i'],
            'availability_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayOrder = array_flip($days);
        $slots = collect($validated['availability'])
            ->map(fn (array $slot): array => [
                'days' => collect($days)
                    ->filter(fn (string $day): bool => in_array($day, $slot['days'], true))
                    ->values()
                    ->all(),
                'start' => $slot['start'],
                'end' => $slot['end'],
            ]);

        foreach ($slots as $index => $slot) {
            if ($slot['end'] <= $slot['start']) {
                throw ValidationException::withMessages([
                    "availability.{$index}.end" => 'The end time must be later than the start time on the same day!',
                ]);
            }
        }

        if ($slots->unique(fn (array $slot): string => implode(',', $slot['days']).'|'.$slot['start'].'|'.$slot['end'])->count() !== $slots->count()) {
            throw ValidationException::withMessages([
                'availability' => 'The same availability period cannot be added twice!',
            ]);
        }

        return [
            'availability' => $slots
                ->sortBy(fn (array $slot): string => sprintf('%d-%s', $dayOrder[$slot['days'][0]], $slot['start']))
                ->values()
                ->all(),
            'availability_comment' => filled($validated['availability_comment'] ?? null)
                ? trim($validated['availability_comment'])
                : null,
        ];
    }

    private function clearWaitlistContactAfterAvailabilityChange(Student $student, bool $availabilityChanged): void
    {
        if (! $availabilityChanged || $student->latestWaitlistContactEvent()->value('title') !== 'Waitlist Contacted') {
            return;
        }

        StudentNote::create([
            'student_id' => $student->id,
            'title' => 'Waitlist Contact Cleared',
            'content' => '<u>'.e(Auth::user()->fullName('FLC')).'</u> unmarked as contacted',
            'created_at' => now(),
        ]);
    }

    public function assignInstructorToStudent(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'remove_instructor' => ['sometimes', 'boolean'],
            'instructor' => ['nullable', 'integer', Rule::exists('instructors', 'id')->where('active', true)],
        ]);
        $student = Student::with('user')->findOrFail($id);

        if ((bool) ($validated['remove_instructor'] ?? false)) {
            try {
                $this->vatcan->unassign((int) $student->user_id);
            } catch (Throwable $exception) {
                return $this->vatcanFailure($exception, (int) $student->user_id, 'unassign the instructor');
            }

            $student->update(['instructor_id' => null]);
            CreateNote::newNote($student->id, 'Removed Instructor', '<u>'.Auth::user()->fullName('FLC').'</u> removed instructor from student');

            return back()->withSuccess('Unassigned '.$student->user->fullName('FLC').' from Instructor!');
        }

        if (! empty($validated['instructor'])) {
            $instructor = Instructor::with('user')->where('active', true)->findOrFail($validated['instructor']);

            try {
                $this->vatcan->assign((int) $student->user_id, (int) $instructor->user_id, (int) Auth::id());
            } catch (Throwable $exception) {
                return $this->vatcanFailure($exception, (int) $student->user_id, 'assign the instructor');
            }

            $student->update(['instructor_id' => $instructor->id]);
            $student->setRelation('instructor', $instructor);
            $instructorText = Auth::id() === $student->instructor->user_id
                ? 'assigned themselves to student'
                : 'assigned instructor '.$student->instructor->user->fullName('FLC').' to student';
            CreateNote::newNote($student->id, 'Assigned Instructor', '<u>'.Auth::user()->fullName('FLC').'</u> '.$instructorText);

            return back()->withSuccess('Paired '.$student->user->fullName('FLC').' with Instructor '.$student->instructor->user->fullName('FLC').'!');
        }

        return back()->with('info', 'No instructor change was requested!');
    }

    public function removeStudent($id): RedirectResponse
    {
        $student = Student::findOrFail($id);

        if ($student->instructor_id) {
            try {
                $this->vatcan->unassign((int) $student->user_id);
            } catch (Throwable $exception) {
                return $this->vatcanFailure($exception, (int) $student->user_id, 'remove the student');
            }
        }

        $student->trainingNotes()->delete();
        $student->instructingSessions()->delete();
        $student->labels()->delete();
        $student->checklistItems()->delete();
        $student->delete();

        return redirect()->route('training.students.students')->withSuccess('Student removed successfully!');
    }

    /** The student themselves, an active instructor, or training managers */
    private function authorizeStudentAccess(Student $student): void
    {
        abort_if(
            $student->user_id !== Auth::id()
                && Auth::user()->activeInstructorProfile === null
                && ! Auth::user()->hasPermissionTo('training.manage'),
            403
        );
    }

    private function latestVatcanNotes(VatcanController $vatcanController, Student $student): array
    {
        return collect($vatcanController->getVatcanNotes($student->id))
            ->sortByDesc('friendly_time')
            ->values()
            ->all();
    }

    private function vatcanFailure(Throwable $exception, int $studentCid, string $action): RedirectResponse
    {
        Log::warning('VATCAN instructor synchronization failed!', [
            'student_cid' => $studentCid,
            'action' => $action,
            'message' => $exception->getMessage(),
        ]);

        // A 4xx means VATCAN refused the change (e.g. not a VATCAN instructor), so retrying won't help
        if ($exception->getCode() >= 400 && $exception->getCode() < 500) {
            return back()->withError('VATCAN refused to '.$action.'. Check that the instructor is a Vancouver instructor on VATCAN! No local changes were made!');
        }

        return back()->withError('VATCAN could not '.$action.'! No local changes were made! Please try again!');
    }
}
