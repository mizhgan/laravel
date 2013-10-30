@extends('layouts.scaffold')

@section('main')

<h1>Show Import</h1>

<p>{{ link_to_route('imports.index', 'Return to all imports') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Hash</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $import->hash }}}</td>
                    <td>{{ link_to_route('imports.edit', 'Edit', array($import->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('imports.destroy', $import->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
