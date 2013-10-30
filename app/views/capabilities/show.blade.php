@extends('layouts.scaffold')

@section('main')

<h1>Show Capability</h1>

<p>{{ link_to_route('capabilities.index', 'Return to all capabilities') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $capability->name }}}</td>
                    <td>{{ link_to_route('capabilities.edit', 'Edit', array($capability->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('capabilities.destroy', $capability->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
