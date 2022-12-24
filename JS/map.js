const ARRONDISSEMENT_DEFAULT_COLOR = "#ED820E";
const ARRONDISSEMENT_HOVER_COLOR = "#FF0000";

const POLY_STYLE_SELECTED = {
    color: ARRONDISSEMENT_HOVER_COLOR,
    opacity: 1,
    fillColor: ARRONDISSEMENT_HOVER_COLOR,
    fillOpacity: 0.15
}
const POLY_STYLE_DEFAULT = {
    color: ARRONDISSEMENT_DEFAULT_COLOR,
    opacity: 1,
    fillColor: ARRONDISSEMENT_DEFAULT_COLOR,
    fillOpacity: 0.15
}

const MARKER_AVAILABLE_STYLE = {
    iconSize: [25, 38],
    iconAnchor: [12.5, 38],
    popupAnchor: [0, -34],
    iconUrl: "./images/available-marker.png"
};

const MARKER_UNAVAILABLE_STYLE = {
    iconSize: [25, 38],
    iconAnchor: [12.5, 38],
    popupAnchor: [0, -34],
    iconUrl: "./images/unavailable-marker.png"
};

const MARKER_DRANK_STYLE = {
    iconSize: [25, 38],
    iconAnchor: [12.5, 38],
    popupAnchor: [0, -34],
    iconUrl: "./images/drank-marker.png"
};

const COORD_CENTRE_PARIS = [48.856614, 2.3522219];
const NB_ARRONDISSEMENT_PARIS = 20;
const JAWG_TOKEN = "iKMSfgXFP3b7DLW1qBal7bue3TA90WZlvJ0Jto8hhBEPgNW5vrBb1nU1kZldsaUI";

let userID;
let groupID;
let map;
let tiles;
let arrondissementsPoly;
let fontainesData;
let fontainesMarkers;
let zoom;
let userCircle;
let userPosition;
let lastArrondChosen;
let showUnavailable = true;
let showAvailable = true;
let showDrank = true;
let currentRoute;
let geocodeService;


function Fontaine(fontaine) {
    this.id = fontaine.ID;
    this.geoJSONData = fontaine.Coords;
    this.disponible = fontaine.Disponible;
    this.rue = fontaine.Rue;
    this.groupeID = fontaine.ID_Groupe == null;
    this.bu = fontaine.ID_Utilisateur != null;
}

$(document).ready(init);

function init() {
    userID = $("#ID_User").text();
    groupID = $("#ID_Groupe").text();

    fontainesData = new Array(NB_ARRONDISSEMENT_PARIS);

    setupMap();

    setupArrondissementPolygons();

    getDataFontaines();

    zoom = {
        start: map.getZoom(),
        end: map.getZoom()
    };

    currentRoute = new L.LayerGroup();
}

function setupMap() {
    map = L.map('Map', {
        center: COORD_CENTRE_PARIS,
        zoom: 12
    });

    tiles = L.tileLayer(`https://tile.jawg.io/jawg-sunny/{z}/{x}/{y}.png?access-token=${JAWG_TOKEN}`, {
        attribution: '<a href="http://jawg.io" title="Tiles Courtesy of Jawg Maps" target="_blank" class="jawg-attrib">&copy; <b>Jawg</b>Maps</a> | <a href="https://www.openstreetmap.org/copyright" title="OpenStreetMap is open data licensed under ODbL" target="_blank" class="osm-attrib">&copy; OSM contributors</a>',
        maxZoom: 19,
    }).addTo(map);


    // On retire le dblclick zoom et on le remplace
    map.doubleClickZoom.disable();
    map.on("dblclick", (event) => createNewFountain(event));

    // Map realiste
    // tiles = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    // }).addTo(map);
}

function setupArrondissementPolygons() {
    $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=arrondissements&q=&rows=20&facet=c_ar&facet=c_arinsee&facet=l_ar",
        (data) => {
            arrondissementsPoly = new Array(data.records.length);
            for (arrondissement of data.records) {
                // L'API renvoie les coordonnées en format long lat et il nous faut l'inverse
                let coordInv = invertCoordList(arrondissement.fields.geom.coordinates[0]);

                // Evite d'inverser les coords mais cause d'autres problemes sur d'autres fctions
                // arrondissementsPoly[Number(arrondissement.fields.c_ar) - 1] = L.geoJSON(arrondissement.fields.geom, {
                //     pointToLayer: (feature, latlng) => { return L.polygon(latlng, POLY_STYLE_DEFAULT); }
                // })
                arrondissementsPoly[Number(arrondissement.fields.c_ar) - 1] = L.polygon(coordInv, POLY_STYLE_DEFAULT)
                    .on({
                        "click": (event) => handleClickArrondissement(event),
                        "mouseover": (event) => handleHoverInArrondissement(event),
                        "mouseout": (event) => handleHoverOutArrondissement(event)
                    })
                    .addTo(map);

                // On planifie la structure pour classer les fontaines
                fontainesData[Number(arrondissement.fields.c_ar) - 1] = {
                    arrondissement: Number(arrondissement.fields.c_ar) - 1,
                    data: []
                };
            }
        }
    );
}

function invertCoordList(list) {
    coordInv = Array(list.length);
    let cpt = 0;
    for (coord of list) {
        coordInv[cpt] = [coord[1], coord[0]];
        cpt++;
    }
    return coordInv
}

function setupUserPosition() {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            userPosition = [position.coords.latitude, position.coords.longitude];
            // userPosition = [48.84223513100503, 2.2679049153440825]
            putUserCircleMarker(true);
        },
        (error) => console.log("User denied access to geolocation")
    );
}

function putUserCircleMarker(showPos) {
    userCircle != null ? userCircle.remove() : null;
    userCircle = L.circleMarker(userPosition, {
        radius: 8,
        weight: 8,
        color: "#0000FF",
        opacity: 0.3,
        fillColor: "#0000FF",
        fillOpacity: 1
    }).addTo(map).bindPopup("User position");
    if (showPos) map.setView(userPosition, 17);
}

function getDataFontaines() {
    $.ajax({
        url: `./PHPScripts/getFontaines.php`,
        type: 'GET',
        data: {
            groupeID: groupID,
            userID: userID,
        },
        success: (fontaines) => {
            console.log(fontaines);
            for (fontaine of fontaines) {
                arrond = getArrondPoint([fontaine.Coords.coordinates[1], fontaine.Coords.coordinates[0]]);
                if (arrond != null) {
                    fontainesData[arrond].
                        data.push((new Fontaine(fontaine)));
                }
            }
        },
        error: (err)=> console.log('fail') 
    });
}

function getArrondPoint(point) {
    while (arrondissementsPoly.length == undefined) {
        console.log("wait");
    }
    for (idx = 0; idx < arrondissementsPoly.length; idx++) {
        // sans raycasting si vous voulez tester
        // if(arrondissementsPoly[idx].getBounds().contains(point)) {
        //     return idx;
        // }
        if (pointInsidePolygon(arrondissementsPoly[idx], point)) {
            return idx;
        }
    }
    return null;
}


// Algorithme de raycasting
function pointInsidePolygon(poly, point) {
    let polyPoints = poly.getLatLngs()[0];
    let x = point[0], y = point[1];

    let inside = false;
    for (i = 0, j = polyPoints.length - 1; i < polyPoints.length; j = i++) {
        let xi = polyPoints[i].lat, yi = polyPoints[i].lng;
        let xj = polyPoints[j].lat, yj = polyPoints[j].lng;

        let intersect = ((yi > y) != (yj > y))
            && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
};

function removeFountainMarkers() {
    if (fontainesMarkers != null) {
        for (marker of fontainesMarkers) {
            map.removeLayer(marker);
        }
    }
}

function showFountainMarkersInArrond(arrond) {
    centerOfPoly = arrond.getBounds().getCenter();
    arrond = getArrondPoint([centerOfPoly.lat, centerOfPoly.lng]);
    fontainesMarkers = [];
    if (arrond == null) arrond = 11;
    for (idx in fontainesData[arrond].data) {
        if (showUnavailable && !fontainesData[arrond].data[idx].disponible) {
            createFountainMarker(arrond, idx);
        }
        else if (showAvailable && fontainesData[arrond].data[idx].disponible){
            createFountainMarker(arrond, idx);
        }
        else if (showDrank && fontainesData[arrond].data[idx].bu) {
            createFountainMarker(arrond, idx);
        }
    }
}

function createFountainMarker(arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];

    let iconStyle;
    if (fontaine.bu) iconStyle = L.icon(MARKER_DRANK_STYLE);
    else if (fontaine.disponible) iconStyle = L.icon(MARKER_AVAILABLE_STYLE);
    else iconStyle = L.icon(MARKER_UNAVAILABLE_STYLE);

    let marker = L.geoJSON(fontaine.geoJSONData, {
        pointToLayer: (feature, latlng) => { return L.marker(latlng, { icon: iconStyle }); }
    }).addTo(map);

    createFountainMarkerText(marker, arrond, idx);

    fontainesMarkers.push(marker);
}

function handleClickArrondissement(event) {
    if (lastArrondChosen != event.target) {
        // We put the last chosen poly back to it's default state
        if (lastArrondChosen != null) lastArrondChosen.setStyle(POLY_STYLE_DEFAULT);

        // We fit the arrondissement 
        map.fitBounds(event.target.getBounds(), { padding: [-66, -66] });

        // On supprime les markers precedents
        removeFountainMarkers();


        lastArrondChosen = event.target;
        lastArrondChosen.setStyle(POLY_STYLE_SELECTED);

        // On affiche les fontaines dans l'arrondissement choisi
        showFountainMarkersInArrond(lastArrondChosen);
    }
}

function handleHoverInArrondissement(event) {
    event.target.bringToFront();
    event.target.setStyle(POLY_STYLE_SELECTED);
}

function handleHoverOutArrondissement(event) {
    if (event.target != lastArrondChosen) {
        event.target.setStyle(POLY_STYLE_DEFAULT);
        event.target.bringToBack();
    }
    else {
        event.target.bringToFront();
    }
}

function createNewFountain(event) {
    activateGeoCodeService();

    point = [event.latlng.lat, event.latlng.lng];
    arrond = getArrondPoint(point);

    geoPoint = [event.latlng.lng, event.latlng.lat];

    if (arrond != null) {
        geocodeService.reverse().latlng(event.latlng).run(function (error, result) {
            if (error) {
                return;
            }

            voie = result.address.Match_addr.split(",")[0] != null ?
                result.address.Match_addr.split(",")[0] : result.address.Match_addr;

            $.ajax({
                url: "./PHPScripts/addFontaine.php",
                type: "GET",
                data:  {
                    coordinates: geoPoint,   
                    disponible: true,
                    rue: voie,
                    groupeID: $("#ID_Groupe").text()
                },
                dataType: 'json',
                success: (fontaine) => {
                    console.log(fontaine);
                    if (arrond != null) {
                        fontainesData[arrond].data.push((new Fontaine(fontaine)));
                        createFountainMarker(arrond, fontainesData[arrond].data.length - 1);
                        refreshMarkers();
                    }
                },
                error: (data) => console.log("failed")
            });
        });
    }
}

function activateGeoCodeService() {
    if (geocodeService == null) {
        geocodeService = L.esri.Geocoding.geocodeService({
            apikey: "AAPK56a058469da84b49ab85187815497bccIz0_IwPeerd6C4ta8ta2SF-i8qmCEOftByx8qRKLaIh_RgB4NvMb1nrXPtYr2Ks2" // replace with your api key - https://developers.arcgis.com
        });
    }
}

function createFountainMarkerText(marker, arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];
    marker.bindPopup(`
                    <div class="popup-${fontaine.disponible ? "dispo" : "indispo"} popup-${fontaine.bu ? "bu" : "pasbu"}">
                        <b>${fontaine.rue}</b>
                        <div class="popup-info">
                            <p> Disponible : <span class="status status-dispo">${fontaine.disponible ? "OUI" : "NON"}</span> </p> 
                            <p> Bu ici : <span class="status status-bu">${fontaine.bu ? "OUI" : "NON"}</span> </p> 
                            <button class="popup-btn popup-btn-bu" onclick="toggleDrink(${arrond}, ${idx})">
                                 ${fontaine.bu ? "Je n'ai pas bu ici" : "J'ai bu ici"}
                            </button>
                            <button class="popup-btn popup-btn-dispo" onclick="toggleDispoFontaine(${arrond}, ${idx})">
                                Rendre ${fontaine.disponible ? "indisponible" : "disponible"}
                            </button>
                            ${fontaine.groupeID ? "" : "<button class='popup-btn popup-btn-remove' onclick='removeFontaine(" + arrond + "," + idx + ")'>Supprimer</button>"}
                        </div>
                    </div>`), {
        className: "popup"
    };
}

// `<button class='popup-btn popup-btn-remove' onclick='removeFountain(${fontaine.id})'>
//     Retirer fontaine
// </button>`

function toggleDispoFontaine(arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];
    fontaine.disponible = !fontaine.disponible;
    removeFountainMarkers();
    showFountainMarkersInArrond(lastArrondChosen);
}

function toggleDrink(arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];
    $.ajax({
        url: './PHPScripts/updateFontaineBu.php',
        type: 'GET',
        data: {
            fontaineID: fontaine.id
        },
        success: (data) => {
            fontaine.bu = !fontaine.bu;
            refreshMarkers();
        }
    })
}


async function handleClickRouting() {
    navigator.geolocation.getCurrentPosition((position) => {
        userPosition = [position.coords.latitude, position.coords.longitude];
        // userPosition = [48.84223513100503, 2.2679049153440825]
        putUserCircleMarker(false);

        let arrond = getArrondPoint(userPosition);

        if (arrond == null) alert("Vous n'êtes pas à Paris !")
        else {
            // We find the closest point from the user
            closestFountain = getClosestFountain(arrond);
            showFountainMarkersInArrond(arrondissementsPoly[arrond]);
            lastArrondChosen = arrondissementsPoly[arrond];
            calculateRouteFromPosition([closestFountain.geoJSONData.coordinates[1],
            closestFountain.geoJSONData.coordinates[0]]);
        }
    });
}

var routingService = new H.service.Platform({
    apikey: "tSrrfY12xrYTQcKrFINr0Wd8DeI8DCRAcdnjuprI_xE"
});

function getClosestFountain(arrond) {
    let minDistance = distanceBetweenTwoPoints([
        fontainesData[arrond].data[0].geoJSONData.coordinates[1],
        fontainesData[arrond].data[0].geoJSONData.coordinates[0]
    ], userPosition);

    let closestFountain = fontainesData[arrond].data[0];
    for (fontaine of fontainesData[arrond].data) {
        if (fontaine.disponible) {
            let distance = distanceBetweenTwoPoints([
                fontaine.geoJSONData.coordinates[1],
                fontaine.geoJSONData.coordinates[0]
            ], userPosition);
            if (distance < minDistance) {
                minDistance = distance;
                closestFountain = fontaine;
            }
        }
    }
    return closestFountain;
}

function calculateRouteFromPosition(nearestFountainCoord) {

    deleteCurrentRoute();

    var router = routingService.getRoutingService(null, 8),
        routeRequestParams = {
            routingMode: 'fast',
            transportMode: 'pedestrian',
            origin: `${userPosition[0]},${userPosition[1]}`,
            destination: `${nearestFountainCoord[0]},${nearestFountainCoord[1]}`,
            return: 'polyline,turnByTurnActions,actions,instructions,travelSummary'
        };

    router.calculateRoute(
        routeRequestParams,
        onSuccessfulRoute,
        onErrorRoute
    );
}

function distanceBetweenTwoPoints(p1, p2) {
    return Math.sqrt(Math.pow(p2[0] - p1[0], 2) + Math.pow(p2[1] - p1[1], 2))
}

function onSuccessfulRoute(result) {
    let route = result.routes[0];

    addTrajectoryToMap(route);
    addStepsToMap(route);

    currentRoute.setZIndex(100000000);
    map.addLayer(currentRoute, true);
}

function onErrorRoute(error) {
    alert('Can\'t reach the remote server');
}

function addTrajectoryToMap(route) {
    route.sections.forEach((section) => {
        let linestring = H.geo.LineString
            .fromFlexiblePolyline(section.polyline);
        let polyline = []
        linestring.eachLatLngAlt((lat, lng, alt, idx) => polyline.push([lat, lng]));
        polyline = L.polyline(polyline, {
            weight: 4,
            color: "#0000FF",
            opacity: 0.75,
        }
        );
        currentRoute.addLayer(polyline);

        // zoom the map to the polyline
        map.fitBounds(polyline.getBounds());
    });
}

function addStepsToMap(route) {
    route.sections.forEach((section) => {
        let poly = H.geo.LineString.fromFlexiblePolyline(section.polyline).getLatLngAltArray();

        var stepIcon = L.icon({
            iconSize: [20, 20],
            iconUrl: './images/route-step-marker.png',
        });

        // Add a marker for each maneuver
        for (action of section.actions) {
            currentRoute.addLayer(
                new L.Marker([
                    poly[action.offset * 3],
                    poly[action.offset * 3 + 1]
                ], {
                    icon: stepIcon
                })
                    .bindPopup(action.instruction)
            );
        }
    });
}

function deleteCurrentRoute() {
    // We delete the previous route
    currentRoute.clearLayers();
}

function handleCloseInformation() {
    $("#InformationText").hide();
    $("#InformationText").css("opacity", 0)    
}

function handleShowInformation() {
    // $("#InformationText").css("display", "flex");
    $("#InformationText").show()
    $("#InformationText").animate({
        opacity: 1,
    }, 300);
}

function refreshButtonTexts() {
    if (showUnavailable) {
        $("#ButtonToggleMarkersDispo > .togglableText").html("Cacher")
    }
    else {
        $("#ButtonToggleMarkersDispo > .togglableText").html("Montrer")
    }

    if (!showAvailable && !showUnavailable) {
        $("#ButtonShowOnlyDrank > .togglableText").html("Montrer");
    }
    else {
        $("#ButtonShowOnlyDrank > .togglableText").html("Cacher")
    }
}

function refreshMarkers() {
    if (lastArrondChosen != null) {
        removeFountainMarkers();
        showFountainMarkersInArrond(lastArrondChosen);
    }
}

function handleClickToggleMarkersDispo() {
    showUnavailable = !showUnavailable
    refreshButtonTexts();
    refreshMarkers();
}


function handleClickToggleNotDrank() {
    if (showAvailable || showUnavailable) {
        showAvailable = showUnavailable = false;
    }
    else {
        showAvailable = showUnavailable = true;
    }
    refreshButtonTexts();
    refreshMarkers();
}

function handleClickShowAll() {
    showAvailable = showUnavailable = showDrank = true;
    refreshButtonTexts();
    refreshMarkers();
}

function removeFontaine(arrond, indexFontaine) {
    $.ajax({
        url: "./PHPScripts/deleteFontaine.php",
        method: 'POST',
        data: { "fontaineID" : fontainesData[arrond].data[indexFontaine].id },
        success: (data) => {
            fontainesData[arrond].data.splice(indexFontaine, 1);
            refreshMarkers();
        }
    });
}