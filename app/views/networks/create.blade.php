@extends('layouts.scaffold')

@section('main')

<h1>Create Network</h1>

{{ Form::open(array('route' => 'networks.store')) }}
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
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


