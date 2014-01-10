@extends('layouts.scaffold')

@section('title')
Карта покрытия WiFi и GSM Кирово-Чепецка - Информация о точке {{{ $network->bssid }}} {{{ $network->ssid }}}
@stop

@section('description')
Карта покрытия WiFi и GSM Кирово-Чепецка. Открытые и закрытые точки доступа WiFi В Кирово-Чепецке. Точки WiFi с шифрованием WPA, WPA2, WEP, WPS. Точка с идентификатором {{{ $network->ssid }}}
@stop

@section('main')


<div class="panel panel-default">
	<div class="panel-heading">
    	<h2>Информация о точке {{{ $network->bssid }}} {{{ $network->ssid }}}</h2>
  	</div>
  	<div class="panel-body">
  		<div class="row">
		  <div class="col-md-6">
		  		<div id="macinfo">Загрузка информации об идентификаторе сети...</div>
		  </div>
		  <div class="col-md-6">
		  		<div id="nominatiminfo">Загрузка информации о координатах...</div>
		  </div>
		</div>
	</div>
  	<div class="panel-body">
		<span class="pull-left">{{ link_to_route('networks.index', 'Вернуться ко всем точкам') }}</span>
		<span class="pull-right"><a href="#disqus_thread">Комментарии</a></span>
	</div>

	@include('networks._networkdetailsopen')
	@include('networks._networkdetails')
	@include('networks._networkdetailsclose')
</div>

<div class="panel panel-default">
  	<div class="panel-body">
		<div class="col-md-12 map-network" id="map-network-{{{$network->getBssid()}}}" ></div>
	</div>
</div>

<div class="panel panel-default">
  	<div class="panel-body">
		<div class="col-md-12" id="disqus_thread"></div>
	</div>
</div>

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

	var macinfo = $("#macinfo");
	$.getJSON('/bssid/{{{ $network->bssid }}}')
		.done(function (json) {
			if (json) {
        		macinfo.text("Информация об идентификаторе сети: " + json[0].company);
        	} else {
        		macinfo.text("Информация об идентификаторе сети: Нет данных");
        	}
    	})
    	.fail(function( jqxhr, textStatus, error ) {
    		macinfo.text("Информация об идентификаторе сети: Нет данных");
		    var err = textStatus + ", " + error;
		    console.log( "Request Failed: " + err );
		});

    var nominatiminfo = $("#nominatiminfo");
	$.getJSON('/nominatim/{{{ $network->loudest_location()->lat }}},{{{ $network->loudest_location()->lon }}}')
		.done(function (json) {
			if (json.length) {
        		nominatiminfo.text("Информация о координатах: " + json[0].display_name);
        	} else {
        		nominatiminfo.text("Информация о координатах: Нет данных");
        	}
    	})
    	.fail(function( jqxhr, textStatus, error ) {
    		nominatiminfo.text("Информация о координатах: Нет данных");
		    var err = textStatus + ", " + error;
		    console.log( "Request Failed: " + err );
		});

	//var spinner = new Spinner(opts).spin(macinfo); 
	//macinfo.prepend(spinner.el);

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
