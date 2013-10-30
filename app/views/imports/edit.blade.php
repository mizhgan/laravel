@extends('layouts.scaffold')

@section('main')

<h1>Edit Import</h1>
{{ Form::model($import, array('method' => 'PATCH', 'route' => array('imports.update', $import->id))) }}
	<ul>
        <li>
            {{ Form::label('hash', 'Hash:') }}
            {{ Form::text('hash') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('imports.show', 'Cancel', $import->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
