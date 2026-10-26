<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use App\Models\Publications\AtcResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AtcResourcesController extends Controller
{
    private const ICONS = [
        'fa-cloud',
        'fa-headset',
        'fa-plane-departure',
        'fa-book',
        'fa-map',
        'fa-comments',
        'fa-graduation-cap',
        'fa-exclamation-triangle',
        'fa-info-circle',
        'fa-video',
        'fa-file-alt',
    ];

    public function index(): View
    {
        $resources = AtcResource::query()->orderBy('title')->get();

        return view('training.manage-resources', compact('resources'));
    }

    public function uploadResource(Request $request): RedirectResponse
    {
        AtcResource::create($this->validatedResource($request));

        return redirect()->route('atcresources.index')->withSuccess('Resource added!');
    }

    public function updateResource(Request $request, AtcResource $resource): RedirectResponse
    {
        $resource->update($this->validatedResource($request));

        return back()->withSuccess('Resource updated!');
    }

    public function deleteResource($id): RedirectResponse
    {
        $resource = AtcResource::whereId($id)->firstOrFail();

        $resource->delete();

        return redirect()->back()->with('info', 'Resource deleted!');
    }

    /** @return array{title: string, font_awesome: string, description: string, url: string} */
    private function validatedResource(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'font_awesome' => ['required', 'string', Rule::in(self::ICONS)],
            'description' => ['required', 'string', 'max:5000'],
            'url' => ['required', 'url', 'max:2048'],
        ]);
    }
}
