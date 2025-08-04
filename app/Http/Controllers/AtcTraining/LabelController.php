<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function assignLabel(Request $request, $student_id)
    {
        $student = Student::with('labels.label')->findOrFail($student_id);
        $label = StudentLabel::findOrFail($request->get('student_label_id'));

        if ($label->exclusive) {
            $existingExclusive = $student->labels
                ->filter(fn($link) => $link->label->exclusive)
                ->first();

            if ($existingExclusive && $existingExclusive->label->id !== $label->id) {
                $existingExclusive->delete();
            }
        }

        if ($student->labels->contains('student_label_id', $label->id)) {
            return back()->with('error', "Label {$label->name} already assigned!");
        }

        $student->labels()->create([
            'student_label_id' => $label->id,
        ]);

        $this->updateStatus($student->refresh());

        return back()->with('success', 'Label added!');
    }

    public function updateStatus(Student $student)
    {
        $newStatus = $student->labels->pluck('label.new_status')->reject(function ($value) {return is_null($value);})->max();


        if ($newStatus !== null) {
            $originalStatus = $student->status;
            $student->status = $newStatus;

            if (
                $student->status !== $originalStatus &&
                in_array($student->status, [0, 3])
            ) {
                $lastPosition = Student::where('status', $student->status)->max('position') ?? 0;
                $student->position = $lastPosition + 1;
                $student->checklistItems()->delete();
            }

            $student->save();
        }
    }

    public function dropLabel($student_id, $student_label_id)
    {
        $student = Student::with('labels.label')->findOrFail($student_id);
        $link = $student->labels->firstWhere('student_label_id', $student_label_id);

        if (! $link) {
            return back()->with('error', 'Label not found for this student!');
        }

        $labelToRemove = $link->label;

        if (! $labelToRemove) {
            return back()->with('error', 'Label does not exist!');
        }

        $currentExclusiveLabels = $student->labels->filter(fn($link) => $link->label->exclusive);

        if ($labelToRemove->exclusive && $currentExclusiveLabels->count() <= 1) {
            return back()->with('error', 'Each student must have at least one exclusive label!');
        }

        $link->delete();

        $student->load('labels.label');
        $this->updateStatus($student);

        return back()->with('success', 'Label removed!');
    }
}
