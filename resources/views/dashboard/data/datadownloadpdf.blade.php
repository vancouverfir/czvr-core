<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<p>Gander Oceanic FIR https://ganderoceanic.com</p>
<h2>{{auth()->user()->fullName('FLC')}}</h2>
<h5>Data as of {{\Carbon\Carbon::now()}}.</h5>
<div style="border: 1px solid; padding: 10px;">
    This data has been gathered at the request of {{auth()->user()->fullName('FLC')}} in accordance with the Gander Oceanic FIR Privacy Policy and the European Union GDDPR. For more information, please visit https://ganderoceanic.com/privacy.
</div>
<h5>Basic Data</h5>
<table>
    <thead><td>Attribute</td><td>Data</td></thead>
    <tbody>
    <tr><td>First Name</td><td>{{auth()->user()->fname}}</td></tr>
    <tr><td>Last Name</td><td>{{auth()->user()->lname}}</td></tr>
    <tr><td>CID</td><td>{{auth()->user()->id}}</td></tr>
    <tr><td>Displayed First Name</td><td>{{auth()->user()->display_fname}}</td></tr>
    <tr><td>Display Last Name</td><td>@if (auth()->user()->display_last_name)True @else False @endif</td></tr>
    <tr><td>Display CID Only</td><td>@if (auth()->user()->display_cid_only)True @else False @endif</td></tr>
    <tr><td>Email</td><td>{{auth()->user()->email}}</td></tr>
    <tr><td>Rating</td><td>{{auth()->user()->rating_GRP}} ({{auth()->user()->rating_id}}, {{auth()->user()->rating_short}}, {{auth()->user()->rating_long}})</td></tr>
    <tr><td>VATSIM Registration Date</td><td>{{auth()->user()->reg_date}}</td></tr>
    <tr><td>Region</td><td>{{auth()->user()->region_name}} ({{auth()->user()->region_code}})</td></tr>
    <tr><td>Division</td><td>{{auth()->user()->division_name}} ({{auth()->user()->division_code}})</td></tr>
    <tr><td>Subdivision</td><td>{{auth()->user()->subdivision_name}} ({{auth()->user()->subdivision_code}})</td></tr>
    <tr><td>Permissions</td><td>{{auth()->user()->permissions}}</td></tr>
    <tr><td>Accepted privacy policy</td><td>{{auth()->user()->init}}</td></tr>
    <tr><td>Subscribed to emails</td><td>{{auth()->user()->gdpr_subscribed_emails}}</td></tr>
    <tr><td>Avatar</td><td>{{auth()->user()->avatar}}</td></tr>
    <tr><td>Biography</td><td>{{auth()->user()->biography}}</td></tr>
    </tbody>
</table>
</body>
</html>
