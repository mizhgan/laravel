@extends('layouts.scaffold')

@section('title')
Карта покрытия WiFi и GSM Кирово-Чепецка - {{{$title}}}
@stop

@section('description')
Карта покрытия WiFi и GSM Кирово-Чепецка. Открытые и закрытые точки доступа WiFi В Кирово-Чепецке. Точки WiFi с шифрованием WPA, WPA2, WEP, WPS. {{{$title}}}
@stop

@section('main')

<div class="panel panel-default">
	<div class="panel-heading">
		<h2>{{{$title}}}</h2>
	</div>
	<div class="panel-body">

		@if(!Auth::guest())
			@if(Auth::user()->isAdmin())
				<p>{{ link_to_route('networks.create', 'Создать точку') }}</p>
			@endif
		@endif

@if ($networks->count())
	{{$networks->links()}}
	</div>

	@include('networks._networkdetailsopen')
			@foreach ($networks as $network)
				@include('networks._networkdetails')
			@endforeach
	@include('networks._networkdetailsclose')
	<div class="panel-body">
		{{$networks->links()}}
@else
	There are no networks
@endif
	</div>
</div>


@stop

@section('scripts')

<script type="text/javascript">
	$('th span').popover();
</script>

@stop
