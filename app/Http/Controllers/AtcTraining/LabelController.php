<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentLabel;
use App\Services\TrainingProgressionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelController extends Controller
{
    public function assignLabel(Request $request, $student_id, TrainingProgressionService $progression): RedirectResponse
    {
        $student = Student::with('labels.label')->findOrFail($student_id);
        $label = StudentLabel::findOrFail($request->get('student_label_id'));

        if (! StudentLabel::assignableTo($student)->whereKey($label->id)->exists()) {
            return back()->with('error', "Label {$label->name} cannot be assigned to this student!");
        }

        if (! $student->isVisitor() && in_array($label->name, Student::RATING_TRAINING_LABELS, true)) {
            $progression->assignTrainingPackage($student, $label, preserveChecklist: true);

            return back()->with('success', "Restored {$label->name}. Remove the pause label to resume training.");
        }

        if ($student->labels->contains('student_label_id', $label->id)) {
            return back()->with('error', "Label {$label->name} already assigned!");
        }

        DB::transaction(function () use ($student, $label): void {
            if ($label->exclusive) {
                $student->labels()->whereHas('label', fn ($query) => $query->where('exclusive', true))->delete();
            }

            if ((int) $student->status === Student::STATUS_MARKED_FOR_REMOVAL
                && in_array($label->new_status, Student::WAITLIST_STATUSES, true)) {
                $student->labels()->whereHas('label', fn ($query) => $query
                    ->where('new_status', Student::STATUS_MARKED_FOR_REMOVAL)
                    ->orWhereIn('name', ['No Response', 'Renewal Expired']))->delete();
            }

            $student->addLabel($label);
            $this->updateStatus($student->refresh());
        });

        return back()->with('success', 'Label added!');
    }

    public function updateStatus(Student $student): void
    {
        $newStatus = $student->labels->pluck('label.new_status')->reject(function ($value) {
            return is_null($value);
        })->max();

        if ($newStatus !== null) {
            $originalStatus = $student->status;
            $student->status = $newStatus;

            if (
                $student->status !== $originalStatus &&
                $student->isWaitlisted()
            ) {
                $student->renewed_at = now();
                $student->position = Student::nextQueuePosition((int) $student->status);
                $student->checklistItems()->delete();
            }

            $student->save();
        }
    }

    public function dropLabel($student_id, $student_label_id): RedirectResponse
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

        if (! $student->isVisitor() && in_array($labelToRemove->name, Student::MANAGED_TRAINING_LABELS, true)) {
            return back()->with('error', 'This training label is managed by the training workflow!');
        }

        $currentExclusiveLabels = $student->labels->filter(fn ($link) => $link->label->exclusive);

        if (($labelToRemove->exclusive && $currentExclusiveLabels->count() <= 1)
            || ($labelToRemove->name === Student::PAUSED_LABEL && $currentExclusiveLabels->isEmpty())) {
            return back()->with('error', 'Each student must have at least one exclusive label!');
        }

        $link->delete();

        $student->load('labels.label');
        $this->updateStatus($student);

        return back()->with('success', 'Label removed!');
    }
}
