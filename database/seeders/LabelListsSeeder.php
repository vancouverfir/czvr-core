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
            ['S1 Training', 'fas fa-graduation-cap', '#006400', true, true, false, 1],
            ['S2 Training', 'fas fa-graduation-cap', '#007d00', true, true, false, 1],
            ['S3 Training', 'fas fa-graduation-cap', '#43A047', true, true, false, 1],
            ['Visitor S3 Training', 'fas fa-graduation-cap', '#388E3C', true, true, true, 5],
            ['C1 Training', 'fas fa-user-graduate', '#1E90FF', true, true, false, 1],
            ['Visitor C1 Training', 'fas fa-user-graduate', '#1C7ED6', true, true, true, 5],
            ['Availability Requested', 'fas fa-question', '#6A5ACD', false, false, false, null],
            ['Waitlist', 'far fa-pause-circle', '#009688', false, true, false, 0],
            ['Visitor Waitlist', 'far fa-pause-circle', '#87CEEB', false, true, true, 3],
            ['No Response', 'fas fa-question', '#9400D3', false, false, false, null],
            ['Inactive', 'fas fa-times', '#D3D3D3', true, false, false, 4],
            ['Marked for Removal', 'fas fa-user-slash', '#FF0000', true, false, false, 4],
            ['Leave Of Absence', 'fas fa-sign-out-alt', '#FFC107', false, false, false, null],
            ['Recertification', 'fas fa-retweet', '#82B1FF', false, false, false, null],
            ['Fast Track', 'fas fa-forward', '#00d382ff', false, false, false, null],
            ['Visitor Non-VATCAN', 'fab fa-pagelines', '#FF6347', false, false, true, null],
            ['Visitor VATCAN', 'fab fa-canadian-maple-leaf', '#B22222', false, false, true, null],
        ];

        foreach ($labels as [$name, $icon, $color, $visible_home, $exclusive, $vatcan, $new_status]) {
            StudentLabel::updateOrCreate(
                ['name' => $name],
                [
                    'fa_icon' => $icon,
                    'color' => $color,
                    'visible_home' => $visible_home,
                    'exclusive' => $exclusive,
                    'visitor' => $vatcan,
                    'new_status' => $new_status,
                ]
            );
        }
    }
}
