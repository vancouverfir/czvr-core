<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentInteractiveLabels;

class LabelController extends Controller
{
    public function assignLabel(Request $request, $student_id)
    {
        $student = Student::with('labels.label')->findOrFail($student_id);
        $label = StudentLabel::findOrFail($request->get('student_label_id'));

        $trainingLabels = ['S1 Training', 'S2 Training', 'S3 Training', 'C1 Training', 'Waitlist', 'Visitor Waitlist', 'Visitor S3 Training', 'Visitor C1 Training'];

        if (in_array($label->name, $trainingLabels)) {
            $existingTraining = $student->labels->first(function ($labelLink) use ($trainingLabels) {
                return in_array($labelLink->label->name ?? '', $trainingLabels);
            });

            if ($existingTraining && $existingTraining->label->id !== $label->id) {
                $existingTraining->delete();
            }
        }

        if (StudentInteractiveLabels::where('student_id', $student->id)->where('student_label_id', $label->id)->exists()) {
            return back()->with('error', "Label {$label->name} already assigned.");
        }

        StudentInteractiveLabels::create([
            'student_id' => $student->id,
            'student_label_id' => $label->id,
        ]);

        $this->updateStudentStatusBasedOnLabels($student->refresh());

        return redirect()->back()->with('success', 'Label Added!');
    }

    public function updateStudentStatusBasedOnLabels(Student $student)
    {
        $labelNames = $student->labels->pluck('label.name')->filter();

        $statusMap = [
            5 => ['Visitor S3 Training', 'Visitor C1 Training'],
            4 => ['Inactive', 'Marked for Removal'],
            3 => ['Visitor Waitlist'],
            1 => ['S1 Training', 'S2 Training', 'S3 Training', 'C1 Training'],
            0 => ['Waitlist'],
        ];

        $originalStatus = $student->status;

        foreach ($statusMap as $status => $labels) {
            if ($labelNames->intersect($labels)->isNotEmpty()) {
                $newStatus = $status;
                break;
            }
        }

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

    public function dropLabel($id, $student_label_id)
    {
        $student = Student::with('labels.label')->findOrFail($id);
        $link = $student->labels->firstWhere('student_label_id', $student_label_id);

        if (! $link) {
            return redirect()->back()->with('error', 'Label not found for this student!');
        }

        $labelToRemove = $link->label;

        if (! $labelToRemove) {
            return redirect()->back()->with('error', 'Label does not exist!');
        }

        $listLabels = [
            'S1 Training', 'S2 Training', 'S3 Training', 'C1 Training', 'Inactive', 'Marked for Removal', 'Waitlist', 'Visitor Waitlist', 'Visitor S3 Training', 'Visitor C1 Training',
        ];

        $currentListLabels = $student->labels->filter(function ($labelLink) use ($listLabels) {
            return in_array($labelLink->label->name ?? '', $listLabels);
        });

        if (in_array($labelToRemove->name, $listLabels) && $currentListLabels->count() <= 1) {
            return redirect()->back()->with('error', 'Each student must have at least one list label!');
        }

        $link->delete();

        $student->load('labels.label');
        $this->updateStudentStatusBasedOnLabels($student);

        return redirect()->back()->with('success', 'Label Removed!');
    }
}
