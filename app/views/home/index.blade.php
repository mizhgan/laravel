@extends('layouts.main')

@section('title')
Карта покрытия WiFi и GSM Кирово-Чепецка
@stop

@section('description')
Карта покрытия WiFi и GSM Кирово-Чепецка. Открытые и закрытые точки доступа WiFi В Кирово-Чепецке. Точки WiFi с шифрованием WPA, WPA2, WEP, WPS.
@stop

@section('main')

<div id="map"></div>

@stop

@section('scripts')

<script>
// create a map in the "map" div, set the view to a given place and zoom
var map = L.map('map').setView([58.5436, 50.0429], 15);

var hash = new L.Hash(map);

// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


$(document).ready(function(){ 

    $('.dropdown-menu input, .dropdown-menu label').click(function(e) {
        e.stopPropagation();
    });

    $('#debug').html("Загрузка GEOjson");

    var pointMarkerOptions = {
        radius: 8,
        fillColor: "#ff7800",
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
    };

    var wifiopen = L.AwesomeMarkers.icon({
                                                icon: 'ok-sign', 
                                                color: 'blue'
                                            });
    var wificlose = L.AwesomeMarkers.icon({
                                                icon: 'remove-sign', 
                                                color: 'red'
                                            });
    var gsm = L.AwesomeMarkers.icon({
                                                icon: 'phone', 
                                                color: 'purple'
                                            });

    var points = L.geoJson(null, {
            onEachFeature: function (feature, layer) {
                var popupMessage = '<dl class="dl-horizontal"><dt>BSSID:</dt><dd><a href="/bssid/' + feature.properties.bssid + '">' + feature.properties.bssid + '</a></dd><dt>SSID:</dt><dd>' + feature.properties.ssid + '</dd><dt>Возможности:</dt><dd>' + feature.properties.capabilities + '</dd><dt>Уровень сигнала:</dt><dd>' + feature.properties.level + 'db</dd><dt>Последний замер:</dt><dd>' + feature.properties.time + '</dd></dl>';
                layer.bindPopup(popupMessage);
            },
            pointToLayer: function (feature, latlng) {
                //markers.addLayer(new L.Marker(latlng));
                if (feature.properties.type == 'G') {
                    outicon = gsm;
                } else {
                    if (feature.properties.open) {
                        outicon = wifiopen;
                    } else {
                        outicon = wificlose;
                    }
                }
                return new L.Marker(latlng, {icon: outicon});
            }
        });

    function makeSsidDatumArray() {
        var ret = [];
        tomap.eachLayer(function (point) {
            ret.push({  value: point.feature.properties.ssid,
                        point: point
                    });
        });
        return ret;
    }
    function makeBssidDatumArray() {
        var ret = [];
        tomap.eachLayer(function (point) {
            ret.push({  value: point.feature.properties.bssid,
                        point: point
                    });
        });
        return ret;
    }

    var markers = new L.MarkerClusterGroup({ spiderfyOnMaxZoom: true, /* disableClusteringAtZoom: 18, */ maxClusterRadius: 40});
    map.spin(true);
    var starttime = new Date().getTime();

    var tomap = new L.LayerGroup();

    function showmarkers() {

        $('#main-search').typeahead('destroy');
        tomap.clearLayers();
        var showwifi, showgsm, showopen, showclose = false;
        $.each($("[name='showselect']:checked"), function(a, b) {
                if (b.value == 'W') showwifi = true;
                if (b.value == 'G') showgsm = true;
                if (b.value == 'open') showopen = true;
                if (b.value == 'close') showclose = true;
            });
        points.eachLayer(function (point) {

            if (point.feature.properties.type == 'W') {
                if (showwifi) {
                    if (point.feature.properties.open) {
                        if (showopen) tomap.addLayer(point);
                    } else {
                        if (showclose) tomap.addLayer(point);
                    }
                }
            } else {
                if (showgsm) tomap.addLayer(point);
            }
            
        });

        markers.clearLayers();
        markers.addLayer(tomap);

        //Поиск с автокомплитом
        $('#main-search').typeahead([
          {
            name: 'ssid',
            local: makeSsidDatumArray()
          },
          {
            name: 'bssid',
            local: makeBssidDatumArray()
          }
        ]);

        //console.log($('#main-search').typeahead());
    }

    showselect = $('[name="showselect"]')
    showselect.change(function() {
            showmarkers();
        });

    $.getJSON('/networks', function (json) {
        var endtime = new Date().getTime();
        $('#debug').html(endtime-starttime);
        map.spin(false);
        points.addData(json);
        
        map.addLayer(markers);

        //markers.addLayer(points);
        showmarkers();

        console.log(points);


        

        $('#main-search').on('typeahead:selected', function(e, d){
            //map.setView(d.point.getLatLng(), 18);
            markers.zoomToShowLayer(d.point, function(){
                d.point.openPopup();
            });
            //setTimeout(function() { d.point.openPopup(); }, 1000);
            
        });
    });

});

</script>

@stop