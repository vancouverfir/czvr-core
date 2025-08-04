<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentChecklistItem;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function assignChecklist(Request $request, Student $student)
    {
        // Validate the request
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
        ]);

        $checklist = Checklist::findOrFail($request->checklist_id);

        foreach ($checklist->items as $item) {
            $student->checklistItems()->create([
                'checklist_item_id' => $item->id,
                'completed' => false,
            ]);
        }

        return back()->with('success', 'Checklist assigned to Student!');
    }

    public function completeItem(Request $request, $id)
    {
        $checklistItem = StudentChecklistItem::findOrFail($id);
        $checklistItem->completed = true;
        $checklistItem->save();

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

        return redirect()->back()->with('success', 'Checklist items marked as completed!');
    }

    public function deleteChecklist($studentId, $name)
    {
        $student = Student::findOrFail($studentId);

        $checklist = Checklist::where('name', $name)->firstOrFail();
        $itemIds = $checklist->items->pluck('id');

        StudentChecklistItem::where('student_id', $student->id)
            ->whereIn('checklist_item_id', $itemIds)
            ->delete();

        return redirect()->back()->with('success', 'Checklist removed!');
    }

    public function getChecklistIdsByLabel($label)
    {
        $map = [
            'S1 Training' => [1, 2],
            'S2 Training' => [5, 6],
            'S3 Training' => [9, 10],
            'C1 Training' => [14, 15],
            'Visitor VATCAN' => [17],
            'Visitor Non-VATCAN' => [],
        ];

        return $map[$label] ?? [];
    }

    public function assignT2Checklist(Request $request, Student $student)
    {
        $labelNames = $student->labels->pluck('label.name')->toArray();
        $rating = $student->user->rating_short;

        $isVatcan = in_array('Visitor Vatcan', $labelNames);
        $isNonVatcan = in_array('Visitor Non Vatcan', $labelNames);

        if ($isVatcan || $isNonVatcan) {
            $checklistName = match (true) {
                $isVatcan && $rating === 'S3' => 'VATCAN Controller - Restricted S3',
                default => null,
            };

            if (! $checklistName) {
                return back()->with('error', 'No applicable Tier 2 checklist found for visitor!');
            }

            $checklist = Checklist::where('name', $checklistName)->first();
            if (! $checklist) {
                return back()->with('error', "Checklist not found: {$checklistName}");
            }

            $existingItemIds = $student->checklistItems->pluck('checklist_item_id');
            foreach ($checklist->items as $item) {
                if (! $existingItemIds->contains($item->id)) {
                    $student->checklistItems()->create([
                        'checklist_item_id' => $item->id,
                        'completed' => false,
                    ]);
                }
            }

            return back()->with('success', "Visitor Tier 2 checklist '{$checklistName}' assigned!");
        }

        $trainingOrder = ['S1 Training', 'S2 Training', 'S3 Training', 'C1 Training'];

        $currentLabel = collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames));

        if (! $currentLabel) {
            return back()->with('error', 'No valid label found for T2 checklist assignment!');
        }

        $checklistIds = $this->getT2ChecklistIdsByLabel($currentLabel);

        if (empty($checklistIds)) {
            return back()->with('error', "No T2 checklists found for {$currentLabel}!");
        }

        $existingItemIds = $student->checklistItems->pluck('checklist_item_id');

        foreach ($checklistIds as $checklistId) {
            $checklist = Checklist::findOrFail($checklistId);
            foreach ($checklist->items as $item) {
                if (! $existingItemIds->contains($item->id)) {
                    $student->checklistItems()->create([
                        'checklist_item_id' => $item->id,
                        'completed' => false,
                    ]);
                }
            }
        }

        return back()->with('success', "T2 checklists assigned for {$currentLabel}!");
    }

    public function getT2ChecklistIdsByLabel($label)
    {
        $map = [
            'S1 Training' => [3, 4],
            'S2 Training' => [7, 8],
            'S3 Training' => [11, 12, 13],
            'C1 Training' => [],
            'Visitor VATCAN' => [18],
            'Visitor Non-VATCAN' => [],
        ];

        return $map[$label] ?? [];
    }

    public function promoteVisitor(Request $request, Student $student)
    {
        $labelNames = $student->labels->pluck('label.name')->toArray();

        if (! in_array('Visitor Waitlist', $labelNames)) {
            return back()->with('error', 'Student is not on Visitor Waitlist!');
        }

        $isVatcan = in_array('Visitor VATCAN', $labelNames);
        $rating = $student->user->rating_short;

        $checklistName = match (true) {
            $isVatcan && $rating === 'S3' => 'VATCAN Controller - Unrestricted S3',
            $isVatcan && $rating === 'C1' => 'VATCAN Controller - Restricted C1+',
            ! $isVatcan && in_array($rating, ['S3', 'C1']) => 'Non-VATCAN Controller - Unrestricted S3 & Restricted C1+',
            default => null,
        };

        if (! $checklistName) {
            return back()->with('error', 'Unsupported rating or label combination for Visitor promotion!');
        }

        $checklist = Checklist::where('name', $checklistName)->first();
        if (! $checklist) {
            return back()->with('error', "Checklist not found: {$checklistName}");
        }

        $student->checklistItems()->delete();

        foreach ($checklist->items as $item) {
            $student->checklistItems()->create([
                'checklist_item_id' => $item->id,
                'completed' => false,
            ]);
        }

        $nextLabelName = $rating === 'S3' ? 'Visitor S3 Training' : 'Visitor C1 Training';
        $nextLabel = StudentLabel::where('name', $nextLabelName)->firstOrFail();

        $student->labels()->whereHas('label', function ($query) {
            $query->where('name', 'Visitor Waitlist');
        })->delete();

        StudentInteractiveLabels::create([
            'student_id' => $student->id,
            'student_label_id' => $nextLabel->id,
        ]);

        (new LabelController)->updateStudentStatusBasedOnLabels($student->refresh());

        return back()->with('success', "Visitor promoted to {$nextLabelName} with checklist assigned!");
    }

    public function promoteStudent(Request $request, Student $student)
    {
        $labelNames = $student->labels->pluck('label.name')->toArray();
        $trainingOrder = ['Waitlist', 'S1 Training', 'S2 Training', 'S3 Training', 'C1 Training'];
        $currentLabel = collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames));

        if (! $currentLabel) {
            return back()->with('error', 'No label assigned to student!');
        }

        $trainingOrder = ['Waitlist', 'S1 Training', 'S2 Training', 'S3 Training', 'C1 Training'];
        $currentIndex = array_search($currentLabel, $trainingOrder);

        if ($currentIndex === false || $currentIndex === count($trainingOrder) - 1) {
            return back()->with('error', 'Student cannot be promoted further!');
        }

        $nextLabelName = $trainingOrder[$currentIndex + 1];

        $currentLabelLink = $student->labels->firstWhere('label.name', $currentLabel);
        if ($currentLabelLink) {
            $currentLabelLink->delete();
        }

        $nextLabel = StudentLabel::where('name', $nextLabelName)->firstOrFail();
        StudentInteractiveLabels::create([
            'student_id' => $student->id,
            'student_label_id' => $nextLabel->id,
        ]);

        $student->checklistItems()->delete();

        $checklistIds = $this->getChecklistIdsByLabel($nextLabelName);
        foreach ($checklistIds as $checklistId) {
            $checklist = Checklist::findOrFail($checklistId);

            foreach ($checklist->items as $item) {
                $student->checklistItems()->create([
                    'checklist_item_id' => $item->id,
                    'completed' => false,
                ]);
            }
        }

        $labelController = new LabelController();

        $labelController->updateStudentStatusBasedOnLabels($student->refresh());

        return back()->with('success', "Student promoted to {$nextLabelName} and checklists assigned!");
    }
}
