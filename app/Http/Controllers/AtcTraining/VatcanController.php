<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use App\Services\VatcanClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class VatcanController extends Controller
{
    public function getVatcanNotes(?int $studentId = null): array
    {
        $student = $studentId === null
            ? Auth::user()?->studentProfile
            : Student::findOrFail($studentId);

        abort_if($student === null, 404);
        abort_if(
            $student->user_id !== Auth::id()
                && ! Auth::user()?->hasPermissionTo('training.manage'),
            403
        );

        return Cache::get('vatcan_notes_'.$student->user_id, []);
    }

    public static function canEditNote(array $note): bool
    {
        return isset($note['id'], $note['created_at'])
            && (int) ($note['instructor_cid'] ?? 0) === Auth::id()
            && Carbon::parse($note['created_at'])->gt(now()->subDay());
    }

    public function storeNote(Request $request, Student $student, VatcanClient $vatcan): RedirectResponse
    {
        $validated = $this->validateNote($request);

        try {
            $vatcan->createNote((int) $student->user_id, (int) Auth::id(), $validated['position'], $validated['note'], (int) $validated['session_type']);
        } catch (Throwable $exception) {
            return $this->noteFailure($exception, $student, 'create');
        }

        $this->refreshNotes($vatcan, $student);

        return back()->withSuccess('Training note added to VATCAN!');
    }

    public function updateNote(Request $request, Student $student, int $note, VatcanClient $vatcan): RedirectResponse
    {
        $validated = $this->validateNote($request);
        $existing = collect(Cache::get('vatcan_notes_'.$student->user_id, []))->firstWhere('id', $note);

        if (! $existing || ! self::canEditNote($existing)) {
            return back()->withError('Only the instructor who wrote a VATCAN note can change it, within 24 hours!');
        }

        try {
            $vatcan->updateNote((int) $student->user_id, $note, (int) Auth::id(), $validated['position'], $validated['note'], (int) $validated['session_type']);
        } catch (Throwable $exception) {
            return $this->noteFailure($exception, $student, 'update');
        }

        $this->refreshNotes($vatcan, $student);

        return back()->withSuccess('Training note updated on VATCAN!');
    }

    /** @return array{position: string, session_type: int, note: string} */
    private function validateNote(Request $request): array
    {
        return $request->validate([
            'position' => ['required', 'string', 'max:10'],
            'session_type' => ['required', 'integer', 'between:0,'.(count(VatcanClient::SESSION_TYPES) - 1)],
            'note' => ['required', 'string', 'min:3', 'max:5000'],
        ]);
    }

    private function refreshNotes(VatcanClient $vatcan, Student $student): void
    {
        try {
            $vatcan->refreshNotes((int) $student->user_id);
        } catch (Throwable $exception) {
            Log::info('Could not refresh VATCAN notes after a change, the scheduled fetch will catch up!', [
                'student_cid' => $student->user_id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function noteFailure(Throwable $exception, Student $student, string $action): RedirectResponse
    {
        Log::warning('VATCAN training note '.$action.' failed!', [
            'student_cid' => $student->user_id,
            'message' => $exception->getMessage(),
        ]);

        return back()->withInput()->withError('VATCAN did not '.$action.' the training note! '.$exception->getMessage());
    }
}
