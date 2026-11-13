const CYVR_STANDS = [
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
	{ name: "Gate 64",  coordinates: [49.19859040,  -123.18380400], callsign: null, annotation: "International Flights | Heavy Gate" },
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
	{ name: "Gate 95B", coordinates: [49.19531725,  -123.17293111], callsign: null, annotation: "USA Flights" },

	// Aprons, general aviation and cargo (smaller dots)
	// South Terminal — Apron I
	{ name: 'Position 1A', coordinates: [49.1817770, -123.1700006], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1B', coordinates: [49.1815197, -123.1703164], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1C', coordinates: [49.1814797, -123.1706882], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1D', coordinates: [49.1815206, -123.1711788], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1E', coordinates: [49.1812363, -123.1712570], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1F', coordinates: [49.1810015, -123.1712885], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1G', coordinates: [49.1809983, -123.1707994], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1H', coordinates: [49.1806676, -123.1710622], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1J', coordinates: [49.1807016, -123.1716406], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1K', coordinates: [49.1804369, -123.1721052], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1L', coordinates: [49.1795099, -123.1717344], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1M', coordinates: [49.1795101, -123.1712057], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1N', coordinates: [49.1792880, -123.1710000], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1O', coordinates: [49.1792884, -123.1714390], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1P', coordinates: [49.1792880, -123.1712201], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1R', coordinates: [49.1792880, -123.1716597], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1T', coordinates: [49.1792872, -123.1718779], annotation: 'South Terminal | Apron I', radius: 10 },
	{ name: 'Position 1U', coordinates: [49.1792863, -123.1720986], annotation: 'South Terminal | Apron I', radius: 10 },

	// South Terminal — Apron II
	{ name: 'Position 2A', coordinates: [49.18207688, -123.17129712], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2B', coordinates: [49.18204563, -123.17079132], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2C', coordinates: [49.18202160, -123.17037363], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2D', coordinates: [49.18165122, -123.16940728], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2E', coordinates: [49.18176699, -123.16891247], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2F', coordinates: [49.18179327, -123.16829546], annotation: 'South Terminal | Apron II', radius: 12 },
	{ name: 'Position 2G', coordinates: [49.18175996, -123.16772263], annotation: 'South Terminal | Apron II', radius: 12 },

	// South Airport and general aviation parking
	{ name: 'Landmark 01', coordinates: [49.18370524, -123.17790286], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Landmark 02', coordinates: [49.18398646, -123.17711489], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Landmark 03', coordinates: [49.18386793, -123.17584564], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Landmark 04', coordinates: [49.18385100, -123.17547263], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Landmark 05', coordinates: [49.18358010, -123.17408423], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Landmark 06', coordinates: [49.18369862, -123.17383556], annotation: 'General Aviation | Landmark Aviation', radius: 11 },
	{ name: 'Canada Regional Ramp 01', coordinates: [49.18176033, -123.16614685], annotation: 'South Airport | Airline Parking', radius: 13 },
	{ name: 'Canada Regional Ramp 02', coordinates: [49.18158139, -123.16617520], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Canada Regional Ramp 03', coordinates: [49.18158754, -123.16637473], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Canada Regional Ramp 04', coordinates: [49.18159629, -123.16658598], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Canada Regional Ramp 05', coordinates: [49.18160381, -123.16679445], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Executive Aircraft 01', coordinates: [49.18142620, -123.15958583], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Executive Aircraft 02', coordinates: [49.18147486, -123.15989531], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Executive Aircraft 03', coordinates: [49.18151583, -123.16019695], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'Executive Aircraft 04', coordinates: [49.18160489, -123.16109450], annotation: 'South Airport | General Aviation', radius: 12 },
	{ name: 'Executive Aircraft 05', coordinates: [49.18157724, -123.16177378], annotation: 'South Airport | General Aviation', radius: 11 },
	{ name: 'Signature Flight 01', coordinates: [49.18178635, -123.16530194], annotation: 'South Airport | General Aviation', radius: 12 },
	{ name: 'Signature Flight 02', coordinates: [49.18152756, -123.16459417], annotation: 'South Airport | General Aviation', radius: 11 },
	{ name: 'Signature Flight 03', coordinates: [49.18152756, -123.16372276], annotation: 'South Airport | General Aviation', radius: 11 },
	{ name: 'Orca Airways Ramp 01', coordinates: [49.18088426, -123.16601512], annotation: 'South Airport | General Aviation', radius: 10 },
	{ name: 'London Air Services 3A', coordinates: [49.17891582, -123.16341696], annotation: 'Apron III | South Airport Operations', radius: 10 },
	{ name: 'London Air Services 3B', coordinates: [49.17905256, -123.16408608], annotation: 'Apron III | South Airport Operations', radius: 10 },
	{ name: 'London Air Services 3C', coordinates: [49.17907428, -123.16464272], annotation: 'Apron III | South Airport Operations', radius: 10 },

	// East remote parking
	{ name: 'Position E1', coordinates: [49.1983791, -123.1697580], annotation: 'East Pad | Remote Parking', radius: 14 },
	{ name: 'Position E2', coordinates: [49.1976330, -123.1697950], annotation: 'East Pad | Remote Parking', radius: 14 },
	{ name: 'Position E3', coordinates: [49.1970267, -123.1700310], annotation: 'East Pad | Remote Parking', radius: 14 },
	{ name: 'Position E10', coordinates: [49.1966176, -123.1663381], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E11', coordinates: [49.1965515, -123.1657449], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E12', coordinates: [49.1964871, -123.1651575], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E13', coordinates: [49.1964190, -123.1645709], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E14', coordinates: [49.1949027, -123.1649749], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E15', coordinates: [49.1949686, -123.1655636], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E16', coordinates: [49.1950243, -123.1661571], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E17', coordinates: [49.1951003, -123.1667395], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E18', coordinates: [49.1951832, -123.1674856], annotation: 'East Hard Stand | Remote Parking', radius: 14 },
	{ name: 'Position E19', coordinates: [49.1952511, -123.1680745], annotation: 'East Hard Stand | Remote Parking', radius: 14 },

	// Main-terminal remote positions
	{ name: 'Position S1A', coordinates: [49.1907326, -123.1824375], annotation: 'South Pad | Remote Parking', radius: 12 },
	{ name: 'Position S1', coordinates: [49.1907648, -123.1827094], annotation: 'South Pad | Remote Parking', radius: 14 },
	{ name: 'Position S1B', coordinates: [49.1907960, -123.1829844], annotation: 'South Pad | Remote Parking', radius: 12 },
	{ name: 'Position S2A', coordinates: [49.1906025, -123.1813001], annotation: 'South Pad | Remote Parking', radius: 12 },
	{ name: 'Position S2', coordinates: [49.1906344, -123.1815737], annotation: 'South Pad | Remote Parking', radius: 14 },
	{ name: 'Position S2B', coordinates: [49.1906643, -123.1818454], annotation: 'South Pad | Remote Parking', radius: 12 },

	// West Pad — cargo staging and de-icing bays
	{ name: 'West Pad W1', coordinates: [49.2003763, -123.1862619], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W2', coordinates: [49.2000740, -123.1862245], annotation: 'West Pad | De-icing', radius: 16 },
	{ name: 'West Pad W3', coordinates: [49.1999541, -123.1862566], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W4', coordinates: [49.1994026, -123.1865356], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W5', coordinates: [49.1992768, -123.1864361], annotation: 'West Pad | De-icing', radius: 16 },
	{ name: 'West Pad W6', coordinates: [49.1990857, -123.1867195], annotation: 'West Pad | De-icing', radius: 11 },
	{ name: 'West Pad W7', coordinates: [49.1986529, -123.1867650], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W8', coordinates: [49.1983932, -123.1866035], annotation: 'West Pad | De-icing', radius: 16 },
	{ name: 'West Pad W9', coordinates: [49.1981822, -123.1868866], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W11', coordinates: [49.2007947, -123.1888422], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W12', coordinates: [49.2005510, -123.1888123], annotation: 'West Pad | De-icing', radius: 18 },
	{ name: 'West Pad W13', coordinates: [49.2003291, -123.1889651], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W15', coordinates: [49.1996305, -123.1890581], annotation: 'West Pad | De-icing', radius: 16 },
	{ name: 'West Pad W17', coordinates: [49.1989489, -123.1893258], annotation: 'West Pad | De-icing', radius: 13 },
	{ name: 'West Pad W18', coordinates: [49.1986882, -123.1891985], annotation: 'West Pad | De-icing', radius: 18 },
	{ name: 'West Pad W19', coordinates: [49.1984703, -123.1894520], annotation: 'West Pad | De-icing', radius: 13 },

	// Main-terminal gates and alternate configurations
	{ name: 'Gate 14A', coordinates: [49.1919183, -123.1805298], annotation: 'Domestic Flights | Alternate Configuration', radius: 11 },
	{ name: 'Gate 16A', coordinates: [49.1918876, -123.1814319], annotation: 'Domestic Flights | Alternate Configuration', radius: 11 },
	{ name: 'Gate 16B', coordinates: [49.1919426, -123.1813202], annotation: 'Domestic Flights | Alternate Configuration', radius: 11 },
	{ name: 'Gate 19A', coordinates: [49.1918129, -123.1832820], annotation: 'Domestic Flights | Alternate Configuration', radius: 11 },
	{ name: 'Gate 26A', coordinates: [49.1929213, -123.1837287], annotation: 'Domestic Flights | Alternate Configuration', radius: 11 },
	{ name: 'Gate 46A', coordinates: [49.1961883, -123.1847811], annotation: 'Domestic Flights | Alternate Configuration', radius: 12 },
	{ name: 'Gate 48A', coordinates: [49.1959960, -123.1830662], annotation: 'Domestic Flights | Alternate Configuration', radius: 12 },
	{ name: 'Gate 49A', coordinates: [49.1956558, -123.1826308], annotation: 'Domestic Flights | Alternate Configuration', radius: 12 },
	{ name: 'Gate 76A', coordinates: [49.1982185, -123.1739662], annotation: 'International Flights | Alternate Configuration', radius: 12 },

	// Cargo apron
	{ name: 'Position 501', coordinates: [49.1886583, -123.1681505], annotation: 'Apron V | Air Canada Cargo', radius: 15 },
	{ name: 'Position 502', coordinates: [49.1885347, -123.1670857], annotation: 'Apron V | Cargo', radius: 15 },

	// Air Canada maintenance hangars
	{ name: 'AC Hangar 01', coordinates: [49.19614967, -123.15905147], annotation: 'Air Canada | Maintenance', radius: 15 },
	{ name: 'AC Hangar 02', coordinates: [49.19606881, -123.15829258], annotation: 'Air Canada | Maintenance', radius: 15 },
	{ name: 'AC Hangar 03', coordinates: [49.19598795, -123.15741819], annotation: 'Air Canada | Maintenance', radius: 17 },
	{ name: 'AC Hangar 04', coordinates: [49.19586936, -123.15644483], annotation: 'Air Canada | Maintenance', radius: 17 },

	// DHL cargo apron
	{ name: 'DHL Ramp 01', coordinates: [49.18927568, -123.17341424], annotation: 'DHL Express | Cargo', radius: 14 },
	{ name: 'DHL Ramp 02', coordinates: [49.18938043, -123.17429034], annotation: 'DHL Express | Cargo', radius: 14 },
	{ name: 'DHL Ramp 03', coordinates: [49.18944940, -123.17502318], annotation: 'DHL Express | Cargo', radius: 14 },

	// FedEx and Purolator cargo aprons
	{ name: 'FedEx Ramp 01', coordinates: [49.18730023, -123.15468193], annotation: 'FedEx | Cargo', radius: 16 },
	{ name: 'FedEx Ramp 02', coordinates: [49.18735090, -123.15527467], annotation: 'FedEx | Cargo', radius: 16 },
	{ name: 'FedEx Ramp 03', coordinates: [49.18735568, -123.15595220], annotation: 'FedEx | Cargo', radius: 16 },
	{ name: 'Purolator Ramp 01', coordinates: [49.18723178, -123.15683641], annotation: 'Purolator | Cargo', radius: 13 },
	{ name: 'Purolator Ramp 02', coordinates: [49.18721259, -123.15752918], annotation: 'Purolator | Cargo', radius: 13 },
	{ name: 'Purolator Ramp 03', coordinates: [49.18721543, -123.15820285], annotation: 'Purolator | Cargo', radius: 13 },

	// UPS cargo apron north of Runway 08L/26R
	{ name: 'UPS Ramp 01', coordinates: [49.20360583, -123.15979520], annotation: 'UPS | Cargo', radius: 16 },
	{ name: 'UPS Ramp 02', coordinates: [49.20368198, -123.16045725], annotation: 'UPS | Cargo', radius: 16 },
	{ name: 'UPS Ramp 03', coordinates: [49.20383402, -123.16107874], annotation: 'UPS | Cargo', radius: 16 },
	{ name: 'UPS Ramp 04', coordinates: [49.20342660, -123.16184245], annotation: 'UPS | Cargo', radius: 13 },
	{ name: 'UPS Ramp 05', coordinates: [49.20359778, -123.16179223], annotation: 'UPS | Cargo', radius: 13 },
	{ name: 'UPS Ramp 06', coordinates: [49.20375833, -123.16176178], annotation: 'UPS | Cargo', radius: 13 },
	{ name: 'UPS Ramp 07', coordinates: [49.20391582, -123.16175309], annotation: 'UPS | Cargo', radius: 13 }
];

window.LIVE_MAP_AIRPORTS = Object.freeze({
    CYYJ: {
        icao: 'CYYJ',
        name: 'Victoria',
        center: [48.6469, -123.4258],
        stands: Object.freeze([
            // Passenger terminal — current numbering
            { name: 'Gate 3', coordinates: [48.6411361, -123.4322434], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 4', coordinates: [48.6410610, -123.4317831], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 5', coordinates: [48.6410082, -123.4310481], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 6', coordinates: [48.6414867, -123.4307914], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 7', coordinates: [48.6419207, -123.4311672], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 8', coordinates: [48.6419705, -123.4304433], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 9', coordinates: [48.6419137, -123.4296704], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 10', coordinates: [48.6414317, -123.4299911], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 11', coordinates: [48.6411028, -123.4299597], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 12', coordinates: [48.6409985, -123.4298482], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 13', coordinates: [48.6407804, -123.4291073], annotation: 'Passenger Terminal', radius: 14 },

            // General aviation apron
            { name: 'GA Apron 01', coordinates: [48.65074529, -123.41761155], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 02', coordinates: [48.65059945, -123.41787185], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 03', coordinates: [48.65049202, -123.41803423], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 04', coordinates: [48.65029943, -123.41830526], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 05', coordinates: [48.65003448, -123.41873970], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 06', coordinates: [48.64994447, -123.41888366], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 07', coordinates: [48.64984936, -123.41902762], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 08', coordinates: [48.64976274, -123.41916644], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 09', coordinates: [48.65096009, -123.41828726], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 10', coordinates: [48.65087008, -123.41842094], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 11', coordinates: [48.65078346, -123.41857261], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 12', coordinates: [48.65069515, -123.41871657], annotation: 'General Aviation', radius: 9 },
            { name: 'GA Apron 13', coordinates: [48.65060174, -123.41885796], annotation: 'General Aviation', radius: 9 },
            { name: 'GA East 01', coordinates: [48.64160321, -123.41910781], annotation: 'General Aviation | East Apron', radius: 9 },
            { name: 'GA East 02', coordinates: [48.64149074, -123.41851880], annotation: 'General Aviation | East Apron', radius: 9 },

            // FBO and operator aprons
            { name: 'Pacific Sky 01', coordinates: [48.64521360, -123.43449234], annotation: 'Pacific Sky Aviation', radius: 9 },
            { name: 'Pacific Sky 02', coordinates: [48.64512440, -123.43431640], annotation: 'Pacific Sky Aviation', radius: 9 },
            { name: 'Pacific Sky 03', coordinates: [48.64502017, -123.43460541], annotation: 'Pacific Sky Aviation', radius: 9 },
            { name: '433 Squadron 01', coordinates: [48.64738191, -123.43213399], annotation: '433 Squadron', radius: 10 },
            { name: '433 Squadron 02', coordinates: [48.64755472, -123.43221126], annotation: '433 Squadron', radius: 10 },
            { name: '433 Squadron 03', coordinates: [48.64772640, -123.43231511], annotation: '433 Squadron', radius: 10 },
            { name: 'Shell FBO 01', coordinates: [48.64600606, -123.41806771], annotation: 'Shell Aerocentre', radius: 9 },
            { name: 'Shell FBO 02', coordinates: [48.64586646, -123.41813757], annotation: 'Shell Aerocentre', radius: 9 },
            { name: 'Shell FBO 03', coordinates: [48.64570772, -123.41821595], annotation: 'Shell Aerocentre', radius: 9 },
            { name: 'Shell FBO 04', coordinates: [48.64603055, -123.41927688], annotation: 'Shell Aerocentre', radius: 9 },
            { name: 'Shell FBO 05', coordinates: [48.64630274, -123.41967117], annotation: 'Shell Aerocentre', radius: 9 },

            // Victoria Flying Club
            { name: 'VFC 01', coordinates: [48.64622973, -123.42088953], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 02', coordinates: [48.64620146, -123.42072965], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 03', coordinates: [48.64617170, -123.42055852], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 04', coordinates: [48.64614583, -123.42040765], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 05', coordinates: [48.64611713, -123.42024566], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 06', coordinates: [48.64608962, -123.42008820], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 07', coordinates: [48.64621478, -123.42003345], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 08', coordinates: [48.64624467, -123.42019543], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 09', coordinates: [48.64627457, -123.42035018], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 10', coordinates: [48.64630446, -123.42050764], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 11', coordinates: [48.64633615, -123.42067053], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 12', coordinates: [48.64553781, -123.42215799], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 13', coordinates: [48.64564901, -123.42210543], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 14', coordinates: [48.64575518, -123.42205663], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 15', coordinates: [48.64585932, -123.42201384], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 16', coordinates: [48.64597090, -123.42196656], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 17', coordinates: [48.64607654, -123.42191927], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 18', coordinates: [48.64618515, -123.42187198], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 19', coordinates: [48.64549700, -123.42188380], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 20', coordinates: [48.64559343, -123.42184210], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 22', coordinates: [48.64554810, -123.42156048], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 23', coordinates: [48.64565225, -123.42151544], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 24', coordinates: [48.64575887, -123.42147041], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 25', coordinates: [48.64586674, -123.42142349], annotation: 'Victoria Flying Club', radius: 8 },
            { name: 'VFC 26', coordinates: [48.64597336, -123.42137658], annotation: 'Victoria Flying Club', radius: 8 },

            // Cargo
            { name: 'Cargo Stand', coordinates: [48.64461541, -123.42228029], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Purolator Cargo 01', coordinates: [48.64404621, -123.42249566], annotation: 'Purolator | Cargo', radius: 14 },
            { name: 'Purolator Cargo 02', coordinates: [48.64326740, -123.42282275], annotation: 'Purolator | Cargo', radius: 14 },
            { name: 'Purolator Cargo 03', coordinates: [48.64293326, -123.42308369], annotation: 'Purolator | Cargo', radius: 14 },
            { name: 'FedEx Apron 01', coordinates: [48.64547277, -123.43364945], annotation: 'FedEx | Cargo', radius: 11 },
        ]),
    },
    CYLW: {
        icao: 'CYLW',
        name: 'Kelowna',
        center: [49.9561, -119.3778],
        alternateGroups: [
            ['Gate 4', 'Gate 4A'],
            ['Gate 10', 'Gate 10A'],
        ],
        stands: Object.freeze([
            // Passenger terminal — current numbering
            { name: 'Gate 1', coordinates: [49.9533031, -119.3805054], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 2', coordinates: [49.9528479, -119.3804257], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 3', coordinates: [49.9523918, -119.3803630], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 4', coordinates: [49.9519475, -119.3803810], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 4A', coordinates: [49.9519598, -119.3803164], annotation: 'Passenger Terminal | Alternate Configuration', radius: 11 },
            { name: 'Gate 6', coordinates: [49.9510589, -119.3803339], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 7', coordinates: [49.9507005, -119.3802726], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 8', coordinates: [49.9502447, -119.3801964], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 9', coordinates: [49.9498575, -119.3802474], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 10', coordinates: [49.9493888, -119.3804801], annotation: 'Passenger Terminal', radius: 14 },
            { name: 'Gate 10A', coordinates: [49.9493517, -119.3804660], annotation: 'Passenger Terminal | Alternate Configuration', radius: 11 },

            // General aviation parking
            { name: 'GA 01', coordinates: [49.96254615, -119.38133191], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 02', coordinates: [49.96255598, -119.38114207], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 03', coordinates: [49.96256300, -119.38095005], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 04', coordinates: [49.96257283, -119.38074711], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 05', coordinates: [49.96258406, -119.38056600], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 06', coordinates: [49.96270715, -119.38134917], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 07', coordinates: [49.96271674, -119.38115691], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 08', coordinates: [49.96272825, -119.38097508], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 09', coordinates: [49.96273879, -119.38077536], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 10', coordinates: [49.96274550, -119.38059949], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 11', coordinates: [49.96303940, -119.38060793], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 12', coordinates: [49.96302042, -119.38079591], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 13', coordinates: [49.96300381, -119.38100654], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 14', coordinates: [49.96299195, -119.38120585], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 15', coordinates: [49.96297534, -119.38140289], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 16', coordinates: [49.96323184, -119.38064896], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 17', coordinates: [49.96321418, -119.38084878], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 18', coordinates: [49.96320362, -119.38103561], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 19', coordinates: [49.96318359, -119.38124266], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 20', coordinates: [49.96316675, -119.38145651], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 21', coordinates: [49.96346912, -119.38068925], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 22', coordinates: [49.96345298, -119.38088626], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 23', coordinates: [49.96343955, -119.38107621], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 24', coordinates: [49.96342596, -119.38126969], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 25', coordinates: [49.96341747, -119.38146508], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 26', coordinates: [49.96366326, -119.38071761], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 27', coordinates: [49.96364899, -119.38090422], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 28', coordinates: [49.96364014, -119.38110691], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 29', coordinates: [49.96363030, -119.38129811], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 30', coordinates: [49.96361455, -119.38149620], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 31', coordinates: [49.96376726, -119.38075627], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 32', coordinates: [49.96375752, -119.38095306], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 33', coordinates: [49.96374409, -119.38114301], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 34', coordinates: [49.96373050, -119.38133649], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 35', coordinates: [49.96371521, -119.38153068], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 36', coordinates: [49.96393335, -119.38154930], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 37', coordinates: [49.96395289, -119.38133941], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 38', coordinates: [49.96395798, -119.38114820], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 39', coordinates: [49.96396938, -119.38094552], annotation: 'General Aviation', radius: 8 },
            { name: 'GA 40', coordinates: [49.96398775, -119.38075890], annotation: 'General Aviation', radius: 8 },

            // Cargo and operator aprons
            { name: 'Cargo 01A', coordinates: [49.95400140, -119.38084838], annotation: 'Cargo Apron', radius: 13 },
            { name: 'Cargo 01B', coordinates: [49.95384362, -119.38010966], annotation: 'Cargo Apron', radius: 13 },
            { name: 'Cargo 01C', coordinates: [49.95382120, -119.37968067], annotation: 'Cargo Apron', radius: 13 },
            { name: 'Cargo 01D', coordinates: [49.95452505, -119.38089214], annotation: 'Cargo Apron', radius: 13 },
            { name: 'Cargo 02', coordinates: [49.95562887, -119.38074970], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Cargo 03', coordinates: [49.95594726, -119.38079752], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 04A', coordinates: [49.95565461, -119.38290729], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Cargo 04B', coordinates: [49.95619150, -119.38300276], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Cargo 04C', coordinates: [49.95705673, -119.38211293], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Cargo 05', coordinates: [49.95740294, -119.38046563], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 08A', coordinates: [49.96476727, -119.38100959], annotation: 'Cargo Apron', radius: 14 },
            { name: 'Cargo 08B', coordinates: [49.96544282, -119.38123926], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 08C', coordinates: [49.96424287, -119.38089748], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 09', coordinates: [49.96712732, -119.38152726], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 10', coordinates: [49.96937822, -119.38197671], annotation: 'Cargo Apron', radius: 12 },
            { name: 'Cargo 13', coordinates: [49.95496333, -119.38096181], annotation: 'Cargo Apron', radius: 14 },
            { name: 'KFC 01', coordinates: [49.96600665, -119.38143725], annotation: 'Kelowna Flightcraft', radius: 14 },
            { name: 'KFC 02', coordinates: [49.96638534, -119.38171067], annotation: 'Kelowna Flightcraft', radius: 12 },
            { name: 'FedEx 01', coordinates: [49.96801347, -119.38143332], annotation: 'FedEx | Cargo', radius: 14 },
            { name: 'FedEx 02', coordinates: [49.96841110, -119.38214843], annotation: 'FedEx | Cargo', radius: 14 },
            { name: 'SIFC 01', coordinates: [49.97023832, -119.38219597], annotation: 'Southern Interior Flight Centre', radius: 12 },
            { name: 'SIFC 02', coordinates: [49.96979571, -119.38190798], annotation: 'Southern Interior Flight Centre', radius: 9 },
            { name: 'SIFC 03', coordinates: [49.96980975, -119.38171506], annotation: 'Southern Interior Flight Centre', radius: 9 },

            // Grass parking
            { name: 'GA Grass 01', coordinates: [49.96232336, -119.38130640], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 02', coordinates: [49.96233407, -119.38112483], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 03', coordinates: [49.96234573, -119.38093374], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 04', coordinates: [49.96236127, -119.38072855], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 05', coordinates: [49.96237488, -119.38056840], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 06', coordinates: [49.96208067, -119.38110784], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 07', coordinates: [49.96209943, -119.38090319], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 08', coordinates: [49.96211131, -119.38073579], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 09', coordinates: [49.96212931, -119.38056437], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 10', coordinates: [49.96182809, -119.38109468], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 11', coordinates: [49.96184195, -119.38087200], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 12', coordinates: [49.96186361, -119.38067779], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 13', coordinates: [49.96187565, -119.38049406], annotation: 'General Aviation | Grass', radius: 7 },
            { name: 'GA Grass 14', coordinates: [49.96189118, -119.38029802], annotation: 'General Aviation | Grass', radius: 7 },
        ]),
    },
});

(() => {
    'use strict';

    const GATE_RADIUS_KM = 0.04;
    const REFRESH_INTERVAL_MS = 30_000;
    const ACTIVE_TAB_REFRESH_THRESHOLD_MS = 10_000;
    const AVAILABLE_COLOR = '#4f9c45';
    const OCCUPIED_COLOR = '#bb4747';
    const AIRPORTS = Object.freeze({
        CYVR: {
            icao: 'CYVR',
            name: 'Vancouver',
            center: [49.1947, -123.1792],
            alternateGroups: [
                ['Gate 14', 'Gate 14A'],
                ['Gate 16', 'Gate 16A', 'Gate 16B'],
                ['Gate 19', 'Gate 19A'],
                ['Gate 26', 'Gate 26A'],
                ['Gate 46', 'Gate 46A'],
                ['Gate 48', 'Gate 48A'],
                ['Gate 49', 'Gate 49A'],
                ['Gate 76', 'Gate 76A'],
                ['Position S1', 'Position S1A', 'Position S1B'],
                ['Position S2', 'Position S2A', 'Position S2B'],
            ],
            stands: Object.freeze([
                ...CYVR_STANDS,
            ]),
        },
        ...(window.LIVE_MAP_AIRPORTS ?? {}),
    });

    class FirLiveMap {
        constructor(element) {
            this.element = element;
            this.feedUrl = element.dataset.feedUrl;
            this.status = document.getElementById('map-status');
            this.airportButtons = [...document.querySelectorAll('[data-airport]')];
            this.airport = AIRPORTS.CYVR;
            this.abortController = null;
            this.refreshTimer = null;
            this.lastRefreshStartedAt = 0;
            this.hasFitStands = false;
            this.map = L.map(element, {
                attributionControl: false,
                preferCanvas: true,
                zoomControl: false,
            }).setView(this.airport.center, 15);

            new ResizeObserver(() => this.map.invalidateSize()).observe(element);
            this.standLayer = L.featureGroup().addTo(this.map);
            this.aircraftLayer = L.featureGroup().addTo(this.map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                minZoom: 0,
            }).addTo(this.map);
        }

        start() {
            for (const button of this.airportButtons) {
                button.addEventListener('click', () => this.selectAirport(button.dataset.airport));
            }

            this.selectAirport('CYVR');

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.refreshIfStale();
                }
            });
            window.addEventListener('focus', () => this.refreshIfStale());
        }

        selectAirport(icao) {
            const airport = AIRPORTS[icao];

            if (!airport) {
                return;
            }

            this.airport = airport;
            this.hasFitStands = false;
            this.status.textContent = 'Loading live traffic…';

            for (const button of this.airportButtons) {
                const selected = button.dataset.airport === airport.icao;
                button.classList.toggle('active', selected);
                button.setAttribute('aria-selected', String(selected));
            }

            this.aircraftLayer.clearLayers();
            this.renderStands(new Map());
            this.refresh();
        }

        async refresh() {
            window.clearTimeout(this.refreshTimer);
            this.abortController?.abort();
            this.abortController = new AbortController();
            this.lastRefreshStartedAt = Date.now();
            const airport = this.airport;
            const url = new URL(this.feedUrl, window.location.origin);
            url.searchParams.set('airport', airport.icao);

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                    signal: this.abortController.signal,
                });

                if (!response.ok) {
                    throw new Error(`Traffic feed returned ${response.status}`);
                }

                const data = await response.json();

                if (this.airport !== airport) {
                    return;
                }

                const pilots = Array.isArray(data.pilots) ? data.pilots : [];
                const occupiedStands = this.findOccupiedStands(pilots);

                this.renderStands(occupiedStands);
                this.renderAircraft(pilots, occupiedStands);
                this.showUpdated(data.updated_at);
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                console.error(`Unable to update the ${airport.icao} live map.`, error);
                this.status.textContent = 'Live traffic is temporarily unavailable!';
            } finally {
                if (this.airport === airport) {
                    this.scheduleRefresh();
                }
            }
        }

        refreshIfStale() {
            if (Date.now() - this.lastRefreshStartedAt >= ACTIVE_TAB_REFRESH_THRESHOLD_MS) {
                this.refresh();
            }
        }

        scheduleRefresh() {
            window.clearTimeout(this.refreshTimer);
            this.refreshTimer = window.setTimeout(() => this.refresh(), REFRESH_INTERVAL_MS);
        }

        findOccupiedStands(pilots) {
            const occupied = new Map();

            for (const pilot of pilots) {
                let closestStand = null;
                let closestDistance = GATE_RADIUS_KM;

                for (const stand of this.airport.stands) {
                    const distance = this.distance(
                        stand.coordinates[0],
                        stand.coordinates[1],
                        Number(pilot.latitude),
                        Number(pilot.longitude),
                    );

                    if (distance <= closestDistance) {
                        closestDistance = distance;
                        closestStand = stand;
                    }
                }

                if (!closestStand) {
                    continue;
                }

                const alternateGroup = (this.airport.alternateGroups ?? [])
                    .find((group) => group.includes(closestStand.name));

                for (const standName of alternateGroup ?? [closestStand.name]) {
                    occupied.set(standName, pilot);
                }
            }

            return occupied;
        }

        renderStands(occupiedStands) {
            this.standLayer.clearLayers();

            for (const stand of this.airport.stands) {
                const pilot = occupiedStands.get(stand.name);
                const color = pilot ? OCCUPIED_COLOR : AVAILABLE_COLOR;
                const tooltip = [
                    `<strong>${this.escape(stand.name)}</strong>`,
                    this.escape(stand.annotation),
                    pilot ? `<strong>${this.escape(pilot.callsign)}</strong>` : 'Available',
                ].join('<br>');

                L.circle(stand.coordinates, {
                    color,
                    fillColor: color,
                    fillOpacity: 0.58,
                    radius: stand.radius ?? 17,
                    weight: 1.5,
                }).bindTooltip(tooltip).addTo(this.standLayer);
            }

            if (!this.hasFitStands && this.standLayer.getBounds().isValid()) {
                this.map.fitBounds(this.standLayer.getBounds(), {
                    maxZoom: 15,
                    padding: [24, 24],
                });
                this.hasFitStands = true;
            }
        }

        renderAircraft(pilots, occupiedStands) {
            const callsignsAtGates = new Set(
                [...occupiedStands.values()].map((pilot) => pilot.callsign),
            );

            this.aircraftLayer.clearLayers();

            for (const pilot of pilots) {
                if (callsignsAtGates.has(pilot.callsign)) {
                    continue;
                }

                const heading = Number.isFinite(Number(pilot.heading)) ? Number(pilot.heading) : 0;
                const icon = L.divIcon({
                    className: '',
                    html: `<div class="aircraft-marker"><span style="transform: rotate(${heading - 45}deg)">✈</span></div>`,
                    iconAnchor: [10, 10],
                    iconSize: [20, 20],
                    popupAnchor: [0, -8],
                });

                L.marker([pilot.latitude, pilot.longitude], { icon })
                    .bindPopup(this.aircraftPopup(pilot))
                    .addTo(this.aircraftLayer);
            }
        }

        aircraftPopup(pilot) {
            const route = [pilot.departure, pilot.arrival]
                .filter(Boolean)
                .map((value) => this.escape(value))
                .join(' → ') || 'Route not filed';
            const aircraft = pilot.aircraft ? ` · ${this.escape(pilot.aircraft)}` : '';

            return [
                `<strong>${this.escape(pilot.callsign || 'Unknown aircraft')}</strong>${aircraft}`,
                route,
                `${Number(pilot.altitude || 0).toLocaleString()} ft · ${Number(pilot.groundspeed || 0)} kt`,
            ].join('<br>');
        }

        showUpdated(timestamp) {
            const updated = timestamp ? new Date(timestamp) : new Date();
            const time = Number.isNaN(updated.getTime())
                ? 'just now'
                : updated.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            this.status.textContent = `Updated ${time}`;
        }

        distance(latitudeA, longitudeA, latitudeB, longitudeB) {
            const earthRadiusKm = 6371;
            const latitudeDelta = this.radians(latitudeB - latitudeA);
            const longitudeDelta = this.radians(longitudeB - longitudeA);
            const a = Math.sin(latitudeDelta / 2) ** 2
                + Math.cos(this.radians(latitudeA))
                * Math.cos(this.radians(latitudeB))
                * Math.sin(longitudeDelta / 2) ** 2;

            return earthRadiusKm * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        radians(degrees) {
            return degrees * (Math.PI / 180);
        }

        escape(value) {
            const element = document.createElement('span');
            element.textContent = String(value ?? '');

            return element.innerHTML;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const map = document.getElementById('map');

        if (map && window.L) {
            new FirLiveMap(map).start();
        }
    });
})();
