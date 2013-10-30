@extends('layouts.scaffold')

@section('main')

<h1>All Imports</h1>

<p>{{ link_to_route('imports.create', 'Add new import') }}</p>

@if ($imports->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Hash</th>
				<th>Created at</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($imports as $import)
				<tr>
					<td>{{{ $import->hash }}}</td>
					<td>{{{ $import->created_at }}}</td>
                    <td>{{ link_to_route('imports.edit', 'Edit', array($import->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('imports.destroy', $import->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no imports
@endif

@stop
