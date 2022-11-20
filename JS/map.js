const COORD_CENTRE_PARIS = [48.856614, 2.3522219];
let map;
let tiles;
let arrondissements;
let fontainesData;
let zoom;
let userCircle;

$(document).ready(init);

function init() {
    setupMap();

    setupArrondissementPolygons();

    getDataFontaines();

    $("#MyPosition").click(setupUserPosition);

    zoom = {
        start:  map.getZoom(),
        end: map.getZoom()
    };

    map.on('zoomstart', handleZoomStart);
    
    map.on('zoomend', handleZoomEnd);
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
                let coordInv = Array(arrondissement.fields.geom.coordinates[0].length);
                let cpt = 0;
                for (coord of arrondissement.fields.geom.coordinates[0]) {
                    coordInv[cpt] = [coord[1], coord[0]];
                    cpt++;
                }
                // On enregistre chaque polygone
                arrondissements[Number(arrondissement.fields.c_ar) - 1] = L.polygon(coordInv, {
                        opacity: 0,
                        fillOpacity: 0,
                        id: Number(arrondissement.fields.c_ar) - 1
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
                console.log(arrondissements[Number(arrondissement.fields.c_ar) - 1]) 
            }
        }
    );
}

function setupUserPosition() {
    navigator.geolocation.getCurrentPosition(
                (position) => {
                    userCircle != null ? userCircle.remove() : null;
                    userCircle = L.circle([position.coords.latitude, position.coords.longitude], {
                        radius: 8,
                        weight: 8,
                        opacity: 0.3,
                        color: "#0000FF",
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
            (data) => fontainesData = data);
}

// $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=fontaines-a-boire&q=&rows=10000&facet=type_objet&facet=modele&facet=commune&facet=dispo",
// (data) => {
//     fontainesData = data;
//     for (fontaine of data.records) {
//         L.marker([fontaine.fields.geo_shape.coordinates[1], fontaine.fields.geo_shape.coordinates[0]]).addTo(map);
//     }
// });

function handleZoomStart(e) {
    zoom.start = map.getZoom();
}

// Scales user position circle to map
function handleZoomEnd(e) {
    zoom.end = map.getZoom();
    if (userCircle != null) {
        let diff = zoom.start - zoom.end;
        if (diff > 0) {
            userCircle.setRadius(userCircle.getRadius() * 2);
        } else if (diff < 0) {
            userCircle.setRadius(userCircle.getRadius() / 2);
        }
    }
}

function handleClickArrondissement(arrondissement) {
    userCircle != null ? userCircle.remove() : null;
    map.fitBounds(arrondissement.target.getBounds());
}

function handleHoverInArrondissement(arrondissement) {
    arrondissement.target.setStyle({
        color: "#FF0000",
        opacity: 1,
        fillColor: "#FF0000",
        fillOpacity: 0.15
    });
}
function handleHoverOutArrondissement(arrondissement) {
    arrondissement.target.setStyle({
        opacity: 0,
        fillOpacity: 0
    });
}