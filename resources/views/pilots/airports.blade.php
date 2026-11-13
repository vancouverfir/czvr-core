@extends('layouts.master')
@section('title', 'Airports - Vancouver FIR')
@section('description', 'Vancouver FIR\'s weather and airports')
@push('styles')
    <link href="{{ asset('css/pages/pilots.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="container py-4 airport-page">
    <div class="page-heading">
        <h1 class="blue-text">Airport guide</h1>
        <p class="mb-0">Weather, airport information, and recommended scenery for Vancouver FIR's primary airports!</p>
    </div>

    <ul class="nav nav-tabs page-tabs" id="myTab" role="tablist" aria-label="Vancouver FIR airports">
        <li class="nav-item">
            <a class="nav-link active" id="cyvr-tab" data-bs-toggle="tab" href="#cyvr" role="tab" aria-controls="cyvr" aria-selected="true">Vancouver (CYVR)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyyj-tab" data-bs-toggle="tab" href="#cyyj" role="tab" aria-controls="cyyj" aria-selected="false">Victoria (CYYJ)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cylw-tab" data-bs-toggle="tab" href="#cylw" role="tab" aria-controls="cylw" aria-selected="false">Kelowna (CYLW)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxs-tab" data-bs-toggle="tab" href="#cyxs" role="tab" aria-controls="cyxs" aria-selected="false">Prince George (CYXS)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxx-tab" data-bs-toggle="tab" href="#cyxx" role="tab" aria-controls="cyxx" aria-selected="false">Abbotsford (CYXX)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqq-tab" data-bs-toggle="tab" href="#cyqq" role="tab" aria-controls="cyqq" aria-selected="false">Comox (CYQQ)</a>
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
                $atisLetter = $airportWeather[$icao]['atis_letter'];
                $atisText = $airportWeather[$icao]['text'];
                $hasAtis = $atisLetter !== null;
            @endphp

            <div class="tab-pane fade @if($index === 0) show active @endif airport-panel" id="{{ strtolower($icao) }}" role="tabpanel" aria-labelledby="{{ strtolower($icao) }}-tab">
                <div class="airport-heading">
                    <h2>{{ $airports[$icao]['name'] }}</h2>
                    <span class="airport-code">{{ $icao }}</span>
                </div>

                <section class="airport-weather {{ $hasAtis ? 'has-atis' : '' }}" aria-label="Current weather">
                    @if($hasAtis)
                        <div class="airport-atis-letter">
                            <span>ATIS</span>
                            <strong>{{ $atisLetter }}</strong>
                        </div>
                    @endif
                    <div class="airport-weather-copy">
                        <h3>{{ $hasAtis ? 'Current ATIS' : 'Current METAR' }}</h3>
                        <p>{{ $atisText }}</p>
                    </div>
                </section>

                <p class="airport-description mb-4">{{ $airports[$icao]['description'] }}</p>

                <section aria-labelledby="{{ strtolower($icao) }}-scenery">
                    <h2 class="airport-section-title" id="{{ strtolower($icao) }}-scenery">Recommended scenery</h2>
                    <div class="row g-3">
                        @foreach ($airports[$icao]['scenery'] as $scenery)
                            <div class="col-md-6 col-xl-4">
                                <article class="airport-scenery-card">
                                    <span class="airport-scenery-type">{{ $scenery['type'] }}</span>
                                    <h3>{{ $scenery['title'] }}</h3>
                                    <a target="_blank" rel="noopener noreferrer" href="{{ $scenery['url'] }}" class="btn btn-sm btn-outline-primary">
                                        View scenery <i class="fas fa-arrow-up-right-from-square ms-1"></i>
                                    </a>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @endforeach
    </div>
</div>

@endsection
