<?php

namespace App\Http\Controllers;

use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\Events\ControllerApplication;
use App\Models\Events\Event;
use App\Models\Events\EventConfirm;
use App\Models\Publications\AtcResource;
use App\Models\Tickets\Ticket;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $student = Student::where('user_id', $user->id)->first();
        $rosterMember = RosterMember::where('user_id', $user->id)->first();

        $certification = $rosterMember?->status;
        $active = $rosterMember?->active;
        $profile = $rosterMember;

        $statusBadges = [
            'certified'     => ['success', 'fa-check', 'CZVR Certified'],
            'not_certified' => ['danger', 'fa-times', 'Not Certified to Control'],
            'training'      => ['warning', 'fa-book-open', 'In Training'],
            'home'          => ['info', 'fa-user-check', 'CZVR Controller', '#2E2F2F'],
            'visit'         => ['info', 'fa-plane', 'CZVR Visiting Controller'],
            'instructor'    => ['info', 'fa-chalkboard-teacher', 'CZVR Instructor'],
        ];

        $activeBadges = [
            0 => ['danger', 'fa-times', 'Inactive'],
            1 => ['success', 'fa-check', 'Active'],
        ];

        $status = $statusBadges[$certification] ?? ['dark', 'fa-question', 'Unknown'];
        $activeStatus = $activeBadges[$active] ?? ['dark', 'fa-question', 'Unknown'];

        $requiredHours = ($profile && in_array($profile->status, ['instructor', 'home']) && $profile->staff === 'exec') ? 5 : 3;

        $confirmedEvents = Event::all()->filter(fn ($e) => Carbon::now()->lt($e->end_timestamp))->sortBy('start_timestamp');

        $confirmedApp = EventConfirm::where('user_id', $user->id)->get()->sortBy('start_timestamp');

        $unconfirmedApp = ControllerApplication::where('user_id', $user->id)->get();

        $openTickets = Ticket::where('user_id', $user->id)->where('status', 0)->get();
        $staffTickets = Ticket::where('staff_member_cid', $user->id)->where('status', 0)->get();

        if ($user->permissions == 0) {
            return view('dashboard.index2', ['openTickets' => $openTickets, 'confirmedevent' => $confirmedEvents, ]);
        }

        return view('dashboard.index', ['user' => $user, 'yourinstructor' => $student, 'openTickets' => $openTickets, 'staffTickets' => $staffTickets, 'certification' => $certification, 'active' => $active, 'profile' => $profile, 'status' => $status, 'activeStatus' => $activeStatus, 'requiredHours' => $requiredHours, 'unconfirmedapp' => $unconfirmedApp, 'confirmedapp' => $confirmedApp, 'confirmedevent' => $confirmedEvents, ]);
    }

    public function postTweet()
    {
        return 'nothing';
    }
}
