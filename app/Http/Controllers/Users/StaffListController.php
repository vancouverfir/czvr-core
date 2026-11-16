<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\Settings\AuditLogEntry;
use App\Models\Tickets\Ticket;
use App\Models\Users\StaffGroup;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StaffListController extends Controller
{
    public function index(): View
    {
        $groups = StaffGroup::with(['members.user'])->get();

        // Instructor list
        $instructors_temp = Instructor::with('user')->where('active', true)->get();

        // Sort assessors to top of array
        $instructors = $instructors_temp->sortByDesc(fn ($i) => $i->qualification === 'Assessor')->values();

        $staff = StaffMember::with('user')->get();

        return view('staff.index', compact('staff', 'instructors', 'groups'));
    }

    public function editIndex(): View
    {
        $groups = StaffGroup::with(['members' => fn ($query) => $query->orderBy('id'), 'members.user'])->orderBy('id')->get();
        $staff = StaffMember::all();
        $users = User::all();

        return view('dashboard.staff.index', compact('groups', 'ticketCounts', 'staff', 'users'));
    }

    public function save(Request $request): RedirectResponse
    {
        $memberRules = [
            'cid' => ['required', 'integer', 'exists:users,id'],
            'position' => ['required', 'string', 'max:255'],
            'shortform' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
        $groupRules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'can_receive_tickets' => ['boolean'],
        ];

        $rules = ['groups' => ['array'], 'new_groups' => ['array'], 'members' => ['array'], 'new_members' => ['array']];
        foreach ($groupRules as $field => $rule) {
            $rules["groups.*.$field"] = $rule;
            $rules["new_groups.*.$field"] = $rule;
        }
        foreach ($memberRules as $field => $rule) {
            $rules["members.*.$field"] = $rule;
            $rules["new_members.*.$field"] = $rule;
        }
        $rules['groups.*.remove'] = ['boolean'];
        $rules['groups.*.delete_members'] = ['boolean'];
        $rules['members.*.remove'] = ['boolean'];
        $rules['members.*.group_id'] = ['required', 'integer', 'exists:staff_groups,id'];
        $rules['new_members.*.group'] = ['required', 'string'];

        $data = $request->validate($rules, [
            '*.*.cid.exists' => 'No user exists with CID :input!',
            '*.*.cid.required' => 'Every staff member needs a CID! Use CID 1 to mark a position vacant!',
            '*.*.position.required' => 'Every staff member needs a position title!',
            '*.*.shortform.required' => 'Every staff member needs a staff number (ZVR_)!',
            '*.*.name.required' => 'Every group needs a name!',
            '*.*.email.email' => ':input is not a valid email address!',
        ]);

        $changes = ['group created' => 0, 'group updated' => 0, 'group deleted' => 0, 'member added' => 0, 'member updated' => 0, 'member deleted' => 0];

        DB::transaction(function () use ($data, &$changes) {
            $problems = [];

            $newGroupIds = [];
            foreach ($data['new_groups'] ?? [] as $key => $groupData) {
                $group = StaffGroup::create([
                    'name' => $groupData['name'],
                    'slug' => $this->uniqueSlug($groupData['name']),
                    'description' => $groupData['description'] ?? null,
                    'can_receive_tickets' => (int) ($groupData['can_receive_tickets'] ?? 0),
                ]);
                $newGroupIds["new:$key"] = $group->id;
                $changes['group created']++;
            }

            $removedGroups = [];
            foreach ($data['groups'] ?? [] as $id => $groupData) {
                $group = StaffGroup::findOrFail($id);
                if ($groupData['remove'] ?? false) {
                    $removedGroups[] = [$group, (bool) ($groupData['delete_members'] ?? false)];

                    continue;
                }
                $group->fill([
                    'name' => $groupData['name'],
                    'description' => $groupData['description'] ?? null,
                    'can_receive_tickets' => (int) ($groupData['can_receive_tickets'] ?? 0),
                ]);
                if ($group->isDirty()) {
                    $group->save();
                    $changes['group updated']++;
                }
            }

            $groupNames = StaffGroup::pluck('name', 'id');

            foreach ($data['members'] ?? [] as $id => $memberData) {
                $member = StaffMember::findOrFail($id);
                if ($memberData['remove'] ?? false) {
                    $tickets = Ticket::where('staff_member_id', $member->id)->count();
                    if ($tickets > 0) {
                        $problems[] = "{$member->position} ({$member->shortform}) has {$tickets} ".Str::plural('ticket', $tickets).' and cannot be deleted! Assign the position to someone else, or to CID 1 to mark it vacant!';
                    } else {
                        $member->delete();
                        $changes['member deleted']++;
                    }

                    continue;
                }
                $member->fill($this->memberAttributes($memberData, (int) $memberData['group_id'], $groupNames));
                if ($member->isDirty()) {
                    $member->save();
                    $changes['member updated']++;
                }
            }

            foreach ($data['new_members'] ?? [] as $memberData) {
                $groupId = str_starts_with($memberData['group'], 'new:')
                    ? ($newGroupIds[$memberData['group']] ?? null)
                    : (int) $memberData['group'];
                if (! $groupId || ! $groupNames->has($groupId)) {
                    $problems[] = "The group for new position {$memberData['position']} no longer exists!";

                    continue;
                }
                StaffMember::create($this->memberAttributes($memberData, $groupId, $groupNames));
                $changes['member added']++;
            }

            foreach ($removedGroups as [$group, $deleteMembers]) {
                if ($deleteMembers) {
                    $blocked = false;
                    foreach ($group->members()->get() as $member) {
                        $tickets = Ticket::where('staff_member_id', $member->id)->count();
                        if ($tickets > 0) {
                            $problems[] = "The {$group->name} group cannot be deleted because {$member->position} ({$member->shortform}) has {$tickets} ".Str::plural('ticket', $tickets).'! Move them to another group first!';
                            $blocked = true;

                            continue;
                        }
                        $member->delete();
                        $changes['member deleted']++;
                    }
                    if ($blocked) {
                        continue;
                    }
                }

                $remaining = $group->members()->count();
                if ($remaining > 0) {
                    $problems[] = "The {$group->name} group still has {$remaining} ".Str::plural('member', $remaining).'! Move or delete them before deleting the group!';

                    continue;
                }
                $group->delete();
                $changes['group deleted']++;
            }

            if ($problems) {
                throw ValidationException::withMessages(['staff' => $problems]);
            }
        });

        $summary = collect($changes)->filter()->map(function ($count, $label) {
            [$noun, $verb] = explode(' ', $label);

            return $count.' '.Str::plural($noun, $count).' '.$verb;
        })->implode(', ');
        if ($summary === '') {
            return redirect()->route('settings.staff')->with('info', 'Nothing to save, no changes were made!');
        }

        AuditLogEntry::insert(Auth::user(), 'Updated staff team: '.$summary, Auth::user(), 0);

        return redirect()->route('settings.staff')->with('success', 'Staff team saved! '.ucfirst($summary).'!');
    }

    private function memberAttributes(array $memberData, int $groupId, $groupNames): array
    {
        return [
            'user_id' => (int) $memberData['cid'],
            'position' => $memberData['position'],
            'shortform' => $memberData['shortform'],
            'group_id' => $groupId,
            'group' => $groupNames[$groupId],
            'email' => filled($memberData['email'] ?? null) ? $memberData['email'] : null,
            'description' => filled($memberData['description'] ?? null) ? $memberData['description'] : null,
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'group';
        $slug = $base;
        $suffix = 2;
        while (StaffGroup::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
