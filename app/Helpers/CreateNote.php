<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateNote
{
    public static function newNote($studentId, $title, $content, ?int $authorCid = null)
    {
        return DB::table('student_notes')->insert([
            'student_id' => $studentId,
            'author_cid' => $authorCid,
            'title' => $title,
            'content' => $content,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
