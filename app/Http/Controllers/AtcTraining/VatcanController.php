<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
}
