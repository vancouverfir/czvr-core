@extends('layouts.master')
@section('title', 'PDC - Vancouver FIR')
@section('content')

    <div class="container py-4">
        <h1 class="font-weight-bold blue-text"><strong>Pre-Departure Clearance</strong></h1>
        <p>Pre-Departure Clearances in the Vancouver FIR are issued upon request by the pilot or during high volumes of traffic.</p>
        <blockquote style="font-size: 1em">PDC service is currently provided at CYVR, CYYJ, CYXX, CYLW, and CYXS. When available, "PDC Available on request" will be listed in the ATIS NOTAMS.</blockquote></p>
        <hr>
        <h3 class="font-weight-bold blue-text">Requesting a PDC</h3>
        <p>PDCs are issued via private message on all VATSIM pilot clients - pilots may request a PDC via voice or text. The format to a PDC will note similar information to an IFR clearance, as shown in the example below:
        <blockquote style="font-size: 1em">PDC - ACA123 - SQUAWK 1234 - CYVR - A320 - FL310 - JANEK SEKAB SEKAB4 - USE SID YVR2 - DEPARTURE RUNWAY 26L - DESTINATION CYLW - CONTACT CZVR_CTR 133.700 WITH IDENTIFIER 123A - END OF MESSAGE</blockquote></p>
        <p class ="green-text"> You can speed up your PDC request by sending <q>Departure Clearance Request</q> in the frequency chat!</p>
        <p>Pilots should then call ATC when ready, noting the identifier assigned to their flight in the PDC.
        <blockquote style="font-size: 1em">Vancouver Center, ACA123, PDC Identifier 123A.</blockquote></p>
        <h3 class="font-weight-bold blue-text">Issues and Amendments</h3>
	<p>If there is an issue with the flight plan the controller will reply with the following message:</p>
	<blockquote style="font-size: 1em">PDC UNAVAILABLE, REVERT TO STANDARD VOICE PROCEDURES</blockquote>
	<p>If you receive this message, the PDC clearance becomes <u>void</u> and you should contact ATC on the normal frequency.</p>
@endsection
