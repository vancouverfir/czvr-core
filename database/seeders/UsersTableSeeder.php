<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_label')->insert([
            'id' => 1,
            'name' => 'Example',
            'fa_icon' => 'fas fa-check',
            'color' => '#00FF00',
            'visible_home' => 1,
            'exclusive' => 1,
            'visitor' => 1,
            'new_status' => 1,
        ]);

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
