@extends('layouts.scaffold')

@section('main')

<h1>Edit Location</h1>
{{ Form::model($location, array('method' => 'PATCH', 'route' => array('locations.update', $location->id))) }}
	<ul>
        <li>
            {{ Form::label('bssid', 'Bssid:') }}
            {{ Form::text('bssid') }}
        </li>

        <li>
            {{ Form::label('level', 'Level:') }}
            {{ Form::input('number', 'level') }}
        </li>

        <li>
            {{ Form::label('lat', 'Lat:') }}
            {{ Form::text('lat') }}
        </li>

        <li>
            {{ Form::label('lon', 'Lon:') }}
            {{ Form::text('lon') }}
        </li>

        <li>
            {{ Form::label('altitude', 'Altitude:') }}
            {{ Form::text('altitude') }}
        </li>

        <li>
            {{ Form::label('accuracy', 'Accuracy:') }}
            {{ Form::text('accuracy') }}
        </li>

        <li>
            {{ Form::label('time', 'Time:') }}
            {{ Form::text('time') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('locations.show', 'Cancel', $location->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
