@extends('layouts.master')
@section('title', 'Preferred Routing - Vancouver FIR')
@section('description', 'Vancouver FIR preferred routing')
@push('styles')
<style>
    .routing-header {
        max-width: 740px;
    }

    .routing-header h1 {
        font-size: 1.65rem;
        font-weight: 500;
    }

    .routing-header p {
        color: var(--czvr-text-muted);
    }

    .routing-tabs {
        border: 1px solid var(--czvr-border);
        border-radius: 0.7rem;
        display: flex;
        gap: 0.25rem;
        overflow-x: auto;
        padding: 0.3rem;
    }

    .routing-tabs .nav-link {
        border: 0;
        border-radius: 0.45rem;
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
        padding: 0.55rem 0.8rem;
        white-space: nowrap;
    }

    .routing-tabs .nav-link:hover,
    .routing-tabs .nav-link:focus {
        background: var(--czvr-surface-muted);
        color: var(--czvr-green-dark);
    }

    .routing-tabs .nav-link.active {
        background: var(--czvr-green-soft);
        color: var(--czvr-green-dark);
    }

    .airac-note {
        align-items: center;
        background: var(--czvr-surface-muted);
        border: 1px solid var(--czvr-border);
        border-radius: 0.65rem;
        color: var(--czvr-text-muted);
        display: flex;
        font-size: var(--czvr-font-size-base);
        gap: 0.65rem;
        margin: 1rem 0 1.5rem;
        padding: 0.7rem 0.85rem;
    }

    .airac-symbol {
        align-items: center;
        background: var(--czvr-green-soft);
        border-radius: 50%;
        color: var(--czvr-green-dark);
        display: inline-flex;
        flex: 0 0 1.65rem;
        font-weight: 600;
        height: 1.65rem;
        justify-content: center;
    }

    .routing-panel {
        padding-top: 1.5rem;
    }

    .route-card {
        background: var(--czvr-white);
        border: 1px solid var(--czvr-border);
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .route-card-heading {
        align-items: center;
        border-bottom: 1px solid var(--czvr-border);
        display: flex;
        justify-content: space-between;
        padding: 0.85rem 1rem;
    }

    .route-card-heading h2 {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
    }

    .route-card-heading span {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
    }

    .route-column {
        padding: 1rem;
    }

    .route-column + .route-column {
        border-left: 1px solid var(--czvr-border);
    }

    .route-column h3 {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
        font-weight: 600;
        letter-spacing: 0.08em;
        margin: 0 0 0.65rem;
        text-transform: uppercase;
    }

    .route-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .route-list li {
        border-top: 1px solid var(--czvr-border);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: var(--czvr-font-size-base);
        line-height: 1.55;
        padding: 0.58rem 0;
    }

    .route-list li:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .route-icao {
        color: var(--czvr-green-dark);
        font-weight: 600;
    }

    .route-number {
        color: var(--czvr-green-dark);
        font-weight: 600;
    }

    .runway-picker {
        border: 1px solid var(--czvr-border);
        border-radius: 0.55rem;
        margin-bottom: 0.65rem;
        overflow: hidden;
    }

    .runway-picker .card-header {
        background: var(--czvr-surface-muted);
        border: 0;
        padding: 0;
    }

    .runway-picker .btn-link {
        color: var(--czvr-green-dark);
        display: flex;
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
        justify-content: space-between;
        padding: 0.65rem 0.75rem;
        text-align: left;
        text-decoration: none;
        width: 100%;
    }

    .runway-picker .btn-link::after {
        content: '+';
        font-size: 1rem;
        font-weight: 400;
        line-height: 1;
    }

    .runway-picker .btn-link[aria-expanded="true"]::after {
        content: '−';
    }

    .runway-picker .card-body {
        padding: 0.8rem 0.75rem 0.2rem;
    }

    @media (max-width: 767.98px) {
        .route-column + .route-column {
            border-left: 0;
            border-top: 1px solid var(--czvr-border);
        }
    }
</style>
@endpush

@section('content')

<div class="container py-4 routing-page">
    <div class="routing-header">
        <h1 class="blue-text mb-2">Preferred routing</h1>
        <p class="mb-0">Common routes between Vancouver FIR airports and nearby destinations!</p>
    </div>

    <div class="airac-note">
        <span class="airac-symbol">#</span>
        <span>STAR versions vary by AIRAC cycle! Always refer to the latest charts!</span>
    </div>

    <ul class="nav routing-tabs" id="airportTabs" role="tablist" aria-label="Preferred routes by airport">
      <li class="nav-item">
        <a class="nav-link active" id="cyvr-tab" data-bs-toggle="tab" href="#CYVR" role="tab" aria-controls="CYVR" aria-selected="true">Vancouver (CYVR)</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="cyyj-tab" data-bs-toggle="tab" href="#CYYJ" role="tab" aria-controls="CYYJ" aria-selected="false">Victoria (CYYJ)</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="cylw-tab" data-bs-toggle="tab" href="#CYLW" role="tab" aria-controls="CYLW" aria-selected="false">Kelowna (CYLW)</a>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="airportTabContent">

        <!-- CYVR -->
        <div class="tab-pane show active routing-panel" id="CYVR" role="tabpanel" aria-labelledby="cyvr-tab">
            <div class="route-card">
                <div class="route-card-heading">
                    <h2>Vancouver International</h2>
                    <span>CYVR</span>
                </div>
                <div class="row g-0">
                    <div class="col-md-6 route-column">
                        <h3>Departing CYVR</h3>
                        <ul class="route-list">
                            <li><span class="route-icao">CYYJ</span> - AMEBO APASS<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYCD</span> - YVR V342 ARMAC YCD (EVEN ALT)</li>
                            <li><span class="route-icao">CYLW</span> - JANEK SEKAB SEKAB<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYXS</span> - GARRE SEATN YWL YXS (EVEN ALT)</li>
                            <li><span class="route-icao">CYEG</span> - VIDRI Q949 ELLKS ELLKS<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYYC</span> - DAPED PETLI MENBO Q983 NORET IGVEP<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">PAKT</span> - TREEL V317 ITMAV T719 UQQ YZT Q902 ANN (EVEN ALT)</li>
                            <li><span class="route-icao">KSEA (RNAV Jet)</span> - YVR ROESH MARNR MARNR<span class="route-number">#</span> (11000, 13000 or 15000)</li>
                            <li><span class="route-icao">KSEA (Prop/Non-RNAV)</span> - YVR JAWBN<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">KPDX</span> - YVR SEA BUWZO KRATR<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">KSFO</span> - VIXOR ELMAA Q1 ERAVE Q1 ETCHY MLBEC BDEGA<span class="route-number">#</span> (ODD ALT)</li>
                        </ul>
                    </div>

                    <div class="col-md-6 route-column">
                        <h3>Arriving CYVR</h3>
                        <div id="runwayAccordionTo" class="accordion">
                            <!-- 08L / 08R -->
                            <div class="runway-picker">
                                <div class="card-header">
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse08" aria-expanded="true" aria-controls="collapse08">
                                        Runways 08L / 08R
                                    </button>
                                </div>
                                <div id="collapse08" class="collapse show" data-bs-parent="#runwayAccordionTo">
                                    <div class="card-body">
                                        <ul class="route-list">
                                        <li><span class="route-icao">CYYJ</span> - OMVEX ILAND<span class="route-number">#</span> (ODD ALT)</li>
                                        <li><span class="route-icao">CYCD</span> - ILATI MEPMU ILAND<span class="route-number">#</span> (ODD ALT)</li>
                                        <li><span class="route-icao">CYLW</span> - MERYT BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                        <li><span class="route-icao">CYXS</span> - YXS YWL Q800 ELIDI WHSLR<span class="route-number">#</span> (ODD ALT)</li>
                                        <li><span class="route-icao">CYEG</span> - ANDIE Q860 MERYT BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                        <li><span class="route-icao">CYYC</span> - BOTAG Q894 BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                        <li><span class="route-icao">KSEA</span> - PAE GRIZZ<span class="route-number">#</span> (EVEN ALT)</li>
                                        <li><span class="route-icao">KPDX</span> - BTG J1 SEA PAE GRIZZ<span class="route-number">#</span> (EVEN ALT)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 26L / 26R -->
                            <div class="runway-picker">
                                <div class="card-header">
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse26" aria-expanded="false" aria-controls="collapse26">
                                        Runways 26L / 26R
                                    </button>
                                </div>
                                <div id="collapse26" class="collapse" data-bs-parent="#runwayAccordionTo">
                                    <div class="card-body">
                                        <ul class="route-list">
                                            <li><span class="route-icao">CYYJ</span> - VIXOR NALGI DUXUM<span class="route-number">#</span> (ODD ALT)</li>
                                            <li><span class="route-icao">CYCD</span> - ILATI NALGI DUXUM<span class="route-number">#</span> (ODD ALT)</li>
                                            <li><span class="route-icao">CYLW</span> - MERYT BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                            <li><span class="route-icao">CYXS</span> - YXS YWL Q800 ELIDI WHSLR<span class="route-number">#</span> (ODD ALT)</li>
                                            <li><span class="route-icao">CYEG</span> - ANDIE Q860 MERYT BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                            <li><span class="route-icao">CYYC</span> - BOTAG Q894 BOOTH CANUC<span class="route-number">#</span> (EVEN ALT)</li>
                                            <li><span class="route-icao">KSEA</span> - PAE GRIZZ<span class="route-number">#</span> (EVEN ALT)</li>
                                            <li><span class="route-icao">KPDX</span> - BTG J1 SEA PAE GRIZZ<span class="route-number">#</span> (EVEN ALT)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CYYJ -->
        <div class="tab-pane fade routing-panel" id="CYYJ" role="tabpanel" aria-labelledby="cyyj-tab">
            <div class="route-card">
                <div class="route-card-heading">
                    <h2>Victoria International</h2>
                    <span>CYYJ</span>
                </div>
                <div class="row g-0">
                    <div class="col-md-6 route-column">
                        <h3>Departing CYYJ</h3>
                        <ul class="route-list">
                            <li><span class="route-icao">CYLW</span> - SQURL YDC PIGLU<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYYC</span> - SQURL DAPED PETLI MENBO Q983 NORET IGVEP<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYEG</span> - SQURL DAPED PETLI ROMRA Q949 ELLKS ELKKS<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYXS</span> - CASDY [ABOVE FL180] (ODD ALT)</li>
                            <li><span class="route-icao">KSEA</span> - JIGEB MARNR<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">KPDX</span> - SEA HELNS KRATR<span class="route-number">#</span> (ODD ALT)</li>
                        </ul>
                    </div>
                    <div class="col-md-6 route-column">
                        <h3>Arriving CYYJ</h3>
                        <ul class="route-list">
                            <li><span class="route-icao">CYYC</span> - BOTAG Q894 BOOTH APASS<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYEG</span> - ANDIE Q860 MERYT BOOTH APASS<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYXS</span> - KEINN APASS<span class="route-number">#</span> [ABOVE FL180] (EVEN ALT)</li>
                            <li><span class="route-icao">KSEA</span> - ARRIE DISCO DISCO<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">KPDX</span> - BTG OLM DISCO DISCO<span class="route-number">#</span> (EVEN ALT)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- CYLW -->
        <div class="tab-pane fade routing-panel" id="CYLW" role="tabpanel" aria-labelledby="cylw-tab">
            <div class="route-card">
                <div class="route-card-heading">
                    <h2>Kelowna International</h2>
                    <span>CYLW</span>
                </div>
                <div class="row g-0">
                    <div class="col-md-6 route-column">
                        <h3>Departing CYLW</h3>
                        <ul class="route-list">
                            <li><span class="route-icao">CYYJ</span> - MERYT BOOTH APASS<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYYC</span> - WHATS MENBO Q983 IGVEP IGVEP<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYEG</span> - ROMRA Q949 ELLKS ELLKS<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYXS</span> - KEINN APASS<span class="route-number">#</span> [ABOVE FL180] (EVEN ALT)</li>
                            <li><span class="route-icao">KSEA</span> - MERYT JAKSN GLASR<span class="route-number">#</span> (EVEN ALT)</li>
                        </ul>
                    </div>
                    <div class="col-md-6 route-column">
                        <h3>Arriving CYLW</h3>
                        <ul class="route-list">
                            <li><span class="route-icao">CYYC</span> - BOTAG SIMTA BINVO ROBTI NORIP<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYEG</span> - ANDIE NADPI ENDBY HUMEK WTMAN (EVEN ALT)</li>
                            <li><span class="route-icao">CYXS</span> - WTMAN (ODD ALT)</li>
                            <li><span class="route-icao">KSEA</span> - ALPSE YDC PIGLU<span class="route-number">#</span> (ODD ALT)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
