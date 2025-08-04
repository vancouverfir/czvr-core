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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (! $user) {abort(403);}

        $student = Student::where('user_id', $user->id)->first();
        $instructor = Instructor::where('user_id', $user->id)->first();

        $labels = $student ? StudentLabel::cursor()->filter(fn ($label) => !StudentInteractiveLabels::where('student_id', $student->id)->where('student_label_id', $label->id)->exists()): collect();

        $yourStudents = $instructor ? Student::where('instructor_id', $instructor->id)->get() : null;

        $waitlistPosition = null;
        $studentChecklistGroups = null;

        $training_time = null;

        $Visitors = Student::where('status', 3)->count();

        if ($student) {
            $training_time = TrainingWaittime::find(1);

            $student->renewed_at;

            if (in_array($student->status, [0, 3])) {
                $waitlistIds = Student::where('status', $student->status)->orderBy('position')->pluck('id')->toArray();

                $index = array_search($student->id, $waitlistIds);
                $waitlistPosition = $index !== false ? $index + 1 : null;
            }

            $studentChecklistGroups = $student->checklistItems->groupBy(function ($item) {
                return $item->checklistItem->checklist->name;
            });
        }

        return view('training.indexinstructor', compact('yourStudents', 'student', 'Visitors', 'waitlistPosition', 'studentChecklistGroups', 'training_time'));
    }

    public function viewResources()
    {
        $atcResources = AtcResource::all()->sortBy('title');

        return view('training.resources', compact('atcResources'));
    }

    public function allNotes($id)
    {
        $student = Student::findOrFail($id);

        if ($student->user_id !== auth()->id() && auth()->user()->permissions < 2) {  abort(403); }

        return view('training.students.viewstudentnotes', compact('student'));
    }

    public function newNoteView($id)
    {
        $student = Student::findOrFail($id);

        return view('training.students.newnote', compact('student'));
    }

    public function addNote(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $instructor = Instructor::where('user_id', Auth::id())->first();

        if (!$instructor) {
            return redirect("/training/students/{$student->id}")->withError('Insufficient Permissions!');
        }

        StudentNote::create([
            'student_id' => $student->id,
            'author_id' => $instructor->id,
            'title' => $request->title,
            'content' => $request->content,
            'created_at' => now(),
        ]);

        return redirect("/training/students/{$student->id}")->withSuccess('Staff Comment added for ' . $student->user->fullName('FLC') . '!');
    }

    public function trainingTime()
    {
        $trainingTime = TrainingWaittime::find(1);
        $waitlist = Student::where('status', 0)->get();
        $visitorWaitlist = Student::where('status', 3)->get();

        return view('trainingtimes', compact('trainingTime', 'waitlist', 'visitorWaitlist'));
    }

    public function editTrainingTime(Request $request)
    {
        $request->validate(['waitTime' => 'required']);

        $trainingTime = TrainingWaittime::find(1);
        $trainingTime->update([
            'wait_length' => $request->waitTime,
            'colour' => $request->trainingTimeColour,
        ]);

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
        $userId = $request->student_id;

        if (Student::where('user_id', $userId)->exists()) {
            return back()->withError('This student already exists!');
        }

        $instructor = $request->instructor !== 'unassign' ? $request->instructor : null;

        $isVisitor = $request->is_visitor == 1;
        $visitorType = $request->visitor_type;

        $status = $isVisitor ? 3 : 0;

        $position = Student::where('status', $status)->max('position') + 1;

        $student = Student::create([
            'user_id' => $request->student_id,
            'instructor_id' => $instructor,
            'position' => $position,
            'status' => $status,
            'last_status_change' => now(),
            'created_at' => now(),
        ]);

        if (in_array($status, [0, 3])) {
            $student->position = Student::where('status', $status)->max('position') + 1;
            $student->save();
        }

        $labels = $isVisitor ? ['Visitor Waitlist', $visitorType === 'vatcan' ? 'Visitor VATCAN' : 'Visitor Non-VATCAN'] : ['Waitlist'];

        foreach ($labels as $labelName) {
            $labelId = StudentLabel::whereName($labelName)->first()?->id;
            if ($labelId) {
                StudentInteractiveLabels::create([
                    'student_label_id' => $labelId,
                    'student_id' => $student->id,
                ]);
            }
        }

        return redirect("/training/students/{$student->id}")->withSuccess('Added New Visitor/Student - ' . $student->user->fullName('FLC') . '!');
    }

    public function AllStudents()
    {
        $students = Student::all();
        $potentialstudent = User::all();
        $instructors = Instructor::all();

        $lists = StudentLabel::where('visible_home', true)->get();

        return view('training.students.allstudents', compact('students', 'potentialstudent', 'instructors', 'lists'));
    }

    public function completedStudents()
    {
        $students = Student::where('status', 2)->get();
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
        $student = Student::findOrFail($id);
        $instructors = Instructor::all();
        $checklists = Checklist::all();
        $times = $student->times;

        $studentChecklistGroups = $student->checklistItems->groupBy(function ($item) {
            return $item->checklistItem->checklist->name;
        });

        $labels = StudentLabel::cursor()->filter(fn ($label) => !StudentInteractiveLabels::where('student_id', $student->id)->where('student_label_id', $label->id)->exists());

        return view('training.students.viewstudent', compact('student', 'instructors', 'times', 'labels', 'checklists', 'studentChecklistGroups'));
    }

    public function sort(Request $request)
    {
        foreach ($request->order as $item) {
            Student::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    public function sortVisitor(Request $request)
    {
        foreach ($request->order as $item) {
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

        $student->labels()->where('student_label_id', 1)->delete();

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

            return redirect()->route('training.index')->withError('Your renewal period has expired and training cannot be renewed!');
        }

        $student->last_status_change = now();
        $student->renewal_token = null;
        $student->save();

        return redirect()->route('training.index')->withSuccess('Your training has been renewed!');
    }

    public function assignInstructorToStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        if ($request->filled('remove_instructor') && $request->remove_instructor == 1) {
            $student->update(['instructor_id' => null]);

            return back()->withSuccess("Unassigned {$student->user->fullName('FLC')} from Instructor.");
        }

        if ($request->filled('instructor')) {
            $student->update(['instructor_id' => $request->instructor]);

            return back()->withSuccess("Paired {$student->user->fullName('FLC')} with Instructor {$student->instructor->user->fullName('FLC')}.");
        }
    }

    public function showDeleteForm($id)
    {
        $student = Student::findOrFail($id);

        return view('training.students.removestudents', compact('student'));
    }

    public function removeStudent($id)
    {
        $student = Student::findOrFail($id);

        $student->trainingNotes()->delete();
        $student->solorequest()->delete();
        $student->instructingSessions()->delete();
        $student->labels()->delete();
        $student->checklistItems()->delete();
        $student->delete();

        return redirect()->route('training.students.students')->withSuccess('Student removed successfully!');
    }
}
