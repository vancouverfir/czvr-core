<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Users\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function showPublic()
    {
        $roster = RosterMember::with('user')->where('visit', '0')->get()->sortBy('cid');
        $visitroster = RosterMember::with('user')->where('visit', '1')->get()->sortBy('cid');

        return view('roster', compact('roster', 'visitroster'));
    }

    public function index()
    {
        $roster = RosterMember::with('user')->where('visit', '0')->get()->sortBy('cid');
        $visitroster2 = RosterMember::with('user')->where('visit', '1')->get()->sortBy('cid');
        $users = User::all();

        return view('dashboard.roster.index', compact('roster', 'visitroster2', 'users'));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deleteController($id)
    {
        $user = User::findorFail($roster->user_id);
        $roster = RosterMember::findorFail($id);
        $session = SessionLog::where('roster_member_id', $id)->get();

        if ($user) {
            $user->permissions = '0';
            $user->save();
        }

        foreach ($session as $s) {
            $s->delete();
        }
        $roster->delete();

        return redirect('/dashboard/roster')->withSuccess('Successfully deleted from roster!');
    }

    public function addController(Request $request)
    {
        $users = User::findOrFail($request->input('newcontroller'));
        $rosterMember = RosterMember::where('cid', $users->id)->first();
        if ($rosterMember == null) {
            RosterMember::create([
                'cid' => $users->id,
                'user_id' => $users->id,
                'full_name' => $users->fullName('FL'),
                'status' => 'home',
                'visit' => '0',
            ]);
            $users->permissions = '1';
            $users->save();
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function addVisitController(Request $request)
    {
        $users = User::findOrFail($request->input('newcontroller'));
        $rosterMember = RosterMember::where('cid', $users->id)->first();

        if ($rosterMember == null) {
            RosterMember::create([
                'cid' => $users->id,
                'user_id' => $users->id,
                'full_name' => $users->fullName('FL'),
                'status' => 'visit',
                'visit' => 1,
            ]);
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function editControllerForm($cid)
    {
        $roster = RosterMember::where('cid', $cid)->first();

        if (! $roster) {
            abort(404);
        }

        return view('dashboard.roster.edituser', compact('roster'))->with('cid', $cid);
    }

    public function editController(Request $request, $cid)
    {
        $roster = RosterMember::where('cid', $cid)->first();
        if ($roster != null) {
            $roster->delgnd = $request->input('delgnd');
            $roster->delgnd_t2 = $request->input('delgnd_t2');
            $roster->twr = $request->input('twr');
            $roster->twr_t2 = $request->input('twr_t2');
            $roster->dep = $request->input('dep');
            $roster->app = $request->input('app');
            $roster->app_t2 = $request->input('app_t2');
            $roster->ctr = $request->input('ctr');
            $roster->fss = $request->input('fss');
            $roster->remarks = $request->input('remarks');
            if ($request->input('rating_hours') == 'true') {
                $roster->rating_hours = 0;
            }
            $roster->active = $request->input('active');
            $roster->save();
        }

        return redirect('/dashboard/roster')->withSuccess('Successfully edited!');
    }
}
