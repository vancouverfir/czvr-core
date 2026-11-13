<?php

namespace App\Http\Controllers\AtcTraining;

use App\Helpers\CreateNote;
use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\LabelChecklistMap;
use App\Models\AtcTraining\LabelChecklistVisitorMap;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentChecklistItem;
use App\Models\AtcTraining\StudentLabel;
use App\Services\TrainingProgressionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    public function completeItem(Request $request, TrainingProgressionService $progression, $id): RedirectResponse
    {
        $item = StudentChecklistItem::findOrFail($id);
        $item->update(['completed' => true]);

        if ($item->student && $progression->completeS1TrainingIfDone($item->student)) {
            return back()->with('success', 'S1 Training complete! The S2 Training package was assigned!');
        }

        return back()->with('success', 'Checklist item marked as Complete!');
    }

    public function completeMultiple(Request $request, TrainingProgressionService $progression, $studentId): RedirectResponse
    {
        $request->validate([
            'checklist_items' => 'required|array',
            'checklist_items.*' => 'integer|exists:student_checklist_items,id',
        ]);

        $student = Student::findOrFail($studentId);
        $items = $student->checklistItems()->whereIn('id', $request->checklist_items)->get();

        foreach ($items as $item) {
            $item->completed = ! $item->completed;
            $item->save();
        }

        if ($progression->completeS1TrainingIfDone($student)) {
            return back()->with('success', 'S1 Training complete! The S2 Training package was assigned!');
        }

        return back()->with('success', 'Checklist items updated successfully!');
    }

    private function getChecklistIdsByLabelName(string $label, string $tierType = 'T1', bool $visitor = false): array
    {
        $mapClass = $visitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;

        return $mapClass::whereHas('label', fn ($q) => $q->where('name', $label))
            ->where('tier_type', $tierType)
            ->pluck('checklist_id')
            ->toArray();
    }

    private function assignChecklistItemsToStudent(Student $student, $items)
    {
        $existingItemIds = $student->checklistItems->pluck('checklist_item_id');

        foreach ($items as $item) {
            if (! $existingItemIds->contains($item->id)) {
                $student->checklistItems()->create([
                    'checklist_item_id' => $item->id,
                    'completed' => false,
                ]);
            }
        }
    }

    public function assignT2Checklist(Request $request, Student $student): RedirectResponse
    {
        $isVisitor = $student->user->visitor;
        $currentLabel = $student->currentTrainingLabel(Student::trainingOrder($isVisitor));

        if (! $currentLabel) {
            return back()->with('error', 'No valid label found for T2 checklist assignment!');
        }

        $checklistIds = $this->getChecklistIdsByLabelName($currentLabel, 'T2', $isVisitor);

        if (empty($checklistIds)) {
            return back()->with('error', "No T2 checklists found for {$currentLabel}!");
        }

        foreach ($checklistIds as $checklistId) {
            $this->assignChecklistItemsToStudent($student, Checklist::findOrFail($checklistId)->items);
        }

        return back()->with('success', "T2 checklists assigned for {$currentLabel}!");
    }

    public function startRecertification(
        Student $student,
        TrainingProgressionService $progression,
    ): RedirectResponse {
        if (! $progression->startRecertification($student, Auth::user())) {
            return back()->with('error', 'Recertification could not be started! Confirm that the controller is rated S1 or higher, is on a waitlist, and that the required training packages have checklists!');
        }

        return back()->with('success', 'The complete recertification path was assigned through the controller\'s current rating!');
    }

    public function promoteVisitor(Request $request, Student $student, TrainingProgressionService $progression): RedirectResponse
    {
        $isNonVatcanVisitor = $student->user->division_code !== 'CAN';
        $trainingOrder = Student::trainingOrder(true);
        $currentLabel = $student->currentTrainingLabel($trainingOrder);

        if (! $currentLabel) {
            return back()->with('error', 'No visitor label assigned to student!');
        }

        $nextLabelName = $isNonVatcanVisitor
            ? collect($trainingOrder)
                ->slice(array_search($currentLabel, $trainingOrder, true) + 1)
                ->first(fn ($label) => ! empty($this->getChecklistIdsByLabelName($label, 'T3', true)))
            : $student->nextTrainingLabel($trainingOrder);

        if (! $nextLabelName) {
            $student->markTrainingCompleted();
            CreateNote::newNote($student->id, 'Completed Training', Auth::user()->fullName('FLC').' marked training as completed!');

            return back()->with('success', 'Completed training for '.$student->user->fullName('FLC').'!');
        }

        $this->assignNewLabel($progression, $student, $nextLabelName, $isNonVatcanVisitor ? 'T3' : 'T1', true);

        CreateNote::newNote($student->id, 'Promoted Visitor', '<u>'.Auth::user()->fullName('FLC').'</u> promoted visitor to '.$nextLabelName);

        return back()->with('success', "Visitor promoted to {$nextLabelName} and checklists assigned!");
    }

    private function assignNewLabel(TrainingProgressionService $progression, Student $student, string $labelName, string $tierType, bool $visitor = false): void
    {
        $progression->assignTrainingPackage($student, StudentLabel::where('name', $labelName)->firstOrFail(), $visitor, $tierType);

        (new LabelController)->updateStatus($student->refresh());
    }

    public function promoteStudent(Request $request, Student $student, TrainingProgressionService $progression): RedirectResponse
    {
        $student->loadMissing('user.rosterProfile');

        if ($student->isConsolidating()) {
            return back()->with('error', sprintf(
                'Training remains locked until %.1f more consolidation hours are logged or the requirement is waived!',
                $student->consolidationRemainingHours(),
            ));
        }

        if ($student->status !== Student::STATUS_WAITLIST) {
            return back()->with('error', 'Active training stages now follow VATCAN rating promotions automatically!');
        }

        if ($student->startsWithRecertification()) {
            return back()->with('error', 'This controller already holds S2 or higher! Use Start Recertification instead!');
        }

        $nextLabelName = Student::RATING_TRAINING_LABELS[0];
        $this->assignNewLabel($progression, $student, $nextLabelName, 'T1');

        CreateNote::newNote($student->id, 'Promoted Student', '<u>'.Auth::user()->fullName('FLC').'</u> promoted student to '.$nextLabelName);

        return back()->with('success', "Student promoted to {$nextLabelName} and checklists assigned!");
    }
}
