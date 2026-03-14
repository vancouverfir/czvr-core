<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\Users\StaffGroup;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffListController extends Controller
{
    public function index(): View
    {
        $groups = StaffGroup::with(['members.user'])->get();

        // Instructor list
        $instructors_temp = Instructor::with('user')->get();

        // Sort assessors to top of array
        $instructors = $instructors_temp->sortByDesc(fn ($i) => $i->qualification === 'Assessor')->values();

        $staff = StaffMember::with('user')->get();

        return view('staff', compact('staff', 'instructors', 'groups'));
    }

    public function editIndex(): View
    {
        $staff = StaffMember::all();
        $users = User::all();
        $groups = StaffGroup::all();

        return view('dashboard.staff.index', compact('staff', 'users', 'groups'));
    }

    public function addStaffMember(Request $request): RedirectResponse
    {
        $request->validate([
            'position' => 'required',
            'shortform' => 'required',
            'group' => 'required',
        ]);
        $user = User::whereId($request->get('newstaff'))->first();
        $addstaff = StaffMember::create([
            'user_id' => $request->input('newstaff'),
            'position' => $request->input('position'),
            'shortform' => $request->input('shortform'),
            'group_id' => $request->input('group'),
            'group' => 'Staff',
            'description' => 'Create Description',
            'email' => 'user@user.com',
        ]);

        return redirect()->back()->with('success', 'Staff member '.$addstaff->shortform.' created!');
    }

    public function editStaffMember(Request $request, $id): RedirectResponse
    {
        // Grab staff object
        $staff = StaffMember::whereId($id)->firstOrFail();

        // Check user given is a user

        $user = User::whereId($request->input('cid'))->first();

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'CID for staff member '.$staff->shortform.' invalid!');
        }

        // Ok we have a user.. assign them!
        $staff->user_id = $user->id;

        // Update description and email
        $staff->description = $request->get('description');
        $staff->email = $request->get('email');

        // Save it
        $staff->save();

        // Return!
        return redirect()->back()->with('success', 'Staff member '.$staff->position.' saved!');
    }

    public function deleteStaffMember(Request $request, $id): RedirectResponse
    {
        // Grab staff object
        $staff = StaffMember::whereId($id)->firstOrFail();

        // Delete it
        $staff->delete();

        // Return!
        return redirect()->back()->with('success', 'Staff member '.$staff->position.' deleted!');
    }
}
