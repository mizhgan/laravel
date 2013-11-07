@extends('layouts.scaffold')

@section('title')
Карта покрытия WiFi и GSM Кирово-Чепецка - Информация о точке {{{ $network->bssid }}} {{{ $network->ssid }}}
@stop

@section('description')
Карта покрытия WiFi и GSM Кирово-Чепецка. Открытые и закрытые точки доступа WiFi В Кирово-Чепецке. Точки WiFi с шифрованием WPA, WPA2, WEP, WPS. Точка с идентификатором {{{ $network->ssid }}}
@stop

@section('main')

<h1>Информация о точке {{{ $network->bssid }}} {{{ $network->ssid }}}</h1>

<p>{{ link_to_route('networks.index', 'Вернуться ко всем точкам') }}</p>
<p class="text-right"><a href="#disqus_thread">Комментарии</a></p>

@include('networks._networkdetailsopen')
@include('networks._networkdetails')
@include('networks._networkdetailsclose')

<div id="map-network-{{{$network->getBssid()}}}" class="map-network"></div>

<div id="disqus_thread"></div>

@stop

@section('scripts')

<script>

$('th span').popover();

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

<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = 'chepetsk-wifi-map'; // required: replace example with your forum shortname

    var disqus_identifier = '{{{$network->id}}}';

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();

	/* * * DON'T EDIT BELOW THIS LINE * * */
	(function () {
	    var s = document.createElement('script'); s.async = true;
	    s.type = 'text/javascript';
	    s.src = '//' + disqus_shortname + '.disqus.com/count.js';
	    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
	}());
</script>

<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

@stop
