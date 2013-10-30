@extends('layouts.scaffold')

@section('main')

<h1>Информация о точке {{{ $network->bssid }}} {{{ $network->ssid }}}</h1>

<p>{{ link_to_route('networks.index', 'Вернуться ко всем точкам') }}</p>

@include('networks._networkdetailsopen')
@include('networks._networkdetails')
@include('networks._networkdetailsclose')

<div id="map-network-{{{$network->getBssid()}}}" class="map-network"></div>

@stop

@section('scripts')

<script>
// create a map in the "map" div, set the view to a given place and zoom
var map = L.map('map-network-{{{$network->getBssid()}}}').setView([58.5436, 50.0429], 15);

// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


$(document).ready(function(){ 
    
    var points = new L.featureGroup();

    @foreach($network->locations as $location)
    	@if ($location->id === $network->loudest_location()->id)
		    var myIcon = L.AwesomeMarkers.icon({
													icon: 'map-marker', 
													color: 'blue',
													text: {{{ $location->level }}}
												})
		@else
		    var myIcon = L.AwesomeMarkers.icon({
													icon: 'map-marker', 
													color: 'cadetblue',
													text: {{{ $location->level }}}
												})
		@endif
		var popup = '@include("partials._locationpopup")';
		marker = new L.Marker([{{{ $location->lat }}}, {{{ $location->lon }}}], {icon: myIcon}).bindPopup(popup);
		points.addLayer(marker);
    @endforeach

    points.addTo(map);
    map.fitBounds(points.getBounds());
});

</script>

@stop
