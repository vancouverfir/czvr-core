<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class VatcanController extends Controller
{
    public function getVatcanNotes(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = Student::findOrFail($studentId);

        $apiKey = env('VATCAN_API_KEY');
        $userId = $student->user_id;
        $client = new Client();

        try {
            $response = $client->get("https://vatcan.ca/api/v2/user/{$userId}/notes", [
                'query' => [
                    'api_key' => $apiKey,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            usort($data['notes'], function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return response()->json($data['notes'] ?? []);
        } catch (RequestException $e) {
            \Log::error('Error fetching training notes from Vatcan API '.$e->getMessage());

            return response()->json([], 500);
        }
    }

    /*    public function newVatcan(Request $request, $studentId)
        {
            $request->validate([
                'position' => 'required|string',
                'note_content' => 'required|string',
                'session_type' => 'required|in:0,1,2,3',
                'ots_file' => 'nullable|file',
                'visiting_controller_note' => 'nullable|boolean',
                'ots_passed' => 'nullable|boolean',
                'backdate_training_note' => 'nullable|boolean',
                'backdate_date' => 'nullable|date',
            ]);

            $student = Student::findOrFail($studentId);
            $apiKey = env('VATCAN_API_KEY');
            $client = new Client();

            $data = [
                'cid' => $student->user_id,
                'instructor_cid' => auth()->user()->id,
                'position' => $request->position,
                'note' => $request->note_content,
                'visiting_controller_note' => $request->boolean('visiting_controller_note'),
                'session_type' => (int) $request->session_type,
                'backdate_training_note' => $request->has('backdate_training_note') ? 1 : 0,
                'ots_passed' => $request->has('ots_passed') ? 1 : 0,
            ];

            if ((int)$request->session_type === 2 && $request->hasFile('ots_file')) {
            }

            if ($request->has('visiting_controller_note') && isset(auth()->user()->fir->id)) {
                $data['facility_id'] = auth()->user()->fir->id;
            }

            if ($request->has('backdate_training_note') && $request->filled('backdate_date')) {
                $data['created_at'] = Carbon::parse($request->input('backdate_date'))->toIso8601String();
            }

            try {
                $response = $client->post("https://vatcan.ca/api/v2/user/{$student->user_id}/notes/create", [
                    'form_params' => array_merge($data, ['api_key' => $apiKey]),
                ]);

                $responseBody = $response->getBody()->getContents();
                \Log::info('Vatcan API success response', ['response' => $responseBody, 'request_data' => $data]);

                return redirect()->route('training.students.view', $studentId)->with('success', 'Training note created successfully!');

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                if (isset($responseData['hint']['instructor_cid']) &&
                    str_contains($responseData['hint']['instructor_cid'][0], 'invalid')) {
                    return back()->withErrors('You are not an authorized Vatcan Instructor!');
                }

                \Log::error('Vatcan create note general error', [
                    'error_message' => $e->getMessage(),
                    'request_data' => $data,
                ]);

                return back()->withErrors('Failed to create training note!');
            }
        }
    */

    public function createVatcan($studentId)
    {
        $student = Student::findOrFail($studentId);

        return view('training.createvatcan', compact('student'));
    }
}
