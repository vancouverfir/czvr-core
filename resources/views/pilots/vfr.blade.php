@extends('layouts.master')
@section('title', 'VFR - Vancouver FIR')
@section('description', 'VFR charts, procedures, and pilot guidance for the Vancouver FIR')

@push('styles')
<style>
    .vfr-page-header p {
        max-width: 760px;
    }

    .vfr-page-header h1 {
        font-size: 1.65rem;
        font-weight: 500;
    }

    .vfr-page-header p,
    .vfr-copy {
        color: var(--czvr-text);
        line-height: 1.7;
    }

    .vfr-card {
        background: var(--czvr-white);
        border: 1px solid var(--czvr-border);
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .vfr-card-heading {
        align-items: center;
        border-bottom: 1px solid var(--czvr-border);
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        padding: 0.85rem 1rem;
    }

    .vfr-card-heading h2 {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
    }

    .vfr-card-heading span {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
    }

    .vfr-card-body {
        padding: 1rem;
    }

    .vfr-card-body > p {
        margin-bottom: 0.85rem;
    }

    .vfr-resource-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .vfr-resource-list li + li {
        border-top: 1px solid var(--czvr-border);
    }

    .vfr-resource-list a {
        align-items: center;
        color: var(--czvr-text);
        display: flex;
        font-size: var(--czvr-font-size-base);
        gap: 0.65rem;
        padding: 0.7rem 0;
        text-decoration: none;
    }

    .vfr-resource-list li:first-child a {
        padding-top: 0;
    }

    .vfr-resource-list li:last-child a {
        padding-bottom: 0;
    }

    .vfr-resource-list a:hover,
    .vfr-resource-list a:focus {
        color: var(--czvr-green-dark);
    }

    .vfr-resource-list a > i:first-child {
        color: var(--czvr-green-dark);
        text-align: center;
        width: 1rem;
    }

    .vfr-resource-list .fa-arrow-up-right-from-square {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
        margin-left: auto;
    }

    .vfr-note {
        align-items: flex-start;
        display: flex;
        font-size: var(--czvr-font-size-base);
        gap: 0.7rem;
        line-height: 1.65;
        margin-top: 1rem;
    }

    .vfr-note p {
        font-size: inherit !important;
        margin: 0;
    }

    .vfr-note p + p {
        margin-top: 0.35rem;
    }

    .vfr-tower-column {
        padding: 1rem;
    }

    .vfr-tower-column + .vfr-tower-column {
        border-left: 1px solid var(--czvr-border);
    }

    .vfr-tower-column h3 {
        color: var(--czvr-green-dark);
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
        margin: 0 0 0.55rem;
    }

    .vfr-tower-column p {
        color: var(--czvr-text);
        font-size: var(--czvr-font-size-base) !important;
        line-height: 1.65;
        margin: 0;
    }

    @media (max-width: 767.98px) {
        .vfr-tower-column + .vfr-tower-column {
            border-left: 0;
            border-top: 1px solid var(--czvr-border);
        }
    }
</style>
@endpush

@section('content')

<div class="container py-4 vfr-page">
    <header class="page-heading vfr-page-header">
        <h1 class="blue-text mb-2">VFR in CZVR</h1>
        <p class="mb-2">CZVR is home to some of the most beautiful landscapes to navigate visually. With increasing flight sim fidelity and distinct landscapes, CZVR provides easy visual navigation and world class sight seeing.</p>
        <p class="mb-0">Both new and seasoned VFR pilots are welcome. No need to be intimidated, our friendly controllers are happy to help learn when possible.</p>
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
                        <li>
                            <a href="{{ \App\Support\Publications\BcVfrTerminalProcedures::currentUrl() }}" target="_blank" rel="noopener noreferrer">
                                <i class="far fa-file-lines" aria-hidden="true"></i>
                                <span>Canadian Flight Supplement - BC VFR Terminal Procedures</span>
                                <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        </li>
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
