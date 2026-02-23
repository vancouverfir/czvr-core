<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class VatcanController extends Controller
{
    public function getVatcanNotes($studentId): array
    {
        $student = Student::findOrFail($studentId);
        return Cache::get("vatcan_notes_{$student->user_id}", []);
    }
}
