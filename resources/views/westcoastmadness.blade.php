@extends('layouts.master')
@section('title', 'West Coast Madness - Vancouver FIR')
@section('content')

<div class="container" style="margin-top: 20px; margin-bottom: 30px;">
    <div class="card">
        <div class="container" style="max-width: 1070px; padding-top: 30px; margin: 0 auto;">
            <img src="https://czvr.ca/storage/files/uploads/1731845844.gif" alt="West Coast Madness" class="card-img-top img-fluid" style="height: auto;">
        </div>

        <div class="card-body text-center">
            <h1 class="font-weight-bold blue-text">🌟 West Coast Madness - Vancouver FIR 🌟</h1>
            <hr class="bg-light" style="margin: 20px 15px;">
            <p class="card-text-light">📚 The Vancouver FIR is proud to present you <b>West Coast Madness!</b></p>
            <p class="card-text-light">This unique event, a first for both ZVR and Vatcan, will feature full ATC coverage around the FIR for 12h straight... You heard that right! <b>From 10am local to 10pm local (18z-06z)</b>, ZVR will fully staff CYVR as well as other variable top-down services at other locations around the FIR!</p>
            <p class="card-text-light">If that wasn't enough, there will also be <b>special routes featuring ZSE, ZLC and ZEG</b> with guaranteed staffing spread out throughout the day! See below for the schedule.</p>
            <p class="card-text-light">But wait, we're not done yet! As every esteemed event, West Coast Madness will host many giveaways for all attendants. Stay tuned!</p><hr>

            <hr class="bg-light" style="margin: 20px 15px;">

            <!-- Pilot Briefing & Event Details -->
            <div style="background-color: #333; color: #ddd; padding: 25px;  margin-top: 30px;">
                <h2 class="card-text-light" style="font-weight: bold; font-size: 1.5em; color: #ddd;">📖 Pilot Briefing & Event Details</h2>
                <p class="card-text-light" style="color: #ddd;">Make sure you're prepared! Check out the official pilot briefing for all the key details, procedures, and routes here<br>
                    <a href="https://czvr.ca/storage/files/uploads/1739018432.pdf" target="_blank" style="font-weight: bold; color: #00B0F0; text-decoration: none; border-bottom: 2px solid #00B0F0; transition: all 0.3s ease;">Official Pilot Briefing</a>
                </p>
            </div>

            <hr class="bg-light" style="margin: 20px 15px;">

            <h2 class="card-text-light text-center" style="font-weight: bold; font-size: 1.5em; margin-top: 30px; color: #ddd;">📅 Event Schedule</h2>

            <div style="overflow-x: auto;">
                <table class="event-schedule">
                    <thead>
                        <tr>
                            <th>🛫 Route</th>
                            <th>🕒 Start</th>
                            <th>🕕 End</th>
                            <th>⏰ Vancouver Time (PST)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>✈ KSEA ↔ CYVR Crossfire</strong></td>
                            <td>18:00z</td>
                            <td>21:00z</td>
                            <td>10:00 - 13:00 PST</td>
                        </tr>
                        <tr>
                            <td><strong>🛫 KSLC → CYVR Flyout</strong></td>
                            <td>20:00z</td>
                            <td>23:00z</td>
                            <td>12:00 - 14:00 PST</td>
                        </tr>
                        <tr>
                            <td><strong>🛬 KSLC → CYVR Arrivals</strong></td>
                            <td>22:00z</td>
                            <td>01:00z</td>
                            <td>14:00 - 17:00 PST</td>
                        </tr>
                        <tr>
                            <td><strong>✈ CYYC ↔ CYVR Crossfire</strong></td>
                            <td>02:00z</td>
                            <td>06:00z</td>
                            <td>18:00 - 22:00 PST</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="countdown" style="font-size: 30px; font-weight: bold; color: white; padding: 20px; background-color: transparent; border-radius: 10px; display: inline-block; margin-top: 20px;"></div>

            <hr class="bg-light" style="margin: 20px 15px;">

            <!-- Giveaways Section -->
            <div style="background-color: #333; color: #ddd; padding: 20px; margin-top: 30px;">
                <h2 class="card-text-light" style="font-weight: bold; font-size: 1.5em; color: #ddd;">🎁 Giveaways</h2>
                <p class="card-text-light" style="color: #ddd;">We’re making things even more exciting with 16 giveaways thanks to our amazing sponsors! Each flight into or out of any airport in the CZVR FIR during the event earns you one entry—the more you fly, the better your chances. Prizes include</p>

                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px;">
                <div class="giveaway-item">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 2em; margin-right: 15px;">🏆</span>
                        <strong style="font-size: 1.2em;">1x Free 1-year Navigraph Unlimited Subscription</strong>
                    </div>
                </div>

                <div class="giveaway-item">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 2em; margin-right: 15px;">🏆</span>
                        <strong style="font-size: 1.2em;">2x €25 gift card from the Contrail Shop</strong>
                    </div>
                </div>

                <div class="giveaway-item">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 2em; margin-right: 15px;">🏆</span>
                        <strong style="font-size: 1.2em;">3x Any Orbx product of your choice (Orbx-developed products only)</strong>
                    </div>
                </div>

                <div class="giveaway-item">
                    <div style="display: flex; align-items: center;">
                        <span style="font-size: 2em; margin-right: 15px;">🏆</span>
                        <strong style="font-size: 1.2em;">10x Any scenery from Canadian Flight Sim Studios</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::check() && Auth::user()->permissions >= 4)
        <!-- BasileOMeter -->
            <div style="display: flex; justify-content: center;">
                <div style="text-align: center; padding: 20px;">
                    <h3 style="margin-bottom: 10px;">BasileOMeter</h3>
                    <div style="margin-bottom: 10px;">
                        <label for="sectorSelect">Sector</label>
                        <select id="sectorSelect" style="width: 100%; padding: 10px;"></select>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="labelToggle">Show Sector Name </label>
                        <input type="checkbox" id="labelToggle">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="newSectorName">New Name (Optional)</label>
                        <input type="text" id="newSectorName" placeholder="New Name" style="width: 100%; padding: 10px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="sectorColor">Color </label>
                        <input type="color" id="sectorColor" value="#0000ff" style="width: 30%; padding: 5px; border: none;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="sectorFreq">Frequency </label>
                        <input type="text" id="sectorFreq" placeholder="Example 133.700" style="width: 100%; padding: 5px;">
                    </div>
                    <div>
                        <label class="switch">
                            <input type="checkbox" id="liveDataToggle" checked>
                            <span class="slider round"></span>
                        </label>
                        <label for="liveDataToggle">Live Data </label>
                    </div>
                    <button id="saveButton" style="background-color: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">Enter</button>
                </div>
            </div>
            @endif
        <!-- Map Section -->
            <div class="card mt-4" style="background-color: #2a2a2a; border: none;">
            <div class="card-body">
                <h3 class="text-center" style="color: #ddd;">Vancouver Center Splits</h3>
                <small> This map is dynamic and will update accordingly each minute displaying when a controller logs online </small><br>
                <small> Grey = No Sectors Online </small><br><br>
                <small id="lastUpdated" style="color: #aaa;">Last Updated: </small>
                <div id="map" style="height: 800px;"></div>
            </div>
            <div id="controlPanel" style="display: none;">
        </div>
    </div>
</div>

<style>
    .event-schedule {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background-color: #333;
        color: #ddd;
        border-collapse: collapse;
    }

    .event-schedule th, .event-schedule td {
        padding: 12px;
        text-align: center;
        border: 1px solid #444;
    }

    .event-schedule thead {
        background-color: #444;
        color: #fff;
    }

    .event-schedule tbody tr:nth-child(even) {
        background-color: #2a2a2a;
    }

    .event-schedule tbody tr:nth-child(odd) {
        background-color: #333;
    }

    @media (max-width: 768px) {
        .event-schedule th, .event-schedule td {
            padding: 8px;
            font-size: 0.9em;
        }

        div[style="overflow-x: auto;"] {
            overflow-x: auto;
            display: block;
        }
    }
    .switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #4caf50;
    }

    input:checked + .slider:before {
        transform: translateX(20px);
    }

    .sector-label {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: #fff !important;
    font-size: 11px !important;
    text-align: center !important;
    }

    .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
    }

    #controlPanel {
        margin-top: 15px;
    }

    .card.mt-4 {
    height: 800px;
    resize: both;
    overflow: hidden;
    }

</style>

<!-- Include Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Include noUiSlider -->
<link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>

<script>
    const targetDate = new Date('2025-02-15T18:00:00Z');

    function updateCountdown() {
        const now = new Date();
        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        document.getElementById('lastUpdated').innerHTML = `Last Updated: ${timeString} Local`;
        const timeRemaining = targetDate - now;
        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
        document.getElementById("countdown").innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        if (timeRemaining <= 0) {
            clearInterval(interval);
            document.getElementById("countdown").innerHTML = '<span class="blue-text"> The event has started! </span>';
        }
    }
    const interval = setInterval(updateCountdown, 1000);
    updateCountdown();

    const map = L.map('map', {
        center: [52.131515, -126.383929],
        zoom: 5
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}.png', {
        attribution: '',
        maxZoom: 19
    }).addTo(map);

    const sectors = [
        {
            id: "CZVR_C_CTR",
            polygon: L.polygon([
                [50.1532,-115.2058], [50.1359,-115.1835], [50.1218,-115.1626], [50.1218,-115.1626], [50.1030,-115.1432], [50.0836,-115.1252], [50.0636,-115.1128], [50.0433,-115.1020], [50.0226,-115.0930], [50.0017,-115.0857], [49.5745,-115.0849], [49.4936,-115.0836], [49.4936,-115.0836], [49.3000,-115.0815], [49.0002,-115.2959], [49.0002,-115.2959], [49.0000,-118.1140], [49.0000,-120.0000], [49.0000,-120.0000], [49.1953,-120.0000], [49.1953,-120.3100], [49.1953,-120.3100], [49.2809,-120.3100], [49.4343,-120.4654], [49.4343,-120.4654], [49.5617,-119.0224], [50.3541,-116.2021], [50.3541,-116.2021], [50.1935,-116.0504], [50.2030,-116.0200], [50.2113,-115.5848], [50.2145,-115.5531], [50.2207,-115.5210], [50.2217,-115.4848], [50.2215,-115.4524], [50.2202,-115.4202], [50.2138,-115.3842], [50.2103,-115.3526], [50.2017,-115.3216], [50.1920,-115.2912], [50.1814,-115.2617], [50.1657,-115.2332], [50.1532,-115.2058]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_E_CTR",
            polygon: L.polygon([
                [49.4037,-122.1809], [50.0000,-122.0000], [50.2850,-121.3203], [50.2850,-121.3203], [50.0000,-121.2305], [49.5834,-121.0206], [49.4343,-120.4654], [49.4343,-120.4654], [49.2809,-120.3100], [49.1953,-120.3100], [49.1953,-120.3100], [49.1629,-121.4704], [49.1629,-121.4704], [49.1142,-122.1721], [48.5647,-123.1312], [48.5647,-123.1312], [49.0007,-123.1953], [49.0007,-123.1953], [49.1516,-122.5319], [49.1516,-122.5319], [49.3146,-122.2702], [49.4037,-122.1809]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_H_CTR",
            polygon: L.polygon([
                [56.0358,-123.5005], [56.0509,-124.0025], [56.0509,-124.0025], [56.1959,-126.1119], [56.1959,-126.1119], [56.2357,-126.4616], [56.2357,-126.4616], [56.3131,-127.5300], [56.3131,-127.5300], [52.4829,-127.5300], [52.4829,-127.5300], [51.0014,-125.3158], [51.0014,-125.3158], [50.1657,-124.3435], [50.1657,-124.3435], [50.5959,-123.5005], [50.5959,-123.5005], [56.0358,-123.5005]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_K_CTR",
            polygon: L.polygon([
                [52.4829,-127.5300], [51.0000,-133.4500], [51.0000,-133.4500], [48.2043,-128.0053], [48.2043,-128.0053], [48.2139,-127.3008], [48.2139,-127.3008], [49.4631,-127.0603], [50.0545,-124.4625], [50.1657,-124.3435], [51.0014,-125.3158]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_K_CTR",
            polygon: L.polygon([
                [51.0000,-133.4500], [48.2000,-128.0000], [48.1000,-127.5530], [47.3000,-130.3000], [47.3500,-131.1000], [50.4750,-134.2428]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_N_CTR",
            polygon: L.polygon([
                [49.0007,-123.1953], [49.1516,-122.5319], [49.1516,-122.5319], [49.3146,-122.2702], [49.4037,-122.1809], [49.4037,-122.1809], [50.0000,-122.0000], [50.2850,-121.3203], [50.2850,-121.3203], [51.1250,-121.4534], [50.5959,-123.5005], [50.5959,-123.5005], [50.1657,-124.3435], [50.1657,-124.3435], [50.0545,-124.4625], [49.0007,-123.1953]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_S_CTR",
            polygon: L.polygon([
                [48.5647,-123.1312], [48.5428,-123.0856], [48.3827,-123.0126], [48.3827,-123.0126], [48.3829,-122.3358], [48.3829,-122.3358], [48.3830,-122.2656], [48.3831,-122.1604], [48.3831,-122.1604], [48.4141,-122.1604], [48.4141,-122.1604], [48.5131,-122.1605], [48.5131,-122.1605], [48.5100,-121.0224], [48.5100,-121.0224], [48.4948,-120.0509], [49.0000,-120.0000], [49.0000,-120.0000], [49.1953,-120.0000], [49.1953,-120.3100], [49.1953,-120.3100], [49.1629,-121.4704], [49.1629,-121.4704], [49.1142,-122.1721], [48.5647,-123.1312]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_W_CTR",
            polygon: L.polygon([
                [50.0545,-124.4625], [49.4631,-127.0603], [48.2139,-127.3008], [48.2139,-127.3008], [48.2500,-126.3000], [48.3000,-125.0000], [48.2936,-124.4338], [48.1748,-124.0043], [48.1748,-124.0043], [48.1312,-123.3148], [48.1312,-123.3148], [48.1702,-123.1454], [48.1702,-123.1454], [48.2524,-123.0651], [48.3827,-123.0126], [48.3827,-123.0126], [48.5428,-123.0856], [48.5647,-123.1312], [48.5647,-123.1312], [49.0007,-123.1953], [49.0007,-123.1953]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_X_CTR",
            polygon: L.polygon([
                [51.1250,-121.4534], [50.5959,-123.5005], [50.5959,-123.5005], [56.0358,-123.5005], [56.0358,-123.5005], [56.0000,-123.1500], [55.1507,-121.5641], [54.3626,-120.5825], [54.3626,-120.5825], [53.4234,-119.3021], [53.4234,-119.3021], [53.2400,-119.0000], [53.2400,-119.0000], [52.5637,-118.3401], [52.5637,-118.3401], [51.1250,-121.4534]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_Y_CTR",
            polygon: L.polygon([
                [51.2613,-117.0817], [51.5148,-117.3233], [51.5148,-117.3233], [52.0610,-117.4610], [52.5637,-118.3401], [52.5637,-118.3401], [51.1250,-121.4534], [51.1250,-121.4534], [50.2850,-121.3203], [50.2850,-121.3203], [50.0000,-121.2305], [49.5834,-121.0206], [49.4343,-120.4654], [49.4343,-120.4654], [49.5617,-119.0224], [50.3541,-116.2021], [50.3541,-116.2021], [50.3838,-116.2309], [51.2613,-117.0817]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_Z_CTR",
            polygon: L.polygon([
                [52.4829,-127.5300], [54.4900,-130.3000], [54.4900,-130.3000], [54.5100,-130.2700], [54.5830,-130.1600], [55.0400,-130.1115], [55.1130,-130.0600], [55.2030,-130.0130], [55.2645,-130.0200], [55.3015,-130.0545], [55.3500,-130.0730], [55.4100,-130.0630], [55.4300,-130.0845], [55.4600,-130.0900], [55.4830,-130.0730], [55.4930,-130.0500], [55.5430,-130.0000], [55.5500,-130.0100], [56.0030,-130.0000], [56.0730,-130.0600], [56.0545,-130.1500], [56.0745,-130.2030], [56.0830,-130.2600], [56.1430,-130.2800], [56.1445,-130.3300], [56.1600,-130.3730], [56.2145,-130.4700], [56.2430,-131.0500], [56.2700,-131.1100], [56.3300,-131.2800], [56.3645,-131.3500], [56.3600,-131.5030], [56.4215,-131.5200], [56.4500,-131.5400], [56.4830,-131.5230], [56.5230,-132.0800], [57.0000,-132.0358], [57.0000,-132.0358], [56.3131,-127.5300], [56.3131,-127.5300], [52.4829,-127.5300]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_Z_CTR",
            polygon: L.polygon([
                [52.4829,-127.5300], [51.0000,-133.4500], [51.0000,-133.4500], [52.4300,-135.0000], [53.3003,-135.3732], [54.0000,-136.0000], [54.0000,-136.0000], [54.0630,-135.2700], [54.0630,-134.0000], [54.2436,-133.1536], [54.2436,-133.1536], [54.3500,-132.5000], [54.2930,-131.4800], [54.4230,-130.3630], [54.4430,-130.3730], [54.4545,-130.3900], [54.4645,-130.3745], [54.4745,-130.3300], [54.4900,-130.3000], [54.4900,-130.3000], [52.4829,-127.5300]
            ], {color: 'grey'})
        },
        {
            id: "CZVR_Z_CTR",
            polygon: L.polygon([
                [54.0000,-136.0000], [53.3003,-135.3732], [52.4300,-135.0000], [51.0000,-133.4500], [50.4750,-134.2428], [53.2203,-137.0000]
            ], {color: 'grey'})
        }
    ];

    const sectorLayer = L.layerGroup(sectors.map(sector => {
        sector.polygon
            .bindTooltip(`${sector.id}`, { 
                permanent: true, 
                direction: 'center', 
                className: 'sector-label' 
            })
            .addTo(map);
        return sector.polygon;
    })).addTo(map);

    const CALLSIGNS = {
        Z: ['CZVR_Z_CTR', 'CZVR_Z1_CTR'],
        K: ['CZVR_K_CTR', 'CZVR_K1_CTR'],
        H: ['CZVR_H_CTR', 'CZVR_H1_CTR'],
        X: ['CZVR_X_CTR', 'CZVR_X1_CTR'],
        C: ['CZVR_C_CTR', 'CZVR_C1_CTR'],
        Y: ['CZVR_Y_CTR', 'CZVR_Y1_CTR'],
        E: ['CZVR_E_CTR', 'CZVR_E1_CTR'],
        N: ['CZVR_N_CTR', 'CZVR_N1_CTR'],
        W: ['CZVR_W_CTR', 'CZVR_W1_CTR'],
        S: ['CZVR_S_CTR', 'CZVR_S1_CTR']
    };

    const OWNERSHIP = {
        "C": ["C", "E"],
        "E": ["E"],
        "H": ["H", "N"],
        "N": ["N"],
        "S": ["S", "E"],
        "W": ["W", "K"],
        "X": ["X", "N"],
        "Y": ["Y", "E"],
        "Z": ["Z", "W"],
        "K": ["K", "W"]
    };
    
    const COLOR_MAPPING = {
        "C": "blue",
        "E": "green",
        "H": "red",
        "N": "purple",
        "S": "orange",
        "W": "yellow",
        "X": "pink",
        "Y": "cyan",
        "Z": "magenta",
        "K": "violet",
    };

    async function checkCZVRControllerStatus() {
    try {
        const response = await fetch('https://data.vatsim.net/v3/vatsim-data.json');
        const data = await response.json();

        const czvrControllers = data.controllers.filter(controller => controller.callsign.toUpperCase().includes('CZVR'));

        sectors.forEach(sector => {
            const sectorCode = sector.id.toUpperCase().match(/CZVR_([CEHZKNWSXY])1?_CTR/)?.[1];
            const czvrCtrController = czvrControllers.find(controller => controller.callsign.toUpperCase() === "CZVR_CTR");
            const czvr1CtrController = czvrControllers.find(controller => controller.callsign.toUpperCase() === "CZVR_1_CTR");

            if (sectorCode && OWNERSHIP[sectorCode]) {
                const [primary, fallback] = OWNERSHIP[sectorCode];
                const primaryCallsign = CALLSIGNS[primary].find(call => czvrControllers.some(controller => controller.callsign.toUpperCase() === call));
                const fallbackCallsign = fallback ? CALLSIGNS[fallback].find(call => czvrControllers.some(controller => controller.callsign.toUpperCase() === call)) : undefined;

                const primaryController = czvrControllers.find(controller => controller.callsign.toUpperCase() === primaryCallsign?.toUpperCase());
                const fallbackController = fallbackCallsign ? czvrControllers.find(controller => controller.callsign.toUpperCase() === fallbackCallsign?.toUpperCase()) : undefined;

                if (primaryController) {
                    sector.polygon.setStyle({ color: COLOR_MAPPING[primary] });
                    sector.polygon.bindTooltip(
                        `${primaryController.callsign} <br> ${primaryController.frequency}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                } else if (fallbackController) {
                    sector.polygon.setStyle({ color: COLOR_MAPPING[fallback] });
                    sector.polygon.bindTooltip(
                        `${fallbackController.callsign} <br> ${fallbackController.frequency}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                } else if (czvrCtrController) {
                    sector.polygon.setStyle({ color: 'blue' });
                    sector.polygon.bindTooltip(
                        `${czvrCtrController.callsign} <br> ${czvrCtrController.frequency}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                } else if (czvr1CtrController) {
                    sector.polygon.setStyle({ color: 'blue' });
                    sector.polygon.bindTooltip(
                        `${czvr1CtrController.callsign} <br> ${czvr1CtrController.frequency}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                } else {
                    sector.polygon.setStyle({ color: 'grey' });
                    sector.polygon.bindTooltip(
                        `${sector.id}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                }
            } else {
                sector.polygon.setStyle({ color: 'grey' });
                sector.polygon.bindTooltip(
                    `${sector.id}`,
                    { permanent: true, direction: 'center', className: 'sector-label' }
                );
            }
            });
        } catch (error) {
            console.error('Error fetching VATSIM data:', error);
        }
    }

    let vatsimInterval;

    function startVatsimDataLoading() {
        checkCZVRControllerStatus();
        vatsimInterval = setInterval(checkCZVRControllerStatus, 100000);
    }

    function stopVatsimDataLoading() {
        clearInterval(vatsimInterval);
    }

    document.getElementById('liveDataToggle').addEventListener('change', function() {
        if (this.checked) {
            startVatsimDataLoading();
        } else {
            stopVatsimDataLoading();
        }
    });

    startVatsimDataLoading();
</script>

<script>
    window.onload = function () {
        function displayControlPanel() {
            document.getElementById('controlPanel').style.display = 'block';
            const sectorSelect = document.getElementById('sectorSelect');
            sectorSelect.innerHTML = '';

            sectors.forEach((sector, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = sector.id;
                sectorSelect.appendChild(option);
            });

            const defaultFreq = sectors[0]?.frequency || '';
            document.getElementById('sectorFreq').value = defaultFreq;
            document.getElementById('saveButton').addEventListener('click', saveSectorChanges);
        }

        function saveSectorChanges() {
            const selectedIndex = document.getElementById('sectorSelect').value;
            const showLabel = document.getElementById('labelToggle').checked;
            const sectorColor = document.getElementById('sectorColor').value;
            const customFreq = document.getElementById('sectorFreq').value;
            const newSectorName = document.getElementById('newSectorName').value;

            const sector = sectors[selectedIndex];

            if (sector) {
                if (newSectorName) {
                    sector.id = newSectorName;
                }

                if (showLabel) {
                    sector.polygon.bindTooltip(
                        `${sector.id} <br> ${customFreq}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                } else {
                    sector.polygon.unbindTooltip();
                }

                sector.polygon.setStyle({ color: sectorColor });
                sector.frequency = customFreq;
            }
        }

        function populateSectorDropdown() {
            const sectorSelect = document.getElementById('sectorSelect');
            if (sectorSelect) {
                sectorSelect.innerHTML = '';

                sectors.forEach((sector, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = sector.id;
                    sectorSelect.appendChild(option);
                });
            }
        }

        populateSectorDropdown();

        document.getElementById('saveButton').addEventListener('click', function () {
            const selectedIndex = document.getElementById('sectorSelect').value;
            const newSectorName = document.getElementById('newSectorName').value;
            const customFreq = document.getElementById('sectorFreq').value;

            if (selectedIndex !== "") {
                const sector = sectors[selectedIndex];

                if (sector) {
                    sector.polygon.unbindTooltip();

                    if (newSectorName) {
                        sector.id = newSectorName;
                    }

                    sector.frequency = customFreq;

                    sector.polygon.bindTooltip(
                        `${sector.id} <br> ${customFreq}`,
                        { permanent: true, direction: 'center', className: 'sector-label' }
                    );
                }
            } else {
                alert('Please select a sector to save changes.');
            }
        });

        const mapContainer = document.getElementById('map');

        const observer = new ResizeObserver(() => {
            if (map && typeof map.invalidateSize === 'function') {
                map.invalidateSize();
            }
        });
        observer.observe(mapContainer);

        displayControlPanel();
    };
</script>

@stop
