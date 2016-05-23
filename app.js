/**
 * Created by op49265 on 17.03.2016.
 */

var osmURL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osm = new L.TileLayer(osmURL, {maxZoom: 18});

var defaultview = {
    zoomlevel: 8,
    center: [52.8, 8]
};

function setDefaultView(map,center,zoomlevel){
    map.setView(center, zoomlevel);
}

var map = L.map('map', {});
map.addLayer(osm);

setDefaultView(map,defaultview.center,defaultview.zoomlevel);

$.getJSON('data.json',function(data){
    $.getJSON('getAreas.php',function(areaData){
        for(var i = 0; i < areaData.length; i++){
            console.log(areaData[i]);
            L.geoJson(areaData[i],{
                style: style,
                onEachFeature: onEachFeature
            }).bindLabel(areaData[i].properties.name + "<br>Nodes: "+data[areaData[i].id]).addTo(map);
            //console.log("Nodes: "+data[areaData[i].id]);
        }
    });

});

function style(feature) {
    return {
        fillColor: '#'+Math.floor(Math.random()*16777215).toString(16),
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.5
    };
}

function onEachFeature(feature, layer) {
    /*layer.on({
        mouseover: highlightFeature,
        mouseout: resetHighlight,
        click: zoomToFeature
    });*/
}



