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

const COORD_CENTRE_PARIS = [48.856614, 2.3522219];
const NB_ARRONDISSEMENT_PARIS = 20;
const JAWG_TOKEN = "iKMSfgXFP3b7DLW1qBal7bue3TA90WZlvJ0Jto8hhBEPgNW5vrBb1nU1kZldsaUI";

let map;
let tiles;
let arrondissementsPoly;
let fontainesData;
let fontainesMarkers;
let zoom;
let userCircle;
let lastArrondChosen;
let showUnavailable = false;


let geocodeService;
// Constructeur d'une fontaine dans un arrondissement
/*
    fontaine: {
        geo_shape: GeoJSONObject,
        dispo: "OUI"|"NON",
        voie: string,
        no_voirie_impair | *_*_pair: string
    }
*/
function Fontaine(fontaine, isDefault) {
    this.geoJSONData = fontaine.geo_shape;
    this.disponible = fontaine.dispo=="OUI"?true:false;

    this.rue = " ";
    if (fontaine.no_voirie_impair != undefined) this.rue += fontaine.no_voirie_impair
    else if(fontaine.no_voirie_pair != undefined) this.rue += fontaine.no_voirie_pair;
    this.rue += " " + fontaine.voie//.toUpperCase();

    this.isDefault = isDefault;
}

$(document).ready(init);

function init() {
    fontainesData = new Array(NB_ARRONDISSEMENT_PARIS);

    setupMap();

    setupArrondissementPolygons();

    getDataFontaines();

    $("#MyPosition").click(setupUserPosition);

    zoom = {
        start:  map.getZoom(),
        end: map.getZoom()
    };
}

function setupMap() {
    map = L.map('map', {
        center: COORD_CENTRE_PARIS,
        zoom: 12
    });

    tiles = L.tileLayer(`https://tile.jawg.io/jawg-sunny/{z}/{x}/{y}.png?access-token=${ JAWG_TOKEN }`, {
        attribution: '<a href="http://jawg.io" title="Tiles Courtesy of Jawg Maps" target="_blank" class="jawg-attrib">&copy; <b>Jawg</b>Maps</a> | <a href="https://www.openstreetmap.org/copyright" title="OpenStreetMap is open data licensed under ODbL" target="_blank" class="osm-attrib">&copy; OSM contributors</a>',
        maxZoom: 19,
    }).addTo(map);


    // On retire le dblclick zoom et on le remplace
    map.doubleClickZoom.disable(); 
    map.on("dblclick", (event) => createNewFountain(event))

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
                    userCircle != null ? userCircle.remove() : null;
                    userCircle = L.circleMarker([position.coords.latitude, position.coords.longitude], {
                        radius: 8,
                        weight: 8,
                        color: "#0000FF",
                        opacity: 0.3,
                        fillColor: "#0000FF",
                        fillOpacity: 1
                    }).addTo(map).bindPopup("User position");
                    map.setView(new L.LatLng(position.coords.latitude, position.coords.longitude), 17);
                }, 
                (error) => console.log("User denied access to geolocation")
            );
}

function getDataFontaines() {
    $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=fontaines-a-boire&q=&rows=10000&facet=type_objet&facet=modele&facet=commune&facet=dispo",
            (data) => {
                for(fontaine of data.records) {
                    arrond = getArrondPoint(fontaine.fields.geo_point_2d)
                    if (arrond != null) {
                        fontainesData[arrond].
                        data.push((new Fontaine(fontaine.fields, true)));
                    }
                }
            });
}

function getArrondPoint(point) {
    for(idx = 0; idx < arrondissementsPoly.length; idx++) {
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
    if (fontainesMarkers!=null) {
        for(marker of fontainesMarkers) {
            map.removeLayer(marker);
        }
    } 
}

function showFountainMarkersInArrond(arrond) {
    centerOfPoly = arrond.getBounds().getCenter();
    arrond = getArrondPoint([centerOfPoly.lat, centerOfPoly.lng]);
    fontainesMarkers = [];
    if (arrond == null) arrond = 11;
    for(idx in fontainesData[arrond].data) {
        if(showUnavailable || fontainesData[arrond].data[idx].disponible) {
            createFountainMarker(arrond, idx);
        }
    }
}

function createFountainMarker(arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];

    let iconStyle;
    if (fontaine.disponible) iconStyle = L.icon(MARKER_AVAILABLE_STYLE);
    else iconStyle = L.icon(MARKER_UNAVAILABLE_STYLE);
    
    let marker = L.geoJSON(fontaine.geoJSONData, {
        pointToLayer: (feature, latlng) => { return L.marker(latlng, {icon: iconStyle}); }
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

function handleClickToggleMarkersDispo() {
    showUnavailable = !showUnavailable
    if (showUnavailable) {
        $("#ButtonToggleMarkersDispo > .togglableText").html("Hide")
    } 
    else  {
        $("#ButtonToggleMarkersDispo > .togglableText").html("Show")
    }
    if (lastArrondChosen != null) {
        removeFountainMarkers();
        showFountainMarkersInArrond(lastArrondChosen);
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
            newFountain  = new Fontaine({
                geo_shape:  { coordinates: geoPoint,
                              type: "Point"
                        },
                dispo: "OUI",
                voie: voie,
                no_voirie_impair: null
            }, false);

            fontainesData[arrond].data.push(newFountain);

            createFountainMarker(arrond, fontainesData[arrond].data.length - 1);
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
    marker.bindPopup(`<b>${ fontaine.rue }</b>
                        <br>
                        Disponible : ${ fontaine.disponible ? "OUI" : "NON" }
                        <button onclick="toggleDispoFontaine(${ arrond }, ${ idx })">
                        Rendre ${ fontaine.disponible ? "indisponible" : "disponible"  }</button>`);
}

function toggleDispoFontaine(arrond, idx) {
    let fontaine = fontainesData[arrond].data[idx];
    console.log("clicked for " + arrond + " -> " + idx);
    fontaine.disponible = !fontaine.disponible;
    removeFountainMarkers();
    showFountainMarkersInArrond(lastArrondChosen);
}