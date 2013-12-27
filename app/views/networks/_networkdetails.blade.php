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
				<div class="panel-group" id="accordion-{{{$network->getBssid()}}}">
				  <div class="panel panel-default">
				    <div class="panel-heading">
				      <h4 class="panel-title">
				        <a data-toggle="collapse" data-parent="#accordion-{{{$network->getBssid()}}}" href="#chosenlocations-{{{$network->getBssid()}}}">
				          Характерные замеры
				        </a>
				      </h4>
				    </div>
				    <div id="chosenlocations-{{{$network->getBssid()}}}" class="panel-collapse collapse in">
				      <div class="panel-body">
				        <div>
							Лучший сигнал:
						</div>
						<div class="label label-primary">
							<span class="glyphicon glyphicon-map-marker"></span>
							{{{ $network->loudest_location()->lat }}} : {{{ $network->loudest_location()->lon }}} @ {{{ $network->loudest_location()->time }}} @ {{{ $network->loudest_location()->level }}}db
						</div>
						<div>
							Последний замер:
						</div>
						<div class="label label-default">
							<span class="glyphicon glyphicon-map-marker"></span>
							{{{ $network->latest_location()->lat }}} : {{{ $network->latest_location()->lon }}} @ {{{ $network->latest_location()->time }}} @ {{{ $network->latest_location()->level }}}db
						</div>
				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading">
				      <h4 class="panel-title">
				        <a data-toggle="collapse" data-parent="#accordion-{{{$network->getBssid()}}}" href="#alllocations--{{{$network->getBssid()}}}">
				          Все замеры ({{{count($network->locations)}}})
				        </a>
				      </h4>
				    </div>
				    <div id="alllocations--{{{$network->getBssid()}}}" class="panel-collapse collapse">
				      <div class="panel-body">
				        <div>
						  	@foreach($network->locations as $location)
						  		@if ($location->id === $network->loudest_location()->id)
								    <div class="label label-primary"><span class="glyphicon glyphicon-map-marker"></span>
								@else
								    <div class="label label-default"><span class="glyphicon glyphicon-map-marker"></span>
								@endif
								{{{ $location->lat }}} : {{{ $location->lon }}} @ {{{ $location->time }}} @ {{{ $location->level }}}db</div>
							@endforeach
						</div>
				      </div>
				    </div>
				  </div>
				</div>
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