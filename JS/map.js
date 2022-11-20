const ARRONDISSEMENT_DEFAULT_COLOR = "#ED820E";
const ARRONDISSEMENT_HOVER_COLOR = "#FF0000";
const COORD_CENTRE_PARIS = [48.856614, 2.3522219];
const NB_ARRONDISSEMENT_PARIS = 20;
let map;
let tiles;
let arrondissements;
let fontainesData;
let fontainesMarkers;
let zoom;
let userCircle;

// Constructeur d'une fontaine dans un arrondissement
function Fontaine(geoJSONData, disponible) {
    this.geoJSONData = geoJSONData;
    this.disponible = disponible=="OUI"?true:false;
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

    tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Map realiste
    // tiles = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    //     attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    // }).addTo(map);
}

function setupArrondissementPolygons() {
    $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=arrondissements&q=&rows=20&facet=c_ar&facet=c_arinsee&facet=l_ar",
        (data) => {
            arrondissements = new Array(data.records.length);
            for (arrondissement of data.records) {
                // L'API renvoie les coordonnées en format long lat et il nous faut l'inverse
                let coordInv = invertCoordList(arrondissement.fields.geom.coordinates[0]);
                
                // On enregistre chaque polygone
                arrondissements[Number(arrondissement.fields.c_ar) - 1] = L.polygon(coordInv, {
                        color: ARRONDISSEMENT_DEFAULT_COLOR,
                        opacity: 1,
                        fillColor: ARRONDISSEMENT_DEFAULT_COLOR,
                        fillOpacity: 0.15
                    })
                    .on("click",
                        (event) => handleClickArrondissement(event)
                    )
                    .on("mouseover",
                        (event) => handleHoverInArrondissement(event)
                    )
                    .on("mouseout",
                        (event) => handleHoverOutArrondissement(event)
                    )  
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
                (error) => console.log("Bug in user tracking")
            );
}

function getDataFontaines() {
    $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=fontaines-a-boire&q=&rows=10000&facet=type_objet&facet=modele&facet=commune&facet=dispo",
            (data) => {
                for(fontaine of data.records) {
                    arrond = getArrondPoint(fontaine.fields.geo_point_2d)
                    if (arrond != null) {
                        fontainesData[arrond].
                        data.push(new Fontaine(fontaine.fields.geo_shape, fontaine.fields.dispo));
                    }
                }
            });
}

function getArrondPoint(point) {
    for(idx in arrondissements) {
        if (arrondissements[idx].getBounds().contains(point)) {
           return idx;
        }
    }
    return null;
}

function handleClickArrondissement(event) {
    // userCircle != null ? userCircle.remove() : null;
    map.fitBounds(event.target.getBounds(), { padding: [-66, -66] });

    // On supprime les markers precedents
    if (fontainesMarkers!=null) {
        for(marker of fontainesMarkers) {
            map.removeLayer(marker);
        }
    } 

    // On affiche les fontaines dans l'arrondissement choisi
    arrond = getArrondPoint(event.target.getCenter());
    fontainesMarkers = [];
    for(fontaine of fontainesData[arrond].data) {
        if(fontaine.disponible) {
            let marker = L.geoJSON(fontaine.geoJSONData).addTo(map);
            fontainesMarkers.push(marker);
        }
    }
}

function handleHoverInArrondissement(event) {
    event.target.bringToFront();
    event.target.setStyle({
        color: ARRONDISSEMENT_HOVER_COLOR,
        opacity: 1,
        fillColor: ARRONDISSEMENT_HOVER_COLOR,
        fillOpacity: 0.15
    });
}
function handleHoverOutArrondissement(event) {
    event.target.setStyle({
        color: ARRONDISSEMENT_DEFAULT_COLOR,
        opacity: 1,
        fillColor: ARRONDISSEMENT_DEFAULT_COLOR,
        fillOpacity: 0.15
    });
}