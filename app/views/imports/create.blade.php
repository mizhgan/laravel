@extends('layouts.scaffold')

@section('main')

<h1>Create Import</h1>

{{ Form::open(array('route' => 'imports.store')) }}
	<ul>
        <li>
            {{ Form::label('hash', 'Hash:') }}
            {{ Form::text('hash', $hash) }}
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


