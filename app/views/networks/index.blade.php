@extends('layouts.scaffold')

@section('title')
Карта покрытия WiFi и GSM Кирово-Чепецка - {{{$title}}}
@stop

@section('description')
Карта покрытия WiFi и GSM Кирово-Чепецка. Открытые и закрытые точки доступа WiFi В Кирово-Чепецке. Точки WiFi с шифрованием WPA, WPA2, WEP, WPS. {{{$title}}}
@stop

@section('main')

<h1>{{{$title}}}</h1>

@if(!Auth::guest())
	@if(Auth::user()->isAdmin())
		<p>{{ link_to_route('networks.create', 'Создать точку') }}</p>
	@endif
@endif

@if ($networks->count())
	{{$networks->links()}}
	@include('networks._networkdetailsopen')
			@foreach ($networks as $network)
				@include('networks._networkdetails')
			@endforeach
	@include('networks._networkdetailsclose')
	{{$networks->links()}}
@else
	There are no networks
@endif


@stop

@section('scripts')

<script type="text/javascript">
	$('th span').popover();
</script>

@stop
