<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(StudentLabelSeeder::class);

        DB::table('checklists')->insert([
            'id' => 1,
            'name' => 'Example',
        ]);

        DB::table('checklist_items')->insert([
            'id' => 1,
            'checklist_id' => 1,
            'item' => 'Example',
        ]);

        DB::table('label_checklist_map')->insert([
            'id' => 1,
            'label_id' => 1,
            'checklist_id' => 1,
            'tier_type' => 'Example',
        ]);

        DB::table('label_checklist_visitor_map')->insert([
            'id' => 1,
            'label_id' => 1,
            'checklist_id' => 1,
            'tier_type' => 'Example',
        ]);
    }
}
