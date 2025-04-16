<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\ChecklistItem;

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------
        // S1 Unrestricted Supervised Checklist
        // -------------------------------------

        $unrestricted = Checklist::create([
            'name' => 'S1 Unrestricted Supervised',
        ]);

        $unrestrictedItems = [
            'Have student screen share to ensure all items are set up correctly.',
            'Review CZVR Knowledge check if student scored less than 80%',
            'Student is able to connect to the VATSIM network',
            'Is able to correctly set-up vis centres and vis ranges',
            'Is able to open the ground ASR for the airport they are selecting',
            'Is able to correctly interpret all the information on the departure list',
            'Is able to modify information on the departure list as needed including: Text/receive/voice capabilities, Scratchpad, Open the flight plan box to view route and other info, Create and modify squawk codes, Update status tags.',
            'Send text messages to text-only pilots',
            'Create a flight plan for an aircraft who don’t have a flight plan filed (e.g. for VFR A/C)',
            'Student has completed the CZVR S1 Knowledge Check review if grade is less than 80%',
            'Student is familiar with SOPs, CBTs, & LOAs, Charts, and Procedures.',
            'Basic radio ediquette, Understands the phonetic alphabet/numbers, Understands how to ask for and give a radio check',
            'Is able to decode the basics of a METAR',
            'Is able to convert wind from True to Magnetic and select the active runway.',
            'Understands the difference between IFR and VFR',
            'Understands how to select an appropriate cruising to assign an aircraft based on  Direction of flight, Major obstacles, Flight levels and transition alts and when FL180 is not available. RVSM and  Non-RVSM',
            'Is able to recognize incorrectly filed altitudes and correctly amends them to a correct altitude',
            'Understands how to read a route and resources to visualize a particular route if necessary (e.g. SkyVector)',
            'Understands the difference between an instruction and a clearance',
            'Is able to use correct phraseology when giving an IFR clearance: Prefix , A/C Ident, Clearance limit, SID, Route, Alt, Speed, Departure, enroute, approach or holding instructions, special instruction such as SSR Code, traffic info.',
            'Understands aprons in Canada are uncontrolled and the implications',
            'Student is able to amend a clearance',
            'Students are familiarized with CYYJ runway configurations',
            'Students are familiarized with CYYJ & CYLW SIDs',
            'Student has demonstrated a working knowledge of the CZVR/KZSE LOA, CZVR/CZEG LOA, and the CZVR/PAZA LOA and its contents.',
            'Student is familiarized with how and when to use a .wallop',
            'Student has been familiarized with PDCs and their usage and the usage of the CZVR PDC plugin.',
            'Student is made aware that filing direct on longer IFR flight plans is not acceptable',
            'Understands how to give taxi instructions with correct pharseology.',
            'How to give controller brief, get control of a runway, how to coordinate with other controllers, runway crossings.',
            'Student has been familiarized with CYYJ taxiway/apron configuration, common parking areas, and taxiway limits/restrictions.',
            'Student has been familiarized with where to find information on charts for ground ops at other airports.',
            'Student has been shown where to find the unrestricted airport documentation',
            'Student has been familiarized with blanket authorization from other controllers (e.g. crossing instructions for runways), including when they are applicable and when they’re not',
            'Completed at least 1 sweatbox on CYYJ or CYLW GND',
        ];

        foreach ($unrestrictedItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $unrestricted->id,
                'item' => $item,
            ]);
        }

        // -------------------------------
        // S1 Unrestricted Solo Checklist
        // -------------------------------

        $solo = Checklist::create([
            'name' => 'S1 Unrestricted Solo',
        ]);

        $soloItems = [
            'Has met the minimum competencies for Clearance Delivery & Ground - Supervised.',
            'Student demonstrates they are able to re-route an aircraft',
            'Student is able to interpret a full METAR (whether through own knowledge or using external resources)',
            'Student is able to interpret equipment codes and select appropriate routes based on this',
            'Student is able to provide alternate departure instructions in the event of an aircraft being unable to fly SIDs',
            'Students are familiarized with where to find charts and other relevant information for other airports within the FIR',
            'Student is able to queue up multiple requests from pilots without becoming too flustered or losing train of thought',
            'Student is made aware of where they can find preferred routes',
            'Student understands the importance of receiving a position briefing',
            'Student is able to brief controllers below them (sign-on briefing) and above them (sign-off briefing)',
            'Student has demonstrated a strong grasp of both deconfliction strategies on the ground as well as the foresight to avoid conflicts in the first place',
            'Student has demonstrated the ability to manage and take control of a marginally busy frequency',
            'Student has been familiarized with where to find the procedures associated with reduced/low visibility procedures and understands the basics of how to apply them',
            'Student has been familiarized with simulated de-icing ops',
            'Student has been familiarized as to where to find additional information for the ground procedures at other airports',
            'Student is introduced to finding suitable cruising altitudes based on MSAs, MEAs, & MOCAs',
            'Student understands usage and procedures for intersection departures',
            'Student has completed a minimum of 2 sweatboxes one on CYYJ and CYLW GND',
        ];

        foreach ($soloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $solo->id,
                'item' => $item,
            ]);
        }

        // -----------------------------------
        // T2 GND Supervised (CYVR) Checklist
        // -----------------------------------

        $t2 = Checklist::create([
            'name' => 'T2 GND Supervised (CYVR)',
        ]);

        $t2Items = [
            "Read T2 CBT's & SOP's",
            'Familiar with CZVR charts and taxiways',
            'Student has passed the CZVR S1 Tier 2 Endorsement Exam',
            'Students are familiarized with CYVR runway configurations',
            'Students are familiarized with CYVR SIDs',
            'Student has been familiarized with CYVR taxiway/apron configuration, common parking areas, and taxiway limits/restrictions',
            'Student has done a minimum of one sweatbox on CYVR_GND',
            'Student has been familiarized with blanket authorization from other controllers (e.g. crossing instructions for 13/31), including when they are applicable and when they’re not',
            'Student understands common routing from CYVR',
        ];

        foreach ($t2Items as $item) {
            ChecklistItem::create([
                'checklist_id' => $t2->id,
                'item' => $item,
            ]);
        }

        // -----------------------------
        // T2 GND Solo (CYVR) Checklist
        // -----------------------------

        $t2Solo = Checklist::create([
            'name' => 'T2 GND Solo (CYVR)',
        ]);

        $t2SoloItems = [
            'Student has met the minimum competencies for CYVR Ground - Supervised',
            'Student has demonstrated a strong grasp of both deconfliction strategies as well as the foresight to avoid conflicts in the first place',
            'Student has demonstrated the ability to manage and take control of a marginally busy frequency (sweatbox or network)',
            'Student has been familiarized with the procedures involved with a ground split',
            'Student has been familiarized with where to find the procedures associated with reduced/low visibility procedures and understands the basics of how to apply them',
            'Student has been familiarized with simulated de-icing ops (specifically at CYVR but also general concepts as applicable)',
            'Student understands usage and procedures for intersection departures at CYVR  Including TC AIM 4.2.5.1 (oblique intersection usage)',
        ];

        foreach ($t2SoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $t2Solo->id,
                'item' => $item,
            ]);
        }

        // -------------------------------------
        // S2 Unrestricted Supervised Checklist
        // -------------------------------------
        $s2 = Checklist::create([
            'name' => 'S2 Unrestricted Supervised',
        ]);

        $s2Items = [
            'Student has met the minimum competencies for Clearance Delivery & Ground certified',
            'Recommended - Student has completed the CZVR S2 Knowledge Check & review if mark less than 80%',
            'Recommended - Student has completed at least 5 hours on ground or clearance',
            'S2 CBT , SOPs',
            'An understanding of flight priority (emergency, then airborne aircraft, then aircraft on the ground)',
            'Understands the circuit, components, standard circuit in Canada, where to find info for non-standard circuits, how VFR are expected to depart the circuit, standard circuit entries & non-standard entries.',
            'Is able to select correct active runways based on wind and airport procedures',
            'Is aware of basic takeoff requests & instructions - Departure frequency, backtracks, short delays on runway, line up and wait, turnouts for VFR A/C',
            'Is aware of basic landing requests & instructions - Full stop landings, touch and goes, stop and goes, low and overs/low approaches, reporting points',
            'Uses the following phraseology correctly - Clearing aircraft for takeoff (warnings, instructions or request approvals, weather, clearance)',
            'Uses the following phraseology correctly - Clearing an aircraft to land (instructions or requests approval, weather, type of landing clearance and runway #)',
            'Continue [sequence #], [traffic to follow if applicable] or extending current leg.',
            'VFR circuit entries/overflights',
            'Traffic point outs',
            'Understands the application of radar releases',
            'Understands basic radar symbology presented in the tower view',
            'Familiar with the published go-around procedures, how to issue non-standard go-around instructions, and when to initiate a go-around, and the correct phraseology for all of the listed items',
            'Understands wake-turbulence concepts',
            'Has a basic understanding of different common IFR approaches (ILS, RNAV, Visual)',
            'Student is familiar with CYYJ runway configuration and circuit directions',
            'Understands Contact approaches.',
            'Helicopter operations and phraseology',
        ];

        foreach ($s2Items as $item) {
            ChecklistItem::create([
                'checklist_id' => $s2->id,
                'item' => $item,
            ]);
        }

        // -------------------------------
        // S2 Unrestricted Solo Checklist
        // -------------------------------
        $s2Solo = Checklist::create([
            'name' => 'S2 Unrestricted Solo',
        ]);

        $s2SoloItems = [
            'Student has met the minimum competencies for Tower - Supervised',
            'Student has completed VATCAN S2 Exam',
            'Student has demonstrated a basic understanding of helicopter operations and phraseology',
            'Student is aware of the airspace around the CYYJ/CYLW Tower CZ',
            'Student demonstrates the ability to effectively sequence traffic in the circuit.',
            'Student demonstrates a basic understanding of simulated IFR procedures',
            'Student has been familiarized with VFR Weather minima',
            'Student has been familiarized with LAHSO Ops',
            'Student has been familiarized with Special VFR phraseology',
            'Student demonstrates a working knowledge of issuing traffic point-outs',
            'Student demonstrates understanding of immediate/on-the-roll takeoffs',
            'Student has been familiarized with where to find information on VFR departure & arrival procedures',
            'Student should be able to deal with airborne conflicts and be familiar with strategies to deconflict airborne traffic',
            'Handling emergencies',
            'Sequencing and resequencing of traffic in the circuit',
            'Intersection departures',
            'Full procedure, circling, contact approaches',
            'Low/Reduced visibility ops',
            'TALPA GRF runway condition reports (where to find them, how to interpret them, how to pass on the info to pilots)',
            'Water-ops & phraseology at CYHC Tower CZ',
            'Assigning VFR departure procedures',
            'Assigning VFR arrival procedures',
            'Assigning VFR overflight routes',
            'Student has a basic understanding of the usages for radar within the tower environment',
            'Student has been familiarized with where to find information about the airspace surrounding other unrestricted tower CZs',
            'Student has been familiarized with where to find information for VFR aircraft at other unrestricted airports',
        ];
                
        foreach ($s2SoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s2Solo->id,
                'item' => $item,
            ]);
        }

        // ----------------------------------
        // S2 T2 Supervised (CYVR) Checklist
        // ----------------------------------
        $s2t2 = Checklist::create([
            'name' => 'S2 T2 Supervised (CYVR)',
        ]);

        $s2t2Items = [
            'Student has met the minimum competencies for Unrestricted Tower - Solo',
            'Student has achieved their S1 Tier 2 endorsement',
            'Student has read study materials as per page 17 https://czvr.ca/storage/files/uploads/1713054501.pdf',
            'Recommended - Student has completed at least 10 hours of solo time on Unrestricted Towers.',
            'Student has passed the CZVR S2 Tier 2 Endorsement Exam',
            'Student has shown a strong comprehension of basic tower items',
            'Student has been familiarized with CYVR configuration and circuit directions',
            'Student has been familiarized with parallel runway ops & simultaneous parallel dependent, and independent approaches',
            'Student has completed a minimum of one sweatbox session on CYVR_TWR',
        ];

        foreach ($s2t2Items as $item) {
            ChecklistItem::create([
                'checklist_id' => $s2t2->id,
                'item' => $item,
            ]);
        }

        // ----------------------------
        // S2 T2 Solo (CYVR) Checklist
        // ----------------------------
        $s2t2Solo = Checklist::create([
            'name' => 'S2 T2 Solo (CYVR)',
        ]);

        $s2t2SoloItems = [
            'Student has met the minimum competencies for Tier 2 Tower - Supervised',
            'Benefits of intersection departures at CYVR',
            'Low/Reduced visibility ops at CYVR',
            'Water-ops & phraseology at CYVR',
            'Assigning VFR departure procedures at CYVR',
            'Assigning VFR arrival procedures at CYVR',
            'Assigning VFR overflight routes at CYVR',
            'Student must be mostly comfortable handling a relatively full circuit plus other aircraft',
            'Student must be able to sequence departures and IFR arrivals in between circuit traffic',
            'Student must demonstrate the ability to maintain control of frequency during busy periods/when potential conflicts start to arise',
            'Recommend FSS Training prior to solo check this box if complete or student is not interested in controlling FSS',
        ];

        foreach ($s2t2SoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s2t2Solo->id,
                'item' => $item,
            ]);
        }

        // -------------------------------------
        // S3 Unrestricted Supervised Checklist
        // -------------------------------------
        $s3Supervised = Checklist::create([
            'name' => 'S3 Unrestricted Supervised',
        ]);

        $s3SupervisedItems = [
            'Student has passed their S2 OTS',
            'Recommended - Student has completed the CZVR S3 Knowledge Check review if mark is less than 80%',
            'Has read study material as per page 20 https://czvr.ca/storage/files/uploads/1713054501.pdf',
            'Has completed VATCAN S3 exam',
            'Student successfully completes the training goals outlined in APP.CYYJ.LP1 sweatbox',
            'Student successfully completes the training goals outlined in DEP.CYVR.LP4 sweatbox',
            'Student has been familiarized with Comox (CYQQ_APP) airspace and procedures',
        ];

        foreach ($s3SupervisedItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s3Supervised->id,
                'item' => $item,
            ]);
        }

        // -------------------------------
        // S3 Unrestricted Solo Checklist
        // -------------------------------
        $s3Solo = Checklist::create([
            'name' => 'S3 Unrestricted Solo',
        ]);

        $s3SoloItems = [
            'Student has met the minimum competencies for Victoria Terminal - Supervised',
            'Student successfully completes the training goals outlined in APP.CYYJ.LP2 sweatbox',
            'Student demonstrates an understanding of the CYVR_APP SOPs',
            'Recommended - Student has completed at least 5 hours of network experience on CYYJ_APP with a mentor',
        ];

        foreach ($s3SoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s3Solo->id,
                'item' => $item,
            ]);
        }

        // --------------------------------------
        // S3 T2 DEP Supervised (CYVR) Checklist
        // --------------------------------------
        $s3t2DepSupervised = Checklist::create([
            'name' => 'S3 T2 DEP Supervised (CYVR)',
        ]);

        $s3t2DepSupervisedItems = [
            'Student has met the minimum competencies for Victoria Terminal - Solo',
            'Student has achieved their S2 Tier 2 endorsement',
            'Recommended - Student has accumulated at least 10 hours of network experience on CYYJ_APP',
            'Student has read the recommended study material page 21 https://czvr.ca/storage/files/uploads/1713054501.pdf',
            'Student has passed the CZVR Tier 2 Terminal Endorsement Exam',
            'Student successfully completes the training goals outlined in DEP.CYVR.LP3',
        ];

        foreach ($s3t2DepSupervisedItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s3t2DepSupervised->id,
                'item' => $item,
            ]);
        }

        // --------------------------------------
        // S3 T2 TML Supervised (CYVR) Checklist
        // --------------------------------------
        $s3t2TmlSupervised = Checklist::create([
            'name' => 'S3 T2 TML Supervised (CYVR)',
        ]);

        $s3t2TmlSupervisedItems = [
            'Student has met the minimum competencies for Tier 2 - Vancouver Departure - Supervised',
            'Read recommended study material page 21 https://czvr.ca/storage/files/uploads/1713054501.pdf',
            'Student successfully completes the training goals outlined in DEP.CYVR.LP5 sweatbox',
            'One of the two options: 
                - Student successfully completes the training goals outlined in APP.CYVR.LP1 sweatbox 
                - Student has attempted APP.CYVR.LP2 sweatbox and been debriefed 
                - Student has completed the training goals outlined in APP.CYVR.LP2
                (With this approach, the instructor should ideally run the session a couple of times to ensure the student has a strong understanding of methods of resolving conflicts. This approach is recommended for stronger students)',
            'Recommended - Student has completed at least 5 hours of network experience on CYVR_DEP with a mentor',
        ];

        foreach ($s3t2TmlSupervisedItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s3t2TmlSupervised->id,
                'item' => $item,
            ]);
        }

        // --------------------------------
        // S3 T2 TML Solo (CYVR) Checklist
        // --------------------------------
        $s3t2TmlSolo = Checklist::create([
            'name' => 'S3 T2 TML Solo (CYVR)',
        ]);

        $s3t2TmlSoloItems = [
            'Student has met the minimum competencies for Tier 2 - Vancouver Terminal/Arrival - Supervised',
            'Recommended - Student has accumulated at least 10 hours of network experience on CYVR_APP or DEP with a mentor',
            'Student successfully completes the training goals outlined in APP.CYVR.LP2 sweatbox',
            'Student successfully completes the majority of training goals outlined in APP.CYVR.LP4 sweatbox',
            'Recommended - Student successfully completes the training goals outlined in one or both of the APP.CYVR.LP3 and/or LP3B sweatboxes',
            'Students with a CYVR_APP solo are also authorized to control CYYJ_APP solo',
        ];

        foreach ($s3t2TmlSoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $s3t2TmlSolo->id,
                'item' => $item,
            ]);
        }

        // -------------------------------
        // C1 Centre Supervised Checklist
        // -------------------------------
        $c1CentreSupervised = Checklist::create([
            'name' => 'C1 Centre Supervised',
        ]);

        $c1CentreSupervisedItems = [
            'Student has passed S3 OTS',
            'Read recommended study material as per page 24 https://czvr.ca/storage/files/uploads/1713054501.pdf',
            'Student has achieved their S3 Tier 2 endorsement',
            'Student has completed VATCAN C1 exam',
            'Student has passed the CZVR Tier 2 Enroute Endorsement Exam',
            'Student successfully completes the training goals outlined in CTR.CYVR.LP1 sweatbox',
            'Student successfully completes the training goals outlined in CTR.CYVR.LP2 sweatbox',
            'Student demonstrates an understanding of items covered in the CYVR_CTR SOPs',
            'Recommended - Student has spent at least 3 hours monitoring and watching CZVR CTR controllers as an observer',
        ];

        foreach ($c1CentreSupervisedItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $c1CentreSupervised->id,
                'item' => $item,
            ]);
        }

        // -------------------------
        // C1 Centre Solo Checklist
        // -------------------------
        $c1CentreSolo = Checklist::create([
            'name' => 'C1 Centre Solo',
        ]);

        $c1CentreSoloItems = [
            'Student has met the minimum competencies for Centre - Supervised',
            'Recommended - Student has accumulated at least 5 hours of network experience as a centre controller while being mentored',
            'Student successfully completes the training goals outlined in CTR.CYVR.LP3',
            'Student has demonstrated a working knowledge of the CZVR/KZSE LOA, CZVR/PAZA LOA, and CZVR/CZEG LOA and their contents with relation to CTR operations',
            'The student has demonstrated a working knowledge of the recommended procedures with KZAK as outlined in the SOP',
            'Recommended - Student has attempted one of the reduced workload CTR.CYVR.LP4 sweatboxes (LP4E and/or LP4W) and has been debriefed',
        ];

        foreach ($c1CentreSoloItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $c1CentreSolo->id,
                'item' => $item,
            ]);
        }

        // ----------------------------
        // T2 FSS Supervised Checklist
        // ----------------------------
        $t2FssSupervised = Checklist::create([
            'name' => 'T2 FSS Supervised',
        ]);

        $t2fssItems = [
            'Student has met the minimum competencies for Unrestricted Tower – Solo',
            'Student has passed the FSS Tier 2 Endorsement Exam',
            'Student understands the differences between FSS and TWR',
            'Student demonstrates an understanding of FSS procedures',
            'Relaying IFR clearances',
            'Traffic advisories',
            'Weather advisories',
            'VFR traffic phraseology',
            'Ground advisory phraseology',
            'Student has completed at least one sweatbox session on a FSS position',
        ];

        foreach ($t2fssItems as $item) {
            ChecklistItem::create([
                'checklist_id' => $t2FssSupervised->id,
                'item' => $item,
            ]);
        }
    }
}
