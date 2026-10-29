@extends('layouts.master')
@section('title', 'VFR - Vancouver FIR')
@section('description', 'VFR charts, procedures, and pilot guidance for the Vancouver FIR')

@push('styles')
    <link href="{{ asset('css/pages/pilots.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="container py-4 vfr-page">
    <header class="page-heading vfr-page-header">
        <h1 class="blue-text mb-2">VFR in CZVR</h1>
        <p class="mb-2">CZVR is home to some of the most beautiful landscapes to navigate visually. With increasing flight sim fidelity and distinct landscapes, CZVR provides easy visual navigation and world class sight seeing.</p>
        <p class="mb-0">Both new and seasoned VFR pilots are welcome! No need to be intimidated, our friendly controllers are happy to help learn when possible!</p>
    </header>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <section class="vfr-card" aria-labelledby="vfr-charts-title">
                <div class="vfr-card-heading">
                    <h2 id="vfr-charts-title">VFR Charts and Procedures</h2>
                    <span>Resources</span>
                </div>
                <div class="vfr-card-body">
                    <p class="vfr-copy">Within the FIR we have been implementing the use of real world VFR procedures. Procedures may be assigned based on destination and traffic. The most up to date of these can be found here:</p>
                    <ul class="vfr-resource-list">
                        <li>
                            <a href="https://imageserver.fltplan.com/legends/VTA_BACK_VANCOUVER.PDF" target="_blank" rel="noopener noreferrer">
                                <i class="far fa-map" aria-hidden="true"></i>
                                <span>VTA Back Vancouver</span>
                                <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        </li>
                        @if ($bcVfr = \App\Models\Publications\AtcResource::bcVfrTerminalProcedures())
                            <li>
                                <a href="{{ $bcVfr->resolvedUrl() }}" target="_blank" rel="noopener noreferrer">
                                    <i class="far fa-file-lines" aria-hidden="true"></i>
                                    <span>Canadian Flight Supplement - BC VFR Terminal Procedures</span>
                                    <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endif
                    </ul>

                    <aside class="page-note vfr-note">
                        <div>
                            <p>It is likely these procedures may be new to many Vatsim pilots. Although the procedures may be enforced it is important not to accept a procedure if you do not understand it.</p>
                            <p>If you do not understand ask the controller for assistance or clarity as needed, we are here to help and learn together. For example, as controller loads permit we may instead assign a heading or altitude to emulate the procedure.</p>
                        </div>
                    </aside>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="vfr-card" aria-labelledby="vfr-fss-title">
                <div class="vfr-card-heading">
                    <h2 id="vfr-fss-title">FSS for Pilots</h2>
                    <span>Handbook</span>
                </div>
                <div class="vfr-card-body">
                    <ul class="vfr-resource-list">
                        <li>
                            <a href="{{ asset('storage/files/fss-handbook.pdf') }}" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-headset" aria-hidden="true"></i>
                                <span>Flight Who? - Introduction to FSS for Pilots</span>
                                <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <section class="vfr-card" aria-labelledby="vfr-tower-title">
        <div class="vfr-card-heading">
            <h2 id="vfr-tower-title">Inner/Outer Tower</h2>
            <span>Operations</span>
        </div>
        <div class="vfr-card-body border-bottom">
            <p class="vfr-copy mb-0">Many of the following procedures indicated points to report/frequency change from inner to outer tower.</p>
        </div>
        <div class="row g-0">
            <div class="col-md-4 vfr-tower-column">
                <h3>Both online</h3>
                <p>In the case both inner and outer tower are online no modifications are needed.</p>
            </div>
            <div class="col-md-4 vfr-tower-column">
                <h3>Inner tower only</h3>
                <p>However, if only inner tower is online this controller will inherit the responsibilities of outer tower. In this case, simply follow the procedure with out frequency change and reporting as outlined to inner tower.</p>
            </div>
            <div class="col-md-4 vfr-tower-column">
                <h3>Callsign</h3>
                <p>Inner tower will always be the standard tower callsign (ex: CYVR_TWR, CYYJ_TWR, CYVR_1_TWR, etc.)</p>
            </div>
        </div>
    </section>
</div>

@endsection
