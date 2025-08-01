<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\InstructorStudents;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentNote;
use App\Models\AtcTraining\TrainingWaittime;
use App\Models\Publications\AtcResource;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $student = Student::where('user_id', $user->id)->first();
        $instructor = Instructor::where('user_id', $user->id)->first();
        $soloreq = SoloRequest::where('approved', '0')->get();

        $statusMap = [
            0 => ['label' => 'Waitlisted', 'class' => 'primary', 'showWaitlist' => true],
            1 => ['label' => 'In Progress', 'class' => 'success'],
            2 => ['label' => 'Completed', 'class' => 'primary'],
            3 => ['label' => 'Waitlisted', 'class' => 'primary', 'showWaitlist' => true],
            4 => ['label' => 'Inactive', 'class' => 'danger'],
            5 => ['label' => 'In Progress', 'class' => 'success'],
            'default' => ['label' => 'White', 'class' => 'white'],
        ];

        $statusInfo = $student ? ($statusMap[$student->status] ?? $statusMap['default']) : null;

        $labels = $student ? StudentLabel::cursor()->filter(function ($label) use ($student) {return ! StudentInteractiveLabels::where('student_id', $student->id)->where('student_label_id', $label->id)->exists();}) : collect();

        $yourStudents = $instructor ? Student::where('instructor_id', $instructor->id)->get() : null;

        $waitlistPosition = null;
        $studentChecklistGroups = null;

        $training_time = null;

        $Visitors = Student::where('status', 3)->count();

        if ($student) {
            $training_time = TrainingWaittime::where('id', 1)->first();

            $student->renewed_at;

            if (in_array($student->status, [0, 3])) {
                $status = $student->status;
                $waitlistIds = Student::where('status', $status)
                    ->orderBy('position')
                    ->pluck('id')
                    ->toArray();

                $index = array_search($student->id, $waitlistIds);
                $waitlistPosition = $index !== false ? $index + 1 : null;
            }

            $studentChecklistGroups = $student->checklistItems->groupBy(function ($item) {
                return $item->checklistItem->checklist->name;
            });
        }

        return view('training.indexinstructor', compact('yourStudents', 'soloreq', 'student', 'Visitors', 'waitlistPosition', 'studentChecklistGroups', 'training_time', 'statusInfo'));
    }

    public function viewResources(){
        $atcResources = AtcResource::all()->sortBy('title');

        return view('training.resources', compact('atcResources'));
    }

    public function allNotes($id)
    {
        $student = Student::findOrFail($id);

        return view('training.students.viewstudentnotes', compact('student'));
    }

    public function newNoteView($id)
    {
        $student = Student::where('id', $id)->firstorFail();

        return view('training.students.newnote', compact('student'));
    }

    public function addNote(Request $request, $id)
    {
        $student = Student::where('id', $id)->first();
        $instructor = Instructor::where('user_id', Auth::user()->id)->first();
        if ($student != null && $instructor != null) {
            $newnote = StudentNote::create([
                'student_id' => $student->id,
                'author_id' => $instructor->id,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ]);

            return redirect('/training/students/'.$student->id.'')->withSuccess('Staff Comment added for '.$student->user->fullName('FLC').'!');
        } else {
            return redirect('/training/students/'.$student->id.'')->withError('Insufficient Permissions!');
        }
    }

    public function trainingTime()
    {
        $training_time = TrainingWaittime::where('id', 1)->first();
        $waitlist = Student::where('status', '0')->get();
        $visitor_waitlist = Student::where('status', '3')->get();

        return view('trainingtimes', compact('training_time', 'waitlist', 'visitor_waitlist'));
    }

    public function editTrainingTime(Request $request)
    {
        request()->validate([
            'waitTime' => 'required',
        ]);

        $training_time = TrainingWaittime::where('id', 1)->first();
        $training_time->wait_length = $request->waitTime;
        $training_time->colour = $request->trainingTimeColour;
        $training_time->save();

        return back()->withSuccess('Waittime updated successfully!');
    }

    public function instructorsIndex()
    {
        $instructors = Instructor::all();
        $potentialinstructor = RosterMember::where('status', 'instructor')->get();

        return view('training.instructors.index', compact('instructors', 'potentialinstructor'));
    }

    public function addInstructor(Request $request)
    {
        Instructor::create([
            'user_id' => $request->input('cid'),
            'qualification' => $request->input('qualification'),
            'email' => $request->input('email'),
        ]);

        return redirect()->back()->withSuccess('Added '.$request->input('cid').' as an Instructor!');
    }

    public function newStudent(Request $request)
    {
        $check = Student::where('user_id', $request->input('student_id'))->first();
        if ($check != null) {
            return redirect()->back()->withError('This student already exists!');
        }

        $instructor = null;
        if ($request->input('instructor') != 'unassign') {
            $instructor = $request->input('instructor');
        }

        $isVisitor = $request->input('is_visitor') == '1';
        $visitorType = $request->input('visitor_type');

        $status = $isVisitor ? 3 : 0;

        $student = Student::create([
            'user_id' => $request->input('student_id'),
            'instructor_id' => $instructor,
            'status' => $status,
            'last_status_change' => Carbon::now()->toDateTimeString(),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        if (in_array($student->status, [0, 3])) {
            $lastPosition = Student::where('status', $student->status)->max('position') ?? 0;
            $student->position = $lastPosition + 1;
            $student->save();
        }

        if ($isVisitor) {
            $labelName = $visitorType === 'vatcan' ? 'Visitor VATCAN' : 'Visitor Non-VATCAN';
        } else {
            $labelName = 'Waitlist';
        }

        $labels = [];

        if ($isVisitor) {
            $labels[] = 'Visitor Waitlist';
            $labels[] = $visitorType === 'vatcan' ? 'Visitor VATCAN' : 'Visitor Non-VATCAN';
        } else {
            $labels[] = 'Waitlist';
        }

        foreach ($labels as $labelName) {
            $labelId = StudentLabel::whereName($labelName)->first()?->id;
            if ($labelId) {
                StudentInteractiveLabels::create([
                    'student_label_id' => $labelId,
                    'student_id' => $student->id,
                ]);
            }
        }

        return redirect('training/students/'.$student->id.'')->withSuccess('Added New Visitor/Student - '.$student->user->fullName('FLC').'!');
    }

    public function AllStudents()
    {
        $students = Student::all();
        $potentialstudent = User::all();
        $instructors = Instructor::all();
        $lists = StudentLabel::where('name', 'S1 Training')->orWhere('name', 'S2 Training')->orWhere('name', 'S3 Training')->orWhere('name', 'C1 Training')->orWhere('name', 'Visitor S3 Training')->orWhere('name', 'Visitor C1 Training')->orWhere('name', 'Inactive')->orWhere('name', 'Marked for Removal')->orderByRaw("FIELD(name, 'S1 Training', 'S2 Training', 'S3 Training', 'C1 Training', 'Visitor S3 Training', 'Visitor C1 Training', 'Inactive', 'Marked for Removal')")->get();

        return view('training.students.allstudents', compact('students', 'potentialstudent', 'instructors', 'lists'));
    }

    public function completedStudents()
    {
        $students = Student::where('status', '2')->get();
        $potentialstudent = User::all();
        $instructors = Instructor::all();

        return view('training.students.completed', compact('students', 'potentialstudent', 'instructors'));
    }

    public function newStudents()
    {
        $students = Student::where('status', '0')->get();

        $waitlistStudents = Student::where('status', 0)->orderBy('position')->get();
        $visitorWaitlist = Student::where('status', 3)->orderBy('position')->get();

        $potentialstudent = User::all();
        $instructors = Instructor::all();

        return view('training.students.waitlist', compact('waitlistStudents', 'visitorWaitlist', 'potentialstudent', 'instructors'));
    }

    public function viewStudent($id)
    {
        $student = Student::where('id', $id)->firstorFail();
        $instructors = Instructor::all();
        $modules2 = CbtModule::all();
        $modules = CbtModuleAssign::where('student_id', $student->id)->get();
        $times = $student->times;
        $exams = CbtExam::all();
        $openexams = CbtExamAssign::where('student_id', $student->id)->get();
        $completedexams = CbtExamResult::where('student_id', $student->id)->get();
        $solo = SoloRequest::where('student_id', $student->id)->get();
        $checklists = Checklist::all();

        $studentChecklistGroups = $student->checklistItems->groupBy(function ($item) {
            return $item->checklistItem->checklist->name;
        });

        $labels = StudentLabel::cursor()->filter(function ($l) use ($student) {
            if (StudentInteractiveLabels::where('student_id', $student->id)->where('student_label_id', $l->id)->first()) {
                return false;
            }

            return true;
        });

        return view('training.students.viewstudent', compact('modules2', 'solo', 'student', 'instructors', 'completedexams', 'times', 'exams', 'openexams', 'modules', 'labels', 'checklists', 'studentChecklistGroups'));
    }

    public function sort(Request $request)
    {
        foreach ($request->order as $item) {
            \App\Models\AtcTraining\Student::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    public function sortVisitor(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            Student::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['status' => 'success']);
    }

    public function editTimes(Request $request, Student $student)
    {
        $validated = $request->validate([
            'times' => 'nullable|string|max:1000',
        ]);

        $student->times = $validated['times'];
        $student->save();

        return redirect()->back()->with('success', 'Times updated successfully!');
    }

    public function renewTraining($token)
    {
        $student = Student::where('renewal_token', $token)->firstOrFail();

        $expirationDays = 11;

        if (
            $student->status == 4 ||
            ($student->renewed_at && $student->renewed_at->lte(now()->subDays($expirationDays)))
        ) {
            $student->renewal_token = null;
            $student->save();

            return redirect()->route('training.index')->with('error', 'Your renewal period has expired and training cannot be renewed!');
        }

        $student->last_status_change = now();
        $student->renewal_token = null;
        $student->save();

        return redirect()->route('training.index')->with('success', 'Your training has been renewed!');
    }

    public function assignInstructorToStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        if ($request->has('remove_instructor') && $request->input('remove_instructor') == 1) {
            $student->instructor_id = null;
            $student->save();

            return redirect()->back()->withSuccess('Unassigned '.$student->user->fullName('FLC').' from Instructor.');
        }

        if ($request->filled('instructor')) {
            $instructorId = $request->input('instructor');
            $student->instructor_id = $instructorId;
            $student->save();

            return redirect()->back()->withSuccess('Paired '.$student->user->fullName('FLC').' with Instructor '.$student->instructor->user->fullName('FLC').'.');
        }
    }

    public function assignStudent(Request $request)
    {
        $fullnameuser = User::findOrFail($request->input('student_id'));
        $fullnameinstructor = User::findorFail($request->input('instructor_id'));
        $assignstudent = InstructorStudents::create([
            'student_id' => $request->input('student_id'),
            'student_name' => $fullnameuser->fullName('FL'),
            'instructor_id' => $request->input('instructor_id'),
            'instructor_name' => $fullnameinstructor->fullName('FL'),
            'instructor_email' => User::where('id', $request->input('instructor_id'))->firstOrFail()->email,
            'assigned_by' => Auth::id(),
        ]);

        return redirect('/dashboard')->withSuccess('Successfully paired Student!');
    }

    public function showDeleteForm($id)
    {
        $student = Student::findOrFail($id);

        return view('training.students.removestudents', compact('student'));
    }

    public function removeStudent($id)
    {
        $student = Student::findOrFail($id);

        if ($student === null) {
            return redirect()->route('training.students.students')->withError('Student not found!');
        } else {
            $student->trainingNotes()->delete();
            $student->solorequest()->delete();
            $student->instructingSessions()->delete();
            $student->labels()->delete();
            $student->checklistItems()->delete();
            $student->delete();
        }

        return redirect()->route('training.students.students')->withSuccess('Student removed successfully!');
    }

    /*
    use App\Notifications\SoloApproval;
    use App\Models\AtcTraining\Application;
    use App\Models\AtcTraining\InstructingSession;
    use App\Models\AtcTraining\SoloRequest;
    use App\Models\AtcTraining\CBT\CbtExam;
    use App\Models\AtcTraining\CBT\CbtExamAnswer;
    use App\Models\AtcTraining\CBT\CbtExamAssign;
    use App\Models\AtcTraining\CBT\CbtExamQuestion;
    use App\Models\AtcTraining\CBT\CbtExamResult;
    use App\Models\AtcTraining\CBT\CbtModule;
    use App\Models\AtcTraining\CBT\CbtModuleAssign;
    use App\Models\AtcTraining\CBT\CbtNotification;

    public function changeStudentStatus(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstorFail();
        if ($student != null) {
            $student->status = $request->input('status');
            $student->save();
        }
        if ($student->status == '1') {
            $modules = CbtModule::all();
            foreach ($modules as $module) {
                if ($module->assignall == '1') {
                    $check = CbtModuleAssign::where([
                        ['cbt_module_id', $module->id],
                        ['student_id', $student->id],
                    ])->first();
                    if ($check == null) {
                        CbtModuleAssign::create([
                            'cbt_module_id' => $module->id,
                            'student_id' => $student->id,
                            'instructor_id' => $student->instructor->id,
                            'intro' => '1',
                            'created_at' => Carbon::now()->toDateTimeString(),
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->withSuccess('Sucessfully Changed The Status Of '.$student->user->fullName('FLC').'');
    }

    public function assignExam(Request $request)
    {
        $student = Student::find($request->input('studentid'));
        if (! $student) {
            return redirect()->back()->withError('Student cannot be found!');
        }
        $check = CbtExamResult::where([
            'student_id' => $request->input('studentid'),
            'cbt_exam_id' => $request->input('examid'),
        ])->first();
        if ($check != null) {
            $removeanswers = CbtExamAnswer::where([
                'student_id' => $student->id,
                'cbt_exam_id' => $request->input('examid'),
            ])->get();
            foreach ($removeanswers as $r) {
                $r->delete();
            }
            $removeresult = CbtExamResult::where([
                'student_id' => $student->id,
                'cbt_exam_id' => $request->input('examid'),
            ])->first();
            $removeresult->delete();
        }
        $questioncount = CbtExamQuestion::where('cbt_exam_id', $request->input('examid'))->get();
        if (count($questioncount) < 10) {
            return redirect()->back()->withError('This exam does not have the minimum 10 questions, so it cannot be assigned!');
        }

        $assign = CbtExamAssign::create([
            'student_id' => $student->id,
            'instructor_id' => $student->instructor_id,
            'cbt_exam_id' => $request->input('examid'),
        ]);
        CbtNotification::create([
            'student_id' => $student->id,
            'message' => 'You have been assigned the '.$assign->cbtexam->name.'',
            'dismissed' => '0',
        ]);

        return redirect()->back()->withSuccess('Assigned exam to student!');
    }



    public function unassignExam($id)
    {
        $exam = CbtExamAssign::whereId($id)->first();
        $exam->delete();

        return redirect()->back()->withSuccess('Unassigned exam sucessfully!');
    }


    public function assignModule(Request $request)
    {
        $student = Student::whereId($request->input('studentid'))->first();
        $check = CbtModuleAssign::where([
            ['cbt_module_id', $request->input('moduleid')],
            ['student_id', $student->id],
        ])->first();
        if ($check != null) {
            return redirect()->back()->withError('Student Already has this Module Assigned!');
        }
        if ($student->instructor == null) {
            $instructor = null;
        }
        if ($student->instructor != null) {
            $instructor = $student->instructor->id;
        }
        $module = CbtModuleAssign::create([
            'cbt_module_id' => $request->input('moduleid'),
            'student_id' => $student->id,
            'instructor_id' => $instructor,
            'intro' => '1',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtNotification::create([
            'student_id' => $student->id,
            'message' => 'You have been assigned the '.$module->cbtmodule->name.' Module!',
            'dismissed' => '0',
        ]);

        return redirect()->back()->withSuccess('Module assigned to student!');
    }

    public function ModuleUnassign($id)
    {
        $module = CbtModuleAssign::whereId($id)->first();
        $module->delete();

        return redirect()->back()->withSuccess('Unassigned module sucessfully!');
    }

    public function viewApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->firstOrFail();

        return view('training.applications.viewapplication', compact('application'));
    }

    public function acceptApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        if ($application != null) {
            $application->status = '2';
            $application->processed_by = Auth::id();
            $application->processed_at = Carbon::now()->toDateTimeString();
            $application->save();
            $newstudent = Student::create([
                'user_id' => $application->user_id,
                'status' => '0',
                'created_at' => Carbon::now()->toDateTimeString(),
                'accepted_application' => $application->id,
            ]);
        }

        return redirect()->back()->withInput()->withSuccess('You have accepted the application for '.$application->user->fullName('FLC').', they have been added as an On-Hold Student!');
    }

    public function denyApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        if ($application != null) {
            $application->status = '1';
            $application->processed_by = Auth::id();
            $application->processed_at = Carbon::now()->toDateTimeString();
            $application->save();
        }

        return redirect()->back()->withInput()->withError('You have DENIED the application for '.$application->user->fullName('FLC').'');
    }

    public function soloRequest(Request $request, $id)
    {
        $student = Student::whereId($id)->first();

        SoloRequest::create([
            'student_id' => $student->id,
            'instructor_id' => $student->instructor->id,
            'position' => $request->input('position'),
            'approved' => '0',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->back()->withSuccess('Solo request has been made!');
    }

    public function deleteStudent($id)
    {
        $student = InstructorStudents::where('student_id', $id)->first();
        if ($student === null) {
            return redirect('/dashboard')->withError('CID '.$id.' does not exist as a student!');
        } else {
            $student->delete();
        }

        return redirect('/dashboard')->withSuccess('Student/Instructor Pairing Removed!');
    }

    public function viewNote($id)
    {
        $note = StudentNote::where('id', $id)->firstorFail();

        return view('training.students.viewnote', compact('note'));
    }

    public function instructingSessionsIndex()
    {
        $sessions = InstructingSession::all();
        $upcomingSessions = InstructingSession::where('start_time', '>', now())->get();

        return view('training.instructingsessions.index', compact('sessions', 'upcomingSessions'));
    }

    public function createInstructingSession()
    {
        return view('training.instructingsessions.createsession');
    }

    public function viewInstructingSession()
    {
        return view('training.instructingsessions.create');
    }

    */
}
