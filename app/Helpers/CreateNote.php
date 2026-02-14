<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateNote
{
    public static function newNote($studentId, $title, $content, $authorId = 1)
    {
        return DB::table('student_notes')->insert([
            'student_id' => $studentId,
            'author_id' => $authorId,
            'title' => $title,
            'content' => $content,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public static function addLabel($studentId, $labelId)
    {
        return DB::table('student_interactive_labels')->insert([
            'student_label_id' => $labelId,
            'student_id' => $studentId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
