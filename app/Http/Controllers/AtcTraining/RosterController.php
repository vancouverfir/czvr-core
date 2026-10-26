<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Mail\RosterStatusMail;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\VatcanClient;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class RosterController extends Controller
{
    public function showPublic(Request $request): View
    {
        $canManage = (bool) $request->user()?->can('roster.manage');

        // Members set as ATC inactive and the user picker are management-only data, so they are never loaded for other visitors
        $members = RosterMember::with('user')
            ->when(! $canManage, fn ($query) => $query->where('active', 1))
            ->get()
            ->sortBy('cid');

        $roster = $members->where('visit', 0);
        $visitroster = $members->where('visit', 1);
        $users = $canManage ? User::query()->orderBy('id')->get(['id', 'fname', 'lname']) : collect();

        return view('roster.index', compact('roster', 'visitroster', 'users', 'canManage'));
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('roster.public');
    }

    public function viewController($id): View
    {
        $roster = RosterMember::query()
            ->where('cid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return view('dashboard.roster.edituser', compact('roster'))->with('cid', $roster->cid);
    }

    public function deleteController($id): RedirectResponse
    {
        $roster = RosterMember::findOrFail($id);
        $user = User::findOrFail($roster->user_id);
        $controllerRole = Role::where('slug', SystemRoleCatalog::CONTROLLER)->firstOrFail();

        SessionLog::where('roster_member_id', $roster->id)->delete();
        $roster->delete();
        $user->removeRole($controllerRole);

        return redirect()->route('roster.public')->withSuccess('Successfully deleted from roster!');
    }

    public function addController(Request $request): RedirectResponse
    {
        $validated = $request->validate(['newcontroller' => ['required', 'integer', 'exists:users,id']]);
        $users = User::findOrFail($validated['newcontroller']);
        $rosterMember = RosterMember::where('cid', $users->id)->first();
        if ($rosterMember == null) {
            $controllerRole = Role::where('slug', SystemRoleCatalog::CONTROLLER)->firstOrFail();

            DB::transaction(function () use ($users, $controllerRole): void {
                RosterMember::create([
                    'cid' => $users->id,
                    'user_id' => $users->id,
                    'full_name' => $users->fullName('FL'),
                    'status' => 'home',
                    'visit' => '0',
                ]);
                $users->assignRole($controllerRole);
            });
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect()->route('roster.public')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function addVisitController(Request $request): RedirectResponse
    {
        $validated = $request->validate(['newcontroller' => ['required', 'integer', 'exists:users,id']]);
        $users = User::findOrFail($validated['newcontroller']);
        $rosterMember = RosterMember::where('cid', $users->id)->first();

        if ($rosterMember == null) {
            $controllerRole = Role::where('slug', SystemRoleCatalog::CONTROLLER)->firstOrFail();

            DB::transaction(function () use ($users, $controllerRole): void {
                RosterMember::create([
                    'cid' => $users->id,
                    'user_id' => $users->id,
                    'full_name' => $users->fullName('FL'),
                    'status' => 'visit',
                    'visit' => 1,
                ]);
                $users->assignRole($controllerRole);
            });
        } else {
            return redirect()->back()->withErrors('Member: '.$users->fullName('FL').' CID: '.$users->id.' is already on the roster!');
        }

        return redirect()->route('roster.public')->withSuccess('Successfully added '.$users->fullName('FL').' CID: '.$users->id.' to roster!');
    }

    public function editControllerForm($cid): View
    {
        $roster = RosterMember::where('cid', $cid)->first();

        if (! $roster) {
            abort(404);
        }

        return view('dashboard.roster.edituser', compact('roster'))->with('cid', $cid);
    }

    public function editController(Request $request, $cid, VatcanClient $vatcan): RedirectResponse
    {
        $roster = RosterMember::where('cid', $cid)->firstOrFail();
        $validated = $request->validate([
            'fss' => ['required', 'integer', 'in:0,1,2,3'],
            'delgnd' => ['required', 'integer', 'in:0,1,3'],
            'delgnd_t2' => ['required', 'integer', 'in:0,1,3'],
            'twr' => ['required', 'integer', 'in:0,1,2,3'],
            'twr_t2' => ['required', 'integer', 'in:0,1,2,3'],
            'dep' => ['required', 'integer', 'in:0,1,3'],
            'app' => ['required', 'integer', 'in:0,1,2,3'],
            'app_t2' => ['required', 'integer', 'in:0,1,2,3'],
            'ctr' => ['required', 'integer', 'in:0,1,2,3'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'active' => ['required', 'boolean'],
            'rating_hours' => ['required', 'in:true,false'],
        ]);

        $oldTier2 = $roster->tier2EndorsementLevel();

        foreach (['fss', 'delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr'] as $field) {
            $roster->{$field} = $validated[$field];
        }

        $newTier2 = $roster->tier2EndorsementLevel();

        if ($oldTier2 !== $newTier2) {
            try {
                $this->syncTier2Endorsement($vatcan, $roster, $oldTier2, $newTier2);
            } catch (Throwable $exception) {
                Log::warning('VATCAN Tier 2 endorsement update failed!', [
                    'cid' => $roster->cid,
                    'from' => $oldTier2,
                    'to' => $newTier2,
                    'message' => $exception->getMessage(),
                ]);

                return back()->withInput()->withError('VATCAN did not update the Tier 2 endorsement, so nothing was saved! '.$exception->getMessage());
            }
        }

        $wasActive = (int) $roster->active === 1;

        $roster->remarks = $validated['remarks'] ?? null;
        $roster->active = $validated['active'];

        if ($validated['rating_hours'] === 'true' && $roster->canResetRatingHours()) {
            $roster->rating_hours = 0;
        }

        $roster->save();

        // Only a change from active to inactive notifies the controller; a mail failure must not undo the saved edit
        if ($wasActive && (int) $roster->active === 0 && $roster->user?->email) {
            rescue(fn () => Mail::to($roster->user->email)->queue(new RosterStatusMail($roster, $roster->user)));
        }

        return redirect()->route('roster.public')->withSuccess('Successfully edited!');
    }

    private function syncTier2Endorsement(VatcanClient $vatcan, RosterMember $roster, ?string $old, ?string $new): void
    {
        $cid = (int) $roster->cid;

        $vatcan->removeEndorsement($cid, VatcanClient::TIER_2_ENDORSEMENT);

        if ($new === null) {
            return;
        }

        try {
            $vatcan->addEndorsement($cid, VatcanClient::TIER_2_ENDORSEMENT, $new, (int) auth()->id());
        } catch (Throwable $exception) {
            if ($old !== null) {
                rescue(fn () => $vatcan->addEndorsement($cid, VatcanClient::TIER_2_ENDORSEMENT, $old, (int) auth()->id()));
            }

            throw $exception;
        }
    }
}
