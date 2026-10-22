<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
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
            'email' => 'info@info.com',
            'display_fname' => 'System',
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'fname' => 'Roster',
            'lname' => 'Placeholder',
            'email' => 'info@info.ca',
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
            'id' => 1,
            'group' => 'exec',
            'position' => 'FIR Chief',
            'user_id' => 1,
            'group_id' => 1,
            'description' => 'Head of Vancouver’s day-to-day operations, manages all staff in the FIR, and keeps VATCAN updated with Vancouver. Also is currently the interim Events Coordinator.',
            'email' => 'chief@info.com',
            'shortform' => 'firchief',
        ]);

        DB::table('event_positions')->insert([
            'id' => 1,
            'position' => 'Centre',
        ]);

        DB::table('homepage_images')->insert([
            'id' => 1,
            'url' => '/CZVR_Colour_Long.png',
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

        $this->call([
            UsersTableSeeder::class,
            AuthorizationSeeder::class,
        ]);
    }
}
