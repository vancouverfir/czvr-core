<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\LabelChecklistVisitorMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LabelChecklistVisitorMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear table before seeding
        DB::table('label_checklist_visitor_map')->truncate();

        $mappings = [
            // Visitor S3 Training (VATCAN visitors with S3 rating)
            [ 'label' => 'Visitor Waitlist', 'checklist_id' => 17, 'tier_type' => 'T1' ], // VATCAN Controller - Tier1
            [ 'label' => 'Visitor S3 Training', 'checklist_id' => 17, 'tier_type' => 'T1' ], // VATCAN Controller - Tier1
            [ 'label' => 'Visitor S3 Training', 'checklist_id' => 18, 'tier_type' => 'T2' ], // VATCAN Controller - Tier 2

            // Visitor C1 Training (VATCAN visitors with C1 rating)
            [ 'label' => 'Visitor C1 Training', 'checklist_id' => 19, 'tier_type' => 'T1' ], // VATCAN Controller - Tier 2 C1+

            // Non-Visitor training
            [ 'label' => 'Visitor C1 Training', 'checklist_id' => 20, 'tier_type' => 'T3' ], // Non-VATCAN Controller - Tier 1 S3 & Tier 2 C1+
        ];

        foreach ($mappings as $map) {
            $label = StudentLabel::where('name', $map['label'])->first();

            LabelChecklistVisitorMap::create([
                'label_id' => $label->id,
                'checklist_id' => $map['checklist_id'],
                'tier_type' => $map['tier_type'],
            ]);
        }
    }
}
