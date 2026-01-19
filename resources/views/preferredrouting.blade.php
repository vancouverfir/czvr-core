@extends('layouts.master')
@section('title', 'Preferred Routing - Vancouver FIR')
@section('description', 'Vancouver FIR preferred routing')
@section('content')

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --vfir-blue: #4da3ff;
    --card-bg: rgba(255,255,255,0.04);
    --card-border: rgba(255,255,255,0.08);
    --star-color: #6CC24A;
}

#runwayAccordionTo .card {
    background: transparent;
    border: none;
    box-shadow: none;
    margin-bottom: 0;
}

#runwayAccordionTo .card-header {
    padding: 0;
    border: none;
    background: transparent;
}

#runwayAccordionTo .btn-link {
    color: #6CC24A;
    font-weight: 600;
    padding: 0.5rem 0;
    text-decoration: none;
}

#runwayAccordionTo .btn-link:hover {
    text-decoration: underline;
}

#runwayAccordionTo .card-body {
    padding-left: 1rem;
    padding-top: 0.25rem;
    padding-bottom: 0.5rem;
    background: transparent;
}

h1 {
    font-weight: 700;
}

h3 {
    font-weight: 700;
    margin-bottom: 0.75rem;
    position: relative;
    padding-bottom: 0.25rem;
}

h3::after {
    content: '';
    display: block;
    width: 50px;
    height: 3px;
    background-color: var(--vfir-blue);
    margin-top: 0.25rem;
    border-radius: 2px;
}

h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

hr {
    border: none;
    height: 1px;
    background: linear-gradient(to right, transparent, #ffffff40, transparent);
}

.airac-note {
    background: rgba(255,255,255,0.05);
    border-left: 4px solid var(--vfir-blue);
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.route-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.route-list {
    list-style: none;
    padding-left: 0;
}

.route-list li {
    margin-bottom: 0.4rem;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 0.9rem;
}

.route-icao {
    color: #6CC24A;
}

.route-number {
    color: var(--star-color);
}

.accordion .card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.accordion .btn-link {
    font-weight: 600;
    color: #6CC24A;
    padding: 0.75rem 1rem;
    text-decoration: none;
    display: block;
    width: 100%;
    text-align: left;
}

.accordion .btn-link:hover {
    text-decoration: none;
}

.accordion .card-body li {
    margin-left: 0.5rem;
}

.nav-tabs {
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

.nav-tabs .nav-link {
    color: #ffffff;
    font-weight: 600;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: #6CC24A;
    border-color: transparent;
}

.tab-pane {
    padding-top: 1rem;
}
</style>

<div class="container py-4">
    <h1 class="blue-text">Preferred Routing</h1>
    <hr>

    <div class="airac-note">
        <b class="blue-text">#</b> STAR versions vary by AIRAC cycle — always refer to the latest charts.
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="airportTabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="cyvr-tab" data-toggle="tab" href="#CYVR" role="tab" aria-controls="CYVR" aria-selected="true">CYVR</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="cyyj-tab" data-toggle="tab" href="#CYYJ" role="tab" aria-controls="CYYJ" aria-selected="false">CYYJ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="cylw-tab" data-toggle="tab" href="#CYLW" role="tab" aria-controls="CYLW" aria-selected="false">CYLW</a>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="airportTabContent">

        <!-- CYVR -->
        <div class="tab-pane show active" id="CYVR" role="tabpanel" aria-labelledby="cyvr-tab">
            <div class="route-card">
                <h3>CYVR</h3>
                <div class="row">
                    <div class="col">
                        <h4>From CYVR</h4>
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

                    <div class="col">
                        <h4>To CYVR</h4>
                        <div id="runwayAccordionTo" class="accordion">
                            <!-- 08L / 08R -->
                            <div class="card-header p-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse08" aria-expanded="true">
                                    08L / 08R
                                </button>
                            </div>
                            <div id="collapse08" class="collapse show" data-parent="#runwayAccordionTo">
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

                            <!-- 26L / 26R -->
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse26" aria-expanded="false">
                                        26L / 26R
                                    </button>
                                </div>
                                <div id="collapse26" class="collapse" data-parent="#runwayAccordionTo">
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
        <div class="tab-pane fade" id="CYYJ" role="tabpanel" aria-labelledby="cyyj-tab">
            <div class="route-card">
                <h3>CYYJ</h3>
                <div class="row">
                    <div class="col">
                        <h4>From CYYJ</h4>
                        <ul class="route-list">
                            <li><span class="route-icao">CYLW</span> - SQURL YDC PIGLU<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYYC</span> - SQURL DAPED PETLI MENBO Q983 NORET IGVEP<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYEG</span> - SQURL DAPED PETLI ROMRA Q949 ELLKS ELKKS<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYXS</span> - CASDY [ABOVE FL180] (ODD ALT)</li>
                            <li><span class="route-icao">KSEA</span> - JIGEB MARNR<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">KPDX</span> - SEA HELNS KRATR<span class="route-number">#</span> (ODD ALT)</li>
                        </ul>
                    </div>
                    <div class="col">
                        <h4>To CYYJ</h4>
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
        <div class="tab-pane fade" id="CYLW" role="tabpanel" aria-labelledby="cylw-tab">
            <div class="route-card">
                <h3>CYLW</h3>
                <div class="row">
                    <div class="col">
                        <h4>From CYLW</h4>
                        <ul class="route-list">
                            <li><span class="route-icao">CYYJ</span> - MERYT BOOTH APASS<span class="route-number">#</span> (EVEN ALT)</li>
                            <li><span class="route-icao">CYYC</span> - WHATS MENBO Q983 IGVEP IGVEP<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYEG</span> - ROMRA Q949 ELLKS ELLKS<span class="route-number">#</span> (ODD ALT)</li>
                            <li><span class="route-icao">CYXS</span> - KEINN APASS<span class="route-number">#</span> [ABOVE FL180] (EVEN ALT)</li>
                            <li><span class="route-icao">KSEA</span> - MERYT JAKSN GLASR<span class="route-number">#</span> (EVEN ALT)</li>
                        </ul>
                    </div>
                    <div class="col">
                        <h4>To CYLW</h4>
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
