@extends('layouts.scaffold')

@section('main')

<h1>Edit Network</h1>
{{ Form::model($network, array('method' => 'PATCH', 'route' => array('networks.update', $network->id))) }}
	<ul>
        <li>
            {{ Form::label('bssid', 'Bssid:') }}
            {{ Form::text('bssid') }}
        </li>

        <li>
            {{ Form::label('ssid', 'Ssid:') }}
            {{ Form::text('ssid') }}
        </li>

        <li>
            {{ Form::label('frequency', 'Frequency:') }}
            {{ Form::input('number', 'frequency') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('networks.show', 'Cancel', $network->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
