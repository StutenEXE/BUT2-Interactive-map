window.onload = init ;

let drawing = false;
let dynamicPoly;

function init() {
    const map = L.map('map').setView([48.84158851946866, 2.2678810636885673], 18);

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const cadreIUT = L.polygon([
        [48.84195598493401, 2.26763558956573],
        [48.841779458174834, 2.267935996977477],
        [48.84137697483277, 2.268788939449336],
        [48.84185713002085, 2.269271737075358],
        [48.84213251107035, 2.2685904559813816],
        [48.842047778602215, 2.2684563455297084],
        [48.84224548747152, 2.268188124626363],
        [48.842040717557396, 2.2678662595426067]
        ], {
        color: '#FF0000',
        fillColor: '#FFA2A2',
        opacity: 0.75 
        }).addTo(map).bindPopup("IUT Paris Descartes");

    const cercleConcorde= L.circle([48.865484005449844, 2.321141822687012],{
                                    color: '#0000FF',
                                    fillColor: 'A2A2FF',
                                    opacity: 0.75,
                                    radius : 70
                                }).addTo(map).bindPopup("Place de la Concorde");
                        
    createdDynamicPoly();

    function onMapClick(e) {
        if(drawing) {
            dynamicPoly.addLatLng(e.latlng);
        }
    }

    map.on('click', onMapClick);


    function onMapClickUpdate(e) {
        drawing = !drawing;
    }
    map.on('contextmenu', onMapClickUpdate);

    function createdDynamicPoly() {
        dynamicPoly = L.polygon([], {
            color: '#FF0000',
            fillColor: '#FFA2A2',
            opacity: 0.75 
            }).addTo(map).bindPopup("IUT Paris Descartes");
        dynamicPoly.on('click', deletePoly);
    }
    
    function deletePoly(e) {
        dynamicPoly.remove();
        createdDynamicPoly();
    }


    $.getJSON("https://velib-metropole-opendata.smoove.pro/opendata/Velib_Metropole/station_information.json",
            (data) => {
                for (station of data.data) {
                    L.marker([station.lat, station.lon]).addTo(map);
                }
            });
}