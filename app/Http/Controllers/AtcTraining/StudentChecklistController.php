<?php

namespace App\Http\Controllers\AtcTraining;

use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\StudentChecklistItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentChecklistController extends Controller
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

    public function deleteChecklist($studentId, $name)
    {
        $student = Student::findOrFail($studentId);

        $checklist = Checklist::where('name', $name)->firstOrFail();
        $itemIds = $checklist->items->pluck('id');

        StudentChecklistItem::where('student_id', $student->id)
            ->whereIn('checklist_item_id', $itemIds)
            ->delete();

        return redirect()->back()->with('success', 'Checklist removed.');
    }

}
