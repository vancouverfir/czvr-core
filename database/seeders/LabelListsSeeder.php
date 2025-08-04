<?php

namespace Database\Seeders;

use App\Models\AtcTraining\StudentLabel;
use Illuminate\Database\Seeder;

class LabelListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labels = [
            ['Availability Requested', 'fas fa-question', '#6A5ACD', false, false, null],
            ['S1 Training', 'fas fa-graduation-cap', '#006400', true, true, 1],
            ['S2 Training', 'fas fa-graduation-cap', '#007d00', true, true, 1],
            ['S3 Training', 'fas fa-graduation-cap', '#43A047', true, true, 1],
            ['Visitor S3 Training', 'fas fa-graduation-cap', '#388E3C', true, true, 5],
            ['C1 Training', 'fas fa-user-graduate', '#1E90FF', true, true, 1],
            ['Visitor C1 Training', 'fas fa-user-graduate', '#1C7ED6', true, true, 5],
            ['Waitlist', 'far fa-pause-circle', '#009688', false, true, 0],
            ['Visitor Waitlist', 'far fa-pause-circle', '#87CEEB', false, true, 3],
            ['No Response', 'fas fa-question', '#9400D3', false, false, null],
            ['Inactive', 'fas fa-times', '#D3D3D3', true, false, 4],
            ['Marked for Removal', 'fas fa-user-slash', '#FF0000', true, false, 4],
            ['Leave Of Absence', 'fas fa-sign-out-alt', '#FFC107', false, false, null],
            ['Recertification', 'fas fa-retweet', '#82B1FF', false, false, null],
            ['Fast Track', 'fas fa-forward', '#00d382ff', false, false, null],
            ['Visitor Non-VATCAN', 'fab fa-pagelines', '#FF6347', false, false, null],
            ['Visitor VATCAN', 'fab fa-canadian-maple-leaf', '#B22222', false, false, null],
        ];

        foreach ($labels as [$name, $icon, $color, $visible_home, $exclusive, $new_status]) {
            StudentLabel::updateOrCreate(
                ['name' => $name],
                [
                    'fa_icon' => $icon,
                    'color' => $color,
                    'visible_home' => $visible_home,
                    'exclusive' => $exclusive,
                    'new_status' => $new_status,
                ]
            );
        }
    }
}
