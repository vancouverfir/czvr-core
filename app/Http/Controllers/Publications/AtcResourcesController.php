<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use App\Models\Publications\AtcResource;
use Illuminate\Http\Request;

class AtcResourcesController extends Controller
{
    public function index()
    {
        $resources = AtcResource::all();

        return view('atcresources', compact('resources'));
    }

    public function uploadResource(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'font_awesome' => 'required|string',
            'description' => 'required',
            'url' => 'required|url',
            'atc_only' => 'required|boolean',
        ]);

        $resource = new AtcResource();
        $resource->title = $request->get('title');
        $resource->font_awesome = $request->get('font_awesome');
        $resource->description = $request->get('description');
        $resource->url = $request->get('url');
        $resource->atc_only = $request->get('atc_only');
        $resource->save();

        return redirect()->route('training.resources')->with('success', 'Resource uploaded!');
    }

    public function deleteResource($id)
    {
        $resource = AtcResource::whereId($id)->firstOrFail();

        $resource->delete();

        return redirect()->back()->with('info', 'Resource deleted!');
    }
}
