<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\SessionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NetworkController extends Controller
{
    public function index(Request $request): View
    {
        $input = $request->validate([
            'tab' => ['nullable', Rule::in(['activity', 'statistics', 'positions'])],
            'status' => ['nullable', Rule::in(['all', 'met', 'below'])],
            'period' => ['nullable', Rule::in(['30', '90', '365', 'all'])],
        ]);
        $tab = $input['tab'] ?? 'activity';
        $filter = $input['status'] ?? 'all';
        $period = $input['period'] ?? '90';

        $activity = $this->controllerActivity();
        $members = $activity
            ->when($filter === 'met', fn ($members) => $members->where('requirement_met', true))
            ->when($filter === 'below', fn ($members) => $members->where('requirement_met', false));

        $summary = [
            'online' => SessionLog::query()->whereNull('session_end')->count(),
            'controllers' => $activity->count(),
            'below' => $activity->where('requirement_met', false)->count(),
            'positions' => MonitoredPosition::query()->count(),
        ];

        $positions = MonitoredPosition::query()
            ->with('latestSession')
            ->withCount('sessions')
            ->orderBy('identifier')
            ->get();

        return view('dashboard.network.index', [
            'tab' => $tab,
            'filter' => $filter,
            'period' => $period,
            'summary' => $summary,
            'members' => $members,
            'positions' => $positions,
            ...$this->statistics($period),
        ]);
    }

    private function controllerActivity(): Collection
    {
        $requirements = config('currency', []);

        return RosterMember::query()
            ->with('user')
            ->withMax('sessionLogs as last_session_at', 'session_end')
            ->where('active', 1)
            ->whereIn('status', array_keys($requirements))
            ->orderBy('full_name')
            ->get()
            ->map(function (RosterMember $member) use ($requirements) {
                $member->required_hours = (float) ($requirements[$member->status] ?? 0);
                $member->recorded_hours = (float) ($member->currency ?? 0);
                $member->requirement_met = $member->recorded_hours >= $member->required_hours;

                return $member;
            });
    }

    private function statistics(string $period): array
    {
        $sessions = SessionLog::query()->whereNotNull('session_end');
        if ($period !== 'all') {
            $sessions->where('session_start', '>=', now()->subDays((int) $period));
        }

        return [
            'statistics' => [
                'sessions' => (clone $sessions)->count(),
                'hours' => (float) (clone $sessions)->sum('duration'),
                'controllers' => (clone $sessions)->distinct()->count('cid'),
                'average' => (float) ((clone $sessions)->avg('duration') ?? 0),
            ],
            'busiestPositions' => (clone $sessions)
                ->selectRaw('monitored_position_id, COUNT(*) as session_count, SUM(COALESCE(duration, 0)) as total_hours')
                ->with('monitoredPosition')
                ->groupBy('monitored_position_id')
                ->orderByDesc('total_hours')
                ->limit(10)
                ->get(),
            'busiestControllers' => (clone $sessions)
                ->selectRaw('cid, COUNT(*) as session_count, SUM(COALESCE(duration, 0)) as total_hours')
                ->with('user')
                ->groupBy('cid')
                ->orderByDesc('total_hours')
                ->limit(10)
                ->get(),
            'availableFrom' => SessionLog::query()->min('session_start'),
        ];
    }

    public function viewMonitoredPosition(string $position): View
    {
        $position = MonitoredPosition::query()
            ->whereRaw('LOWER(identifier) = ?', [strtolower($position)])
            ->firstOrFail();
        $sessions = $position->sessions()
            ->with('user')
            ->latest('session_start')
            ->paginate(25);

        return view('dashboard.network.monitoredpositions.view', compact('position', 'sessions'));
    }

    public function createMonitoredPosition(Request $request): RedirectResponse
    {
        $data = $this->validatePosition($request);
        $position = MonitoredPosition::query()->create($data);

        return redirect()
            ->route('network.monitoredpositions.view', strtolower($position->identifier))
            ->withSuccess($position->identifier.' is now being monitored!');
    }

    public function updateMonitoredPosition(Request $request, MonitoredPosition $position): RedirectResponse
    {
        $position->update($this->validatePosition($request, $position));

        return redirect()
            ->route('network.monitoredpositions.view', strtolower($position->identifier))
            ->withSuccess('The monitored position was updated.');
    }

    public function deleteMonitoredPosition(MonitoredPosition $position): RedirectResponse
    {
        if ($position->sessions()->exists()) {
            return back()->with('error', 'This position has session history and cannot be deleted!');
        }

        $identifier = $position->identifier;
        $position->delete();

        return redirect()->route('network.index', ['tab' => 'positions'])->withSuccess($identifier.' was removed!');
    }

    public function downloadMonitoredPositions(): JsonResponse
    {
        return response()->json([
            'positions' => MonitoredPosition::query()->orderBy('identifier')->pluck('identifier'),
        ], headers: [
            'Content-Disposition' => 'attachment; filename="monitored-positions.json"',
        ]);
    }

    /** @return array{identifier: string} */
    private function validatePosition(Request $request, ?MonitoredPosition $position = null): array
    {
        $request->merge(['identifier' => strtoupper(trim((string) $request->input('identifier')))]);

        return $request->validateWithBag('createMonitoredPosition', [
            'identifier' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_*]+$/',
                Rule::unique('monitored_positions', 'identifier')->ignore($position?->id),
            ],
        ], [
            'identifier.regex' => 'The callsign may only contain letters, numbers, underscores, and an asterisk!',
        ]);
    }
}
