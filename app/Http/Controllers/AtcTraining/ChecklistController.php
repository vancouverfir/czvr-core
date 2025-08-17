<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\LabelChecklistMap;
use App\Models\AtcTraining\LabelChecklistVisitorMap;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentChecklistItem;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function completeItem(Request $request, $id)
    {
        $item = StudentChecklistItem::findOrFail($id);
        $item->update(['completed' => true]);

        return back()->with('success', 'Checklist item marked as Complete!');
    }

    public function completeMultiple(Request $request, $studentId)
    {
        $request->validate([
            'checklist_items' => 'required|array',
            'checklist_items.*' => 'integer|exists:student_checklist_items,id',
        ]);

        $student = Student::findOrFail($studentId);
        $itemIds = $request->input('checklist_items');

        StudentChecklistItem::whereIn('id', $itemIds)
            ->where('student_id', $student->id)
            ->update(['completed' => true]);

        return back()->with('success', 'Checklist items marked as completed!');
    }

    public function getTrainingOrder(bool $visitor = false): array
    {
        $query = $visitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;

        return $query::orderBy('id')
            ->with('label')
            ->get()
            ->pluck('label.name')
            ->unique()
            ->values()
            ->toArray();
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

    public function assignT2Checklist(Request $request, Student $student)
    {
        $isVisitor = $student->user->visitor;

        $labelNames = $student->labels->pluck('label.name')->unique()->toArray();
        $trainingOrder = $this->getTrainingOrder($isVisitor);

        $currentLabel = collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames));
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

    public function promoteVisitor(Request $request, Student $student)
    {
        $isNonVatcanVisitor = $student->user->division_code !== 'CAN';

        $labelNames = $student->labels->pluck('label.name')->unique()->toArray();
        $trainingOrder = $this->getTrainingOrder(true);

        $currentLabel = collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames));
        if (! $currentLabel) {
            return back()->with('error', 'No visitor label assigned to student!');
        }

        $nextLabelName = $this->NextVisitorLabel($currentLabel, $trainingOrder, $isNonVatcanVisitor);
        if (! $nextLabelName) {
            $student->labels()->delete();
            $student->checklistItems()->delete();
            $student->update(['status' => 9]);
            return back()->with('success', 'Completed training for '.$student->user->fullName('FLC').'!');
        }

        $this->assignNewLabel($student, $nextLabelName, $isNonVatcanVisitor ? 'T3' : 'T1', true);

        return back()->with('success', "Visitor promoted to {$nextLabelName} and checklists assigned!");
    }

    private function NextVisitorLabel(string $currentLabel, array $trainingOrder, bool $nonVatcan): ?string
    {
        if ($nonVatcan) {
            return collect($trainingOrder)
                ->slice(array_search($currentLabel, $trainingOrder) + 1)
                ->first(fn ($label) => ! empty($this->getChecklistIdsByLabelName($label, 'T3', true)));
        }

        $currentIndex = array_search($currentLabel, $trainingOrder);

        return $trainingOrder[$currentIndex + 1] ?? null;
    }

    private function assignNewLabel(Student $student, string $labelName, string $tierType, bool $visitor = false)
    {
        $student->labels()->delete();

        $nextLabel = StudentLabel::where('name', $labelName)->firstOrFail();
        StudentInteractiveLabels::create([
            'student_id' => $student->id,
            'student_label_id' => $nextLabel->id,
        ]);

        $student->checklistItems()->delete();

        $checklistIds = $this->getChecklistIdsByLabelName($labelName, $tierType, $visitor);
        foreach ($checklistIds as $checklistId) {
            $this->assignChecklistItemsToStudent($student, Checklist::findOrFail($checklistId)->items);
        }

        (new LabelController())->updateStatus($student->refresh());
    }

    public function promoteStudent(Request $request, Student $student)
    {
        $labelNames = $student->labels->pluck('label.name')->unique()->toArray();
        $trainingOrder = $this->getTrainingOrder(false);

        $currentLabel = collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames));
        if (! $currentLabel) {
            return back()->with('error', 'No label assigned to student!');
        }

        $currentIndex = array_search($currentLabel, $trainingOrder);
        $nextLabelName = $trainingOrder[$currentIndex + 1] ?? null;
        if (! $nextLabelName) {
            $student->labels()->delete();
            $student->checklistItems()->delete();
            $student->update(['status' => 9]);
            return back()->with('success', 'Completed training for '.$student->user->fullName('FLC').'!');
        }

        $this->assignNewLabel($student, $nextLabelName, 'T1');

        return back()->with('success', "Student promoted to {$nextLabelName} and checklists assigned!");
    }
}
