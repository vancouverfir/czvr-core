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
        $waitlist = new StudentLabel([
            'name' => 'Waitlist',
            'fa_icon' => 'far fa-pause-circle',
            'color' => '#009688',
        ]);
        $waitlist->save();

        $visitorWaitlist = new StudentLabel([
            'name' => 'Visitor Waitlist',
            'fa_icon' => 'far fa-pause-circle',
            'color' => '#87CEEB',
        ]);
        $visitorWaitlist->save();

        $availabilityRequested = new StudentLabel([
            'name' => 'Availability Requested',
            'fa_icon' => 'fa-solid fa-question',
            'color' => '#6A5ACD',
        ]);
        $availabilityRequested->save();

        $noResponse = new StudentLabel([
            'name' => 'No Response',
            'fa_icon' => 'far fa-pause-circle',
            'color' => '#9400D3',
        ]);
        $noResponse->save();

        $readyForPickUp = new StudentLabel([
            'name' => 'Ready For Pick-Up',
            'fa_icon' => 'fa-solid fa-truck-pickup',
            'color' => '#00E5FF',
        ]);
        $readyForPickUp->save();

        $recertification = new StudentLabel([
            'name' => 'Recertification',
            'fa_icon' => 'fa-solid fa-chalkboard',
            'color' => '#82B1FF',
        ]);
        $recertification->save();

        $leaveOfAbsence = new StudentLabel([
            'name' => 'Leave Of Absence',
            'fa_icon' => 'fa fa-sign-out',
            'color' => '#FFC107',
        ]);
        $leaveOfAbsence->save();

        $visitorNonVATCAN = new StudentLabel([
            'name' => 'Visitor Non-VATCAN',
            'fa_icon' => 'fa-brands fa-pagelines',
            'color' => '#FF6347',
        ]);
        $visitorNonVATCAN->save();

        $visitorVATCAN = new StudentLabel([
            'name' => 'Visitor VATCAN',
            'fa_icon' => 'fa-brands fa-canadian-maple-leaf',
            'color' => '#B22222',
        ]);
        $visitorVATCAN->save();

        $S1Training = new StudentLabel([
            'name' => 'S1 Training',
            'fa_icon' => 'fa fa-mortar-board',
            'color' => 'darkgreen',
        ]);
        $S1Training->save();

        $S2Training = new StudentLabel([
            'name' => 'S2 Training',
            'fa_icon' => 'fa fa-mortar-board',
            'color' => '#006400',
        ]);
        $S2Training->save();

        $S3Training = new StudentLabel([
            'name' => 'S3 Training',
            'fa_icon' => 'fa fa-mortar-board',
            'color' => '#43A047',
        ]);
        $S3Training->save();

        $C1Training = new StudentLabel([
            'name' => 'C1 Training',
            'fa_icon' => 'fa fa-mortar-board',
            'color' => '#1E90FF',
        ]);
        $C1Training->save();

        $inactive = new StudentLabel([
            'name' => 'Inactive',
            'fa_icon' => 'fa fa-close',
            'color' => '#D3D3D3',
        ]);
        $inactive->save();

        $markedforRemoval = new StudentLabel([
            'name' => 'Marked for Removal',
            'fa_icon' => 'fa-solid fa-user-slash',
            'color' => '#FF0000',
        ]);
        $markedforRemoval->save();
    }
}
