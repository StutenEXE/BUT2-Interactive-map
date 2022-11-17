window.onload = init ;

let drawing = false;
let dynamicPoly;

function init() {
    // If error, we set default coordinates to IUT de Paris Descartes
    navigator.geolocation.getCurrentPosition(
        (position) => setupMap([position.coords.latitude,position.coords.longitude]), 
        (error) => setupMap([48.84158851946866, 2.2678810636885673]));
}

function setupMap(coord) {
    const map = L.map('map').setView(coord, 15);

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    
    // We set user on map
    L.circle(coord, {
        radius: 20,
        color: "#378805",
        fillColor: "#378805",
        fillOpacity: 1
    }).addTo(map).bindPopup('User position');;

    $.getJSON("https://opendata.paris.fr/api/records/1.0/search/?dataset=fontaines-a-boire&q=&rows=10000&facet=type_objet&facet=modele&facet=commune&facet=dispo",
            (data) => {
                for (fontaine of data.records) {
                    L.marker([fontaine.fields.geo_shape.coordinates[1], fontaine.fields.geo_shape.coordinates[0]]).addTo(map);
                }
            }
        );
}
