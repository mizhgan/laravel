		<tr>
			<td>{{ link_to_route('networks.show', $network->bssid, array($network->id)) }}</td>
			<td>{{{ $network->ssid }}}</td>
			<td>{{{ $network->frequency }}}</td>
			<td>
				@foreach($network->types as $type)
					<a class="btn btn-primary btn-lg" href="{{ route('networks.by_type', $type->name) }}">
	                @if ($type->name == 'W')
					    <span class="glyphicon glyphicon-signal"></span>
					@else
					    <span class="glyphicon glyphicon-phone"></span>
					@endif
					</a>
	            @endforeach
			</td>
			<td>
				@foreach($network->capabilities as $capability)
	                <a class="btn btn-primary btn-xs" href="{{ route('networks.by_capability', $capability->name) }}">{{{ $capability->name }}}</a>
	            @endforeach
			</td>
			<td>
			  	@foreach($network->locations as $location)
			  		@if ($location->id === $network->loudest_location()->id)
					    <span class="label label-primary"><span class="glyphicon glyphicon-map-marker"></span>
					@else
					    <span class="label label-default"><span class="glyphicon glyphicon-map-marker"></span>
					@endif
					{{{ $location->lat }}} : {{{ $location->lon }}} @ {{{ $location->time }}} @ {{{ $location->level }}}db</span>
				@endforeach
			</td>
			@if(!Auth::guest())
				@if(Auth::user()->isAdmin())
		            <td>{{ link_to_route('networks.edit', 'Edit', array($network->id), array('class' => 'btn btn-info')) }}
		                {{ Form::open(array('method' => 'DELETE', 'route' => array('networks.destroy', $network->id))) }}
		                    {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
		                {{ Form::close() }}
		            </td>
		        @endif
		    @endif
		</tr>