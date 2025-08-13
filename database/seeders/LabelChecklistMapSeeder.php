<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\LabelChecklistMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LabelChecklistMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear table before seeding
        DB::table('label_checklist_map')->truncate();

        $mappings = [
            // Tier 1 - Normal training
            [ 'label' => 'Waitlist', 'checklist_id' => 1,  'tier_type' => 'T1' ],
            [ 'label' => 'S1 Training', 'checklist_id' => 1,  'tier_type' => 'T1' ],
            [ 'label' => 'S1 Training', 'checklist_id' => 2,  'tier_type' => 'T1' ],
            [ 'label' => 'S2 Training', 'checklist_id' => 5,  'tier_type' => 'T1' ],
            [ 'label' => 'S2 Training', 'checklist_id' => 6,  'tier_type' => 'T1' ],
            [ 'label' => 'S3 Training', 'checklist_id' => 9,  'tier_type' => 'T1' ],
            [ 'label' => 'S3 Training', 'checklist_id' => 10, 'tier_type' => 'T1' ],
            [ 'label' => 'C1 Training', 'checklist_id' => 14, 'tier_type' => 'T1' ],
            [ 'label' => 'C1 Training', 'checklist_id' => 15, 'tier_type' => 'T1' ],

            // Tier 2 - Normal training
            [ 'label' => 'S1 Training', 'checklist_id' => 3,  'tier_type' => 'T2' ],
            [ 'label' => 'S1 Training', 'checklist_id' => 4,  'tier_type' => 'T2' ],
            [ 'label' => 'S2 Training', 'checklist_id' => 7,  'tier_type' => 'T2' ],
            [ 'label' => 'S2 Training', 'checklist_id' => 8,  'tier_type' => 'T2' ],
            [ 'label' => 'S3 Training', 'checklist_id' => 11, 'tier_type' => 'T2' ],
            [ 'label' => 'S3 Training', 'checklist_id' => 12, 'tier_type' => 'T2' ],
            [ 'label' => 'S3 Training', 'checklist_id' => 13, 'tier_type' => 'T2' ],
        ];

        foreach ($mappings as $map) {
            $label = StudentLabel::where('name', $map['label'])->first();

            LabelChecklistMap::create([
                'label_id' => $label->id,
                'checklist_id' => $map['checklist_id'],
                'tier_type' => $map['tier_type'],
            ]);
        }
    }
}
