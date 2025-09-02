var stands = [
	{ name: "Gate 06",  coordinates: [49.18980866,  -123.17913224], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 07",  coordinates: [49.19012180,  -123.17925138], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 08",  coordinates: [49.19039952,  -123.17903890], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 09",  coordinates: [49.19074046,  -123.17892450], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 10",  coordinates: [49.19110984,  -123.17893675], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 12",  coordinates: [49.19138668,  -123.17931674], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 13",  coordinates: [49.19160411,  -123.17973492], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 14",  coordinates: [49.19185805,  -123.18034620], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 15",  coordinates: [49.19189117,  -123.18091985], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 16",  coordinates: [49.19187350,  -123.18150966], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 17",  coordinates: [49.19198624,  -123.18205781], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 18",  coordinates: [49.19199830,  -123.18278372], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 19",  coordinates: [49.19203622,  -123.18347592], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 20",  coordinates: [49.19201698,  -123.18397909], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 21",  coordinates: [49.19215863,  -123.18446199], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 22",  coordinates: [49.19236031,  -123.18476177], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 23",  coordinates: [49.19269438,  -123.18517129], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 24",  coordinates: [49.19300338,  -123.18488336], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 25",  coordinates: [49.19296065,  -123.18433172], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 26",  coordinates: [49.19284496,  -123.18366303], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 27",  coordinates: [49.19295301,  -123.18310966], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 28",  coordinates: [49.19319916,  -123.18261832], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 29",  coordinates: [49.19410788,  -123.18237482], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 30",  coordinates: [49.19452769,  -123.18256996], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 31",  coordinates: [49.19492444,  -123.18302208], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 32",  coordinates: [49.19491902,  -123.18384879], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 33",  coordinates: [49.19449552,  -123.18405829], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 34",  coordinates: [49.19422125,  -123.18383836], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 35",  coordinates: [49.19421636,  -123.18452751], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 36",  coordinates: [49.19445993,  -123.18475448], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 37",  coordinates: [49.19470353,  -123.18468481], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 38",  coordinates: [49.19507003,  -123.18460825], callsign: null, annotation: "Domestic Flights | Dash-8/CRJ900 Only" },
	{ name: "Gate 39",  coordinates: [49.19475200,  -123.18603317], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 40",  coordinates: [49.19497172,  -123.18615169], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 41",  coordinates: [49.19534097,  -123.18610587], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 42",  coordinates: [49.19572898,  -123.18601672], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 43",  coordinates: [49.19602579,  -123.18582632], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 44",  coordinates: [49.19624015,  -123.18560478], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 45",  coordinates: [49.19630282,  -123.18528995], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 46",  coordinates: [49.19617259,  -123.18485685], callsign: null, annotation: "Domestic Flights" },
	{ name: "Gate 47",  coordinates: [49.19606973,  -123.18389376], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 48",  coordinates: [49.19601388,  -123.18305323], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 49",  coordinates: [49.19561937,  -123.18254823], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 50",  coordinates: [49.19527706,  -123.18131139], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 51",  coordinates: [49.19553489,  -123.18014646], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 52",  coordinates: [49.19584416,  -123.17915944], callsign: null, annotation: "Domestic Flights | Heavy Gate" },
	{ name: "Gate 53",  coordinates: [49.19705974,  -123.17892655], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 54",  coordinates: [49.19745653,  -123.17984852], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 55",  coordinates: [49.19789490,  -123.18058555], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 58",  coordinates: [49.19826715,  -123.18139276], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 62",  coordinates: [49.19832437,  -123.18288315], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 64",  coordinates: [49.19832692,  -123.18403745], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 66",  coordinates: [49.19939000,  -123.18376400], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 67",  coordinates: [49.19929900,  -123.18268600], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 68",  coordinates: [49.19918200,  -123.18147300], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 69",  coordinates: [49.19904650,  -123.18028677], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 70",  coordinates: [49.19877200,  -123.17941500], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 71",  coordinates: [49.19847100,  -123.17884000], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 72",  coordinates: [49.19811143,  -123.17785847], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 73",  coordinates: [49.19786364,  -123.17672114], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 74",  coordinates: [49.19804448,  -123.17554974], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 75",  coordinates: [49.19831349,  -123.17465896], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 76",  coordinates: [49.19838547,  -123.17389219], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 77",  coordinates: [49.19828102,  -123.17341389], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 78",  coordinates: [49.19811318,  -123.17304570], callsign: null, annotation: "International Flights | Heavy Gate" },
	{ name: "Gate 79",  coordinates: [49.19770433,  -123.17307720], callsign: null, annotation: "USA Flights | Heavy Gate" },
	{ name: "Gate 80",  coordinates: [49.19736210,  -123.17299700], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 81",  coordinates: [49.19713633,  -123.17315920], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 82",  coordinates: [49.19717426,  -123.17373681], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 83",  coordinates: [49.19711138,  -123.17433669], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 85",  coordinates: [49.19694580,  -123.17486553], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 86",  coordinates: [49.19679168,  -123.17541961], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 87",  coordinates: [49.19664818,  -123.17598470], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 88",  coordinates: [49.19641414,  -123.17645366], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 90",  coordinates: [49.19574250,  -123.17460362], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 91",  coordinates: [49.19588097,  -123.17420382], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 92",  coordinates: [49.19587412,  -123.17363526], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 93",  coordinates: [49.19587161,  -123.17306739], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 94",  coordinates: [49.19597194,  -123.17254916], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 95A", coordinates: [49.19545486,  -123.17278389], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 96",  coordinates: [49.19533293,  -123.17373550], callsign: null, annotation: "USA Flights" },
	{ name: "Gate 96B", coordinates: [49.19531725,  -123.17293111], callsign: null, annotation: "USA Flights" }
];

const refreshCooldown = 2 * 60 * 1000;
let lastRefreshTime = 0;
let isRefreshing = false;

const map = L.map('map').setView([49.194, -123.18], 15);
const standGroup = L.featureGroup().addTo(map);

L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    ext: 'png'
}).addTo(map);

async function fetchVatsimData() {
    try {
        const response = await fetch('https://data.vatsim.net/v3/vatsim-data.json');
        return await response.json();
    } catch (error) {
        displayErrorMessage();
        return null;
    }
}

function displayErrorMessage() {
    const mapContainer = document.getElementById('map');
    const overlay = document.createElement('div');
    Object.assign(overlay.style, {
        position: 'absolute', top: 0, left: 0, width: '100%', height: '100%', backgroundColor: 'rgba(0, 0, 0, 0.5)', zIndex: 1000
    });

    const errorMessageContainer = document.createElement('div');
    Object.assign(errorMessageContainer.style, {
        position: 'absolute', top: '50%', left: '50%',
        transform: 'translate(-50%, -50%)', padding: '20px',
        borderRadius: '10px', textAlign: 'center'
    });

    const errorMessage = document.createElement('p');
    errorMessage.textContent = 'Unable to fetch VATSIM data, please check back later!';
    Object.assign(errorMessage.style, { color: 'red', fontSize: '18px' });

    errorMessageContainer.appendChild(errorMessage);
    overlay.appendChild(errorMessageContainer);
    mapContainer.appendChild(overlay);
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

async function checkNearbyUsers(coordinates, vatsimData) {
    if (!vatsimData) return [];
    return vatsimData.pilots.filter(pilot => getDistance(coordinates[0], coordinates[1], pilot.latitude, pilot.longitude) <= 0.03);
}

function createTooltipContent(stand) {
    return `<b>${stand.name}</b><br>${stand.annotation}${stand.taken ? `<br><b>${stand.callsign}</b>` : ''}`;
}

function renderStands() {
    stands.forEach(stand => {
        const color = stand.taken ? 'red' : 'green';
        const existingStand = standGroup.getLayers().find(layer => layer.options.name === stand.name);

        if (!existingStand) {
            const newStand = L.circle(stand.coordinates, {
                color, fillColor: color, fillOpacity: 0.5, radius: 17, name: stand.name
            }).addTo(standGroup);
            newStand.bindTooltip(createTooltipContent(stand));
        } else {
            existingStand.setStyle({ color, fillColor: color });
            existingStand.setTooltipContent(createTooltipContent(stand));
        }
    });
}

function updateLastUpdated(date) {
    document.getElementById("last-updated").textContent = "Updated " + date.toLocaleString();
}

async function refreshMap() {
    if (isRefreshing) return;
    isRefreshing = true;

    try {
        const vatsimData = await fetchVatsimData();
        if (!vatsimData) return;

        await Promise.all(stands.map(async stand => {
            const nearbyUsers = await checkNearbyUsers(stand.coordinates, vatsimData);
            stand.taken = nearbyUsers.length > 0;
            stand.callsign = nearbyUsers[0]?.callsign || null;
        }));

        renderStands();
        updateLastUpdated(new Date());
        lastRefreshTime = Date.now();
    } finally {
        isRefreshing = false;
    }
}

async function initializeMap() {
    await refreshMap();
    setInterval(async () => {
        if (Date.now() - lastRefreshTime >= refreshCooldown) {
            await refreshMap();
        }
    }, refreshCooldown);
}

document.addEventListener("DOMContentLoaded", initializeMap);
