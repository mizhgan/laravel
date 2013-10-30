@extends('layouts.scaffold')

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
