<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentLabelSeeder extends Seeder
{
    public function run(): void
    {
        $columns = ['name', 'fa_icon', 'color', 'visible_home', 'exclusive', 'visitor', 'new_status'];
        $labels = [
            1 => ['S1 Training', 'fas fa-graduation-cap', '#006400', 1, 1, 0, 1],
            2 => ['S2 Training', 'fas fa-graduation-cap', '#007d00', 1, 1, 0, 1],
            3 => ['S3 Training', 'fas fa-graduation-cap', '#43A047', 1, 1, 0, 1],
            4 => ['Visitor S3 Training', 'fas fa-graduation-cap', '#388E3C', 1, 1, 1, 5],
            5 => ['C1 Training', 'fas fa-user-graduate', '#1E90FF', 1, 1, 0, 1],
            6 => ['Visitor C1 Training', 'fas fa-user-graduate', '#1C7ED6', 1, 1, 1, 5],
            7 => ['Availability Requested', 'fas fa-question', '#6A5ACD', 0, 0, 0, null],
            8 => ['Waitlist', 'far fa-pause-circle', '#009688', 0, 1, 0, 0],
            9 => ['Visitor Waitlist', 'far fa-pause-circle', '#87CEEB', 0, 1, 1, 3],
            10 => ['No Response', 'fas fa-question', '#9400D3', 0, 0, 0, null],
            11 => ['Inactive', 'fas fa-times', '#D3D3D3', 1, 0, 0, 4],
            12 => ['Marked for Removal', 'fas fa-user-slash', '#FF0000', 1, 1, 0, 4],
            13 => ['Leave Of Absence', 'fas fa-sign-out-alt', '#FFC107', 0, 0, 0, null],
            14 => ['Recertification', 'fas fa-retweet', '#82B1FF', 0, 0, 0, null],
            15 => ['Fast Track', 'fas fa-forward', '#00d382ff', 0, 0, 0, null],
            16 => ['Visitor Non-VATCAN', 'fab fa-pagelines', '#FF6347', 0, 0, 1, null],
            17 => ['Visitor VATCAN', 'fab fa-canadian-maple-leaf', '#B22222', 0, 0, 1, null],
            18 => ['Training Paused', 'fas fa-pause', '#FFEB00', 1, 1, 0, 1],
        ];

        foreach ($labels as $id => $label) {
            DB::table('student_label')->insert(['id' => $id, ...array_combine($columns, $label)]);
        }
    }
}
