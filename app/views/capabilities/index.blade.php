@extends('layouts.scaffold')

@section('main')

<h1>All Capabilities</h1>

<p>{{ link_to_route('capabilities.create', 'Add new capability') }}</p>

@if ($capabilities->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($capabilities as $capability)
				<tr>
					<td>{{{ $capability->name }}}</td>
                    <td>{{ link_to_route('capabilities.edit', 'Edit', array($capability->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('capabilities.destroy', $capability->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no capabilities
@endif

@stop
