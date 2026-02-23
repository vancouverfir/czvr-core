<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Mail\InstructingSession as InstructingSessionMail;
use App\Models\AtcTraining\InstructingSession;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InstructingSessionsController extends Controller
{
    public function index(): View
    {
        $upcomingSessions = InstructingSession::where('end_time', '>=', now())->get();

        InstructingSession::where('end_time', '<', now())->delete();

        return view('training.instructingsessions.index', compact('upcomingSessions'));
    }

    public function show($id): View
    {
        $session = InstructingSession::findOrFail($id);

        return view('training.instructingsessions.view', compact('session'));
    }

    public function createForm(): View
    {
        return view('training.instructingsessions.new', $this->getStudentsAndInstructors());
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $data['instructor_id'] = Instructor::where('user_id', Auth::id())->value('id') ?? Auth::id();

        $session = InstructingSession::create($data);

        $this->sendSessionMail($session, 'created');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session created and notification sent!');
    }

    public function edit(InstructingSession $session): View
    {
        return view('training.instructingsessions.edit', array_merge(['session' => $session], $this->getStudentsAndInstructors()));
    }

    public function cancel(InstructingSession $session): RedirectResponse
    {
        $session->delete();
        $this->sendSessionMail($session, 'cancelled');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session cancelled and notification sent!');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['instructor_id'] = Instructor::where('user_id', Auth::id())->value('id') ?? Auth::id();
        $session = InstructingSession::create($data);

        $this->sendSessionMail($session, 'created');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session created and notification sent!');
    }

    public function update(Request $request, InstructingSession $session): RedirectResponse
    {
        $data = $this->validateData($request);

        $session->update($data);

        $session->refresh();

        $this->sendSessionMail($session, 'updated');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session updated and notification sent!');
    }

    private function getStudentsAndInstructors()
    {
        $students = Student::with('user')->get();
        $instructors = Instructor::with('user')->get();

        return compact('students', 'instructors');
    }

    private function sendSessionMail(InstructingSession $session, string $type)
    {
        Mail::to($session->instructorUser()->email)->send(new InstructingSessionMail($session, $type, 'instructor'));
        Mail::to($session->student->user->email)->send(new InstructingSessionMail($session, $type, 'student'));
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'instructor_id' => 'required|integer',
            'student_id' => 'required|integer',
            'title' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'instructor_comment' => 'nullable|string',
        ]);
    }
}
