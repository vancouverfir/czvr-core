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
    public function getVatcanNotes($studentId): array
    {
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
            $notes = $data['notes'] ?? [];

            usort($notes, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return $notes;
        } catch (RequestException $e) {
            \Log::error('Error fetching training notes from Vatcan API ' . $e->getMessage());
            return [];
        }
    }
}
