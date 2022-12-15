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
            <a class="nav-link active" id="cywg-tab" data-toggle="tab" href="#cywg" role="tab" aria-controls="cywg" aria-selected="true">Vancouver (CYVR)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cypg-tab" data-toggle="tab" href="#cypg" role="tab" aria-controls="cypg" aria-selected="false">Victoria (CYYJ)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxe-tab" data-toggle="tab" href="#cyxe" role="tab" aria-controls="cyxe" aria-selected="false">Kelowna (CYLW)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqt-tab" data-toggle="tab" href="#cyqt" role="tab" aria-controls="cyqt" aria-selected="false">Prince George (CYXS)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqr-tab" data-toggle="tab" href="#cyqr" role="tab" aria-controls="cyqr" aria-selected="false">Abbotsford (CYXX)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cymj-tab" data-toggle="tab" href="#cymj" role="tab" aria-controls="cymj" aria-selected="false">Comox (CYQQ)</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="cywg" role="tabpanel" aria-labelledby="cywg"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYVR') == true)
                    <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYWG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYVR')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYVR')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYVR')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Vancouver International Airport is the busiest airport within the FIR on VATSIM and in real life by passenger volume. The airport has won the SkyTrax award for Best North American Airport 12 years in a row. YVR features 2 main parallel runways, one shorter crosswind runway, and a floatplane terminal on the river adjacent to the airport.</h4>
            <hr>
                <h2 class="font-weight-bold blue-text">Scenery</h2>
                    <h4>FSDreamTean - for FSX and P3D</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target=”_blank” href="https://www.fsdreamteam.com/products_cyvr.html"class="btn btn-primary">View More</a>
                <br></br>
                    <h4>FSimStudios - For MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://www.fsdreamteam.com/products_cyvr2_msfs.html"class="btn btn-primary">View More</a>
                <br></br>
                <h4>Project YVR - for MSFS</h4>
                    <h5>Freeware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/29246/project-yvr-vancouver-international-airport"class="btn btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cypg" role="tabpanel" aria-labelledby="cypg"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYYJ') == true)
                <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYPG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYYJ')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYYJ')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYYJ')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Victoria International Airport is located just north of the province’s capital of Victoria, and it is Vancouver Island’s largest airport both in terms of movements and passenger volume. The airport sees an extensive mix of commercial, general aviation, and floatplane traffic. Despite the short distance, the airport sees a significant number of flights between YVR and YYJ both on the network and in real life.</h4>
            <hr>
                <h2 class="font-weight-bold blue-text">Scenery</h2>
                <h4>SimAddons - For P3Dv4 and MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>YYJ Victoria Dock - For MSFS</h4>
                    <h5>Freeware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/24311/cyyj-victoria-dock"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>Victoria CYYJ Improvements</h4>
                    <h5>Freeware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/15496/victoria-cyyj-improvements  "class="btn btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cyxe" role="tabpanel" aria-labelledby="cyxe"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYLW') == true)
                <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYXE" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYLW')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corners">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYLW')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYLW')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Kelowna International Airport is located in the okanagan valley and is Canada’s 10th busiest airport by passenger volume. The surrounding scenery makes for a stunning VFR flight and incredible IFR approaches. The airport is only 30-60 minutes away from major airports such as YVR, YYC, YEG, and SEA, making it an attractive destination for many VATSIM pilots.</h4>
            <hr>
                <h2 class="font-weight-bold blue-text">Scenery</h2>
                <h4>SimAddons - For P3Dv4 and MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>FSimStudios - For MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://www.fsimstudios.com/product/fsimstudios-kelowna-international-airport-cylw-msfs"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>CYLW Kelowna, British Columbia</h4>
                    <h5>Freeware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/1257/airport-cylw-kelowna" class="btn btn-primary">View More</a>
                <h4>CYLW Waterfront</h4>
                    <h5>Freeware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/17403/cylw-dock"class="btn btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cyqt" role="tabpanel" aria-labelledby="cyqt"><br>
            <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYXS') == true)
                <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYQT" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYXS')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXS')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXS')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Prince George Airport is a mid-sized airport located in northern British Columbia. The airport sees a moderate amount of airline and GA traffic in real life. Being located just an hour or less away from major airports such as YVR, YEG, and YYC it is an attractive destination and departure aerodrome for many pilots on VATSIM.
</h4>
            <hr>
            <h2 class="font-weight-bold blue-text">Scenery</h2>
            <h4>SimAddons - For P3Dv4 and MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/"class="btn btn-primary">View More</a>
        </div>


        <div class="tab-pane fade" id="cyqr" role="tabpanel" aria-labelledby="cyqr"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYXX') == true)
                <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYQR" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYXX')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXX')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXX')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Abbotsford International Airport is the second largest airport located in the lower mainland in terms of physical size. The airport sees extensive general aviation fixed and rotary wing traffic. C130 Hercules transport aircraft are often spotted at the airport as YXX is home to one of only two worldwide C130 heavy maintenance centres. The airport is also home to Canada’s largest airshow that is held in the summer annually both on VATSIM and in real life.</h4>
            <hr>
            <h2 class="font-weight-bold blue-text">Scenery</h2>
                <h4>CYXX - Abbotsford International Airport - for MSFS</h4>
                    <h5>Freeware</h5>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/30091/cyxx-abbotsford-international-airport"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>BC Helicopters - For MSFS</h4>
                    <h5>Freeware</h5>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/36055/bc-helicopters-canada"class="btn btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cymj" role="tabpanel" aria-labelledby="cymj"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYQQ') == true)
                <div class="col">
                        <div class="card"
                             style="width: 25%; float:left; min-height: 100%;">
                            <div class="card-body corner">
                                <div class="CYMJ" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYQQ')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="width: 175%; float: right;">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQQ')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card">
                            <div class="card-body corner">
                                <h3>Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQQ')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <h4>Canadian Forces Base Comox is located on the east coast of Vancouver Island and is primarily operated by the RCAF. However the airport also has scheduled airline traffic as well as a limited amount of general aviation traffic. The airport is one of three in the FIR to operate its own terminal (the ATC position) which covers a large portion of the Sunshine Coast and as a result sees a large number of general aviation overflights as well.</h4>
            <hr>
            <h2 class="font-weight-bold blue-text">Scenery</h2>
            <h4>SimAddons - For P3Dv4 and MSFS</h4>
                    <h5>Payware</h5>
                    <p></p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/"class="btn btn-primary">View More</a>
                    <br></br>
                <h4>CYQQ - 19 Wing Comox - For MSFS</h4>
                    <h5>Freeware</h5>
                    <a style="margin-left: -0.1%" target="_blank" href="https://flightsim.to/file/3656/cyqq-19-wing-comox"class="btn btn-primary">View More</a>
        </div>
    </div>
</div>

@endsection
