@extends('layouts.master')
@section('title', 'Airports - Vancouver FIR')
@section('description', 'Vancouver FIR\'s weather and airports')
@section('content')

<style>
    .CYWG{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .ATIS{
        margin:auto;
    }
</style>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Airports</h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="cyvr-tab" data-toggle="tab" href="#cyvr" role="tab" aria-controls="cyvr" aria-selected="true">Vancouver (CYVR)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyyj-tab" data-toggle="tab" href="#cyyj" role="tab" aria-controls="cyyj" aria-selected="false">Victoria (CYYJ)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cylw-tab" data-toggle="tab" href="#cylw" role="tab" aria-controls="cylw" aria-selected="false">Kelowna (CYLW)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxs-tab" data-toggle="tab" href="#cyxs" role="tab" aria-controls="cyxs" aria-selected="false">Prince George (CYXS)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxx-tab" data-toggle="tab" href="#cyxx" role="tab" aria-controls="cyxx" aria-selected="false">Abbotsford (CYXX)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqq-tab" data-toggle="tab" href="#cyqq" role="tab" aria-controls="cyqq" aria-selected="false">Comox (CYQQ)</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        @php
            $airports = [
                'CYVR' => [
                    'name' => 'Vancouver International Airport',
                    'description' => 'Vancouver International Airport is the busiest airport within the FIR on VATSIM and in real life by passenger volume. The airport has won the SkyTrax award for Best North American Airport 12 years in a row. YVR features 2 main parallel runways, one shorter crosswind runway, and a floatplane terminal on the river adjacent to the airport.',
                    'scenery' => [
                        ['title' => 'FSDreamTeam - for FSX and P3D', 'type' => 'Payware', 'url' => 'https://www.fsdreamteam.com/products_cyvr.html'],
                        ['title' => 'FSimStudios - For MSFS', 'type' => 'Payware', 'url' => 'https://www.fsdreamteam.com/products_cyvr2_msfs.html'],
                        ['title' => 'Project YVR - for MSFS', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/29246/project-yvr-vancouver-international-airport']
                    ]
                ],
                'CYYJ' => [
                    'name' => 'Victoria International Airport',
                    'description' => 'Victoria International Airport is located just north of the province\'s capital of Victoria, and it is Vancouver Island\'s largest airport both in terms of movements and passenger volume. The airport sees an extensive mix of commercial, general aviation, and floatplane traffic. Despite the short distance, the airport sees a significant number of flights between YVR and YYJ both on the network and in real life.',
                    'scenery' => [
                        ['title' => 'SimAddons - For P3Dv4 and MSFS', 'type' => 'Payware', 'url' => 'http://www.simaddons.com/'],
                        ['title' => 'YYJ Victoria Dock - For MSFS', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/24311/cyyj-victoria-dock'],
                        ['title' => 'Victoria CYYJ Improvements', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/15496/victoria-cyyj-improvements']
                    ]
                ],
                'CYLW' => [
                    'name' => 'Kelowna International Airport',
                    'description' => 'Kelowna International Airport is located in the okanagan valley and is Canada\'s 10th busiest airport by passenger volume. The surrounding scenery makes for a stunning VFR flight and incredible IFR approaches. The airport is only 30-60 minutes away from major airports such as YVR, YYC, YEG, and SEA, making it an attractive destination for many VATSIM pilots.',
                    'scenery' => [
                        ['title' => 'SimAddons - For P3Dv4 and MSFS', 'type' => 'Payware', 'url' => 'http://www.simaddons.com/'],
                        ['title' => 'FSimStudios - For MSFS', 'type' => 'Payware', 'url' => 'https://www.fsimstudios.com/product/fsimstudios-kelowna-international-airport-cylw-msfs'],
                        ['title' => 'CYLW Kelowna, British Columbia', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/1257/airport-cylw-kelowna'],
                        ['title' => 'CYLW Waterfront', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/17403/cylw-dock']
                    ]
                ],
                'CYXS' => [
                    'name' => 'Prince George Airport',
                    'description' => 'Prince George Airport is a mid-sized airport located in northern British Columbia. The airport sees a moderate amount of airline and GA traffic in real life. Being located just an hour or less away from major airports such as YVR, YEG, and YYC it is an attractive destination and departure aerodrome for many pilots on VATSIM.',
                    'scenery' => [
                        ['title' => 'SimAddons - For P3Dv4 and MSFS', 'type' => 'Payware', 'url' => 'http://www.simaddons.com/']
                    ]
                ],
                'CYXX' => [
                    'name' => 'Abbotsford International Airport',
                    'description' => 'Abbotsford International Airport is the second largest airport located in the lower mainland in terms of physical size. The airport sees extensive general aviation fixed and rotary wing traffic. C130 Hercules transport aircraft are often spotted at the airport as YXX is home to one of only two worldwide C130 heavy maintenance centres. The airport is also home to Canada\'s largest airshow that is held in the summer annually both on VATSIM and in real life.',
                    'scenery' => [
                        ['title' => 'CYXX - Abbotsford International Airport - for MSFS', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/30091/cyxx-abbotsford-international-airport'],
                        ['title' => 'BC Helicopters - For MSFS', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/36055/bc-helicopters-canada']
                    ]
                ],
                'CYQQ' => [
                    'name' => 'Canadian Forces Base Comox',
                    'description' => 'Canadian Forces Base Comox is located on the east coast of Vancouver Island and is primarily operated by the RCAF. However the airport also has scheduled airline traffic as well as a limited amount of general aviation traffic. The airport is one of three in the FIR to operate its own terminal (the ATC position) which covers a large portion of the Sunshine Coast and as a result sees a large number of general aviation overflights as well.',
                    'scenery' => [
                        ['title' => 'SimAddons - For P3Dv4 and MSFS', 'type' => 'Payware', 'url' => 'http://www.simaddons.com/'],
                        ['title' => 'CYQQ - 19 Wing Comox - For MSFS', 'type' => 'Freeware', 'url' => 'https://flightsim.to/file/3656/cyqq-19-wing-comox']
                    ]
                ]
            ];
            $icaoCodes = ['CYVR', 'CYYJ', 'CYLW', 'CYXS', 'CYXX', 'CYQQ'];
        @endphp

        @foreach ($icaoCodes as $index => $icao)
            @php
                // Fetch data ONCE per airport
                $atisLetter = \App\Classes\WeatherHelper::getAtisLetter($icao);
                $atisText = \App\Classes\WeatherHelper::getAtis($icao);
                $hasAtis = $atisLetter != null;
            @endphp

            <div class="tab-pane fade @if($index === 0) show active @endif" id="{{ strtolower($icao) }}" role="tabpanel" aria-labelledby="{{ strtolower($icao) }}-tab"><br>
                <div class="row">
                    @if($hasAtis)
                    <div class="col">
                        <div class="card" style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="{{ $icao }}" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{ $atisLetter }}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card" style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{ $atisText }}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{ $atisText }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <br>
                <h4>{{ $airports[$icao]['description'] }}</h4>
                <hr>
                <h2 class="font-weight-bold blue-text">Scenery</h2>
                @foreach ($airports[$icao]['scenery'] as $scenery)
                    <h4>{{ $scenery['title'] }}</h4>
                    <h5>{{ $scenery['type'] }}</h5>
                    <a style="margin-left: -0.1%" target="_blank" href="{{ $scenery['url'] }}" class="btn btn-primary">View More</a>
                    <br></br>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

@endsection
