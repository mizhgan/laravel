@extends('layouts.scaffold')

@section('main')

<h1>Show Type</h1>

<p>{{ link_to_route('types.index', 'Return to all types') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $type->name }}}</td>
                    <td>{{ link_to_route('types.edit', 'Edit', array($type->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('types.destroy', $type->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
