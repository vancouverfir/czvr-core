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
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class InstructingSessionsController extends Controller
{
    public function index(): View
    {
        $upcomingSessions = InstructingSession::with(['student.user', 'instructor.user'])
            ->where('end_time', '>=', now())
            ->orderBy('start_time')
            ->get();
        $students = Student::with('user.rosterProfile')->orderBy('user_id')->get();

        return view('training.instructingsessions.index', compact('upcomingSessions', 'students'));
    }

    public function show(InstructingSession $session): RedirectResponse
    {
        return redirect()->route('training.instructingsessions.index', ['session' => $session->id]);
    }

    public function createForm(): RedirectResponse
    {
        return redirect()->route('training.instructingsessions.index', ['action' => 'new']);
    }

    public function create(Request $request): RedirectResponse
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('training.instructingsessions.index', ['action' => 'new'])
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($response = $this->consolidationResponse((int) $data['student_id'], ['action' => 'new'])) {
            return $response;
        }

        $data['instructor_id'] = Instructor::where('user_id', Auth::id())->where('active', true)->value('id') ?? Auth::id();

        $session = InstructingSession::create($data);

        $this->sendSessionMail($session, 'created');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session created and notification sent!');
    }

    public function edit(InstructingSession $session): RedirectResponse
    {
        return redirect()->route('training.instructingsessions.index', [
            'session' => $session->id,
            'action' => 'edit',
        ]);
    }

    public function cancel(InstructingSession $session): RedirectResponse
    {
        $session->delete();
        $this->sendSessionMail($session, 'cancelled');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session cancelled and notification sent!');
    }

    public function update(Request $request, InstructingSession $session): RedirectResponse
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('training.instructingsessions.index', [
                    'session' => $session->id,
                    'action' => 'edit',
                ])
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($response = $this->consolidationResponse((int) $data['student_id'], [
            'session' => $session->id,
            'action' => 'edit',
        ])) {
            return $response;
        }

        $session->update($data);

        $session->refresh();

        $this->sendSessionMail($session, 'updated');

        return redirect()->route('training.instructingsessions.index')->with('success', 'Session updated and notification sent!');
    }

    private function sendSessionMail(InstructingSession $session, string $type)
    {
        Mail::to($session->instructorUser()->email)->send(new InstructingSessionMail($session, $type, 'instructor'));
        Mail::to($session->student->user->email)->send(new InstructingSessionMail($session, $type, 'student'));
    }

    private function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'instructor_comment' => 'nullable|string|max:5000',
        ]);
    }

    private function consolidationResponse(int $studentId, array $routeParameters): ?RedirectResponse
    {
        $student = Student::with('user.rosterProfile')->findOrFail($studentId);

        if (! $student->isConsolidating()) {
            return null;
        }

        return redirect()
            ->route('training.instructingsessions.index', $routeParameters)
            ->withInput()
            ->withErrors([
                'student_id' => sprintf(
                    '%s must log %.1f more hours at %s before training can be scheduled!',
                    $student->user->fullName('FLC'),
                    $student->consolidationRemainingHours(),
                    $student->consolidation_rating,
                ),
            ]);
    }
}
