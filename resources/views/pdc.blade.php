@extends('layouts.master')
@section('title', 'PDC - Vancouver FIR')
@section('content')

    <div class="container py-4">
        <h1 class="font-weight-bold blue-text"><strong>Pre-Departure Clearance</strong></h1>
        <p>Pre-Departure Clearances in the Vancouver FIR are issued upon request by the pilot or during high volumes of traffic.</p>
        <blockquote style="font-size: 1em">PDC service is currently provided <u>only</u> in CYVR and CYYJ. When available, "PDC Available on Request" will be listed in the controller's information.</blockquote></p>
        <hr>
        <h3 class="font-weight-bold blue-text">Requesting a PDC</h3>
        <p>PDCs are issued via private message on all VATSIM pilot clients - pilots may request a PDC via voice or text. The format to a PDC will note similar information to an IFR clearance, as shown in the example below:
        <blockquote style="font-size: 1em">PDC - ACA123 1234 CYVR - A320 - FL310 - JANEK SEKAB SEKAB4 - USE SID YVR2 - DEPARTURE RUNWAY 26L - DESTINATION CYLW - CONTACT CYVR_GND 121.700 WITH IDENTIFIER 999A - END OF MESSAGE</blockquote></p>
        <p class ="content-warning"> If requesting via text please private message the controller with <q>DEPARTURE CLEARANCE REQUEST</p>
        <p>Pilots should then call ATC when ready for push and start, noting the identifier assigned to their flight in the PDC.
        <blockquote style="font-size: 1em">Vancouver Ground, ACA123, PDC Identifier 999A, ready for push and start.</blockquote></p>
        <h3 class="font-weight-bold blue-text">Issues and Amendments</h3>
	<p>If there is an issue with the flight plan (i.e. wrong altitude, non-existant waypoints, or out of date SIDs/STARs) the controller will reply with the following message:</p>
	<blockquote style="font-size: 1em">PDC UNAVAILABLE, REVERT TO STANDARD VOICE PROCEDURES</blockquote>
	<p>If you receive this message, the PDC clearance becomes <u>void</u> and you should contact ATC on the normal frequency.</p>
@endsection
