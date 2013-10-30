@extends('layouts.scaffold')

@section('main')

<h1>
	@if (isset($title))
		{{{$title}}}
	@else
		Все ресположения
	@endif
</h1>

<p>{{ link_to_route('locations.create', 'Add new location') }}</p>

@if ($locations->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Bssid</th>
				<th>Level</th>
				<th>Lat</th>
				<th>Lon</th>
				<th>Altitude</th>
				<th>Accuracy</th>
				<th>Time</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($locations as $location)
				<tr>
					<td>{{{ $location->bssid }}}</td>
					<td>{{{ $location->level }}}</td>
					<td>{{{ $location->lat }}}</td>
					<td>{{{ $location->lon }}}</td>
					<td>{{{ $location->altitude }}}</td>
					<td>{{{ $location->accuracy }}}</td>
					<td>{{{ $location->time }}}</td>
                    <td>{{ link_to_route('locations.edit', 'Edit', array($location->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('locations.destroy', $location->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
			{{$locations->links()}}
		</tbody>
	</table>
	{{$locations->links()}}
@else
	There are no locations
@endif

@stop
