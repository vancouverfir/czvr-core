<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function store(Request $request)
    {
        $teacher = new Teacher;
        $teacher->user_cid = $request->input('newteacher');
        $teacher->is_twr = $request->input('is_twr');
        $teacher->is_gnd = $request->input('is_gnd');
        $teacher->is_radar = $request->input('is_radar');
        $teacher->is_enroute = $request->input('is_enroute');
        $teacher->is_instructor = $request->input('is_instructor');
        $teacher->save();

        return redirect()->route('instructors');
    }

    public function delete($id)
    {
        $teacher = Teacher::whereId($id)->firstOrFail();
        $teacher->delete();

        return redirect('/instructors')->withSuccess('Teacher Removed!');
    }
}
