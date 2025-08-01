<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('core_info')->insert([
            'id' => 1,
            'sys_name' => 'Vancouver FIR Core',
            'release' => 'DEV',
            'sys_build' => 'DEV',
            'copyright_year' => 'NONE',
            'banner' => '',
            'bannerLink' => '',
            'bannerMode' => '',
            'emailfirchief' => 'info@info.ca',
            'emaildepfirchief' => 'info@info.ca',
            'emailcinstructor' => 'info@info.ca',
            'emaileventc' => 'info@info.ca',
            'emailfacilitye' => 'info@info.ca',
            'emailwebmaster' => 'info@info.ca',
        ]);

        DB::table('users')->insert([
            'id' => 1,
            'fname' => 'System',
            'lname' => 'User',
            'email' => 'exmaple@email.com',
            'permissions' => 1,
            'display_fname' => 'System',
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'fname' => 'Roster',
            'lname' => 'Placeholder',
            'email' => 'exmaple@email.com',
            'permissions' => 1,
            'display_fname' => 'Roster',
        ]);

        DB::table('staff_groups')->insert([
            'id' => 1,
            'name' => 'Executive Team',
            'slug' => 'executive',
            'description' => 'CZVR\'s executive team oversees FIR operations',
            'can_receive_tickets' => true,
        ]);

        DB::table('staff_member')->insert([
            'group' => 'exec',
            'position' => 'FIR Chief',
            'user_id' => 1,
            'group_id' => 1,
            'description' => 'Head of Vancouver’s day-to-day operations, manages all staff in the FIR, and keeps VATCAN updated with Vancouver. Also is currently the interim Events Coordinator.',
            'email' => 'chief@info.com',
            'shortform' => 'firchief',
        ]);

        DB::table('event_positions')->insert(
            ['position' => 'Delivery'],
            ['position' => 'Ground'],
            ['position' => 'Tower'],
            ['position' => 'Departure'],
            ['position' => 'Arrival'],
            ['position' => 'Centre'],
            ['position' => 'Relief'],
        );

        DB::table('homepage_images')->insert([
            'url' => 'https://czvr.ca/storage/files/branding/czvr-logomark.png',
            'credit' => 'Vancouver FIR',
        ]);

        DB::table('training_waittimes')->insert([
            'id' => 1,
            'wait_length' => '1 Month',
            'colour' => 'green',
        ]);

        DB::table('instructors')->insert([
            'id' => 1,
            'user_id' => 1,
            'qualification' => 'ALL',
            'email' => 'example@email.com',
        ]);


        $this->call([\Database\Seeders\LabelListsSeeder::class, \Database\Seeders\ChecklistsSeeder::class,]);
    }
}
