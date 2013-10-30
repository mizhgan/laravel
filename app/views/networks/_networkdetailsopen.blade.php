<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Bssid</th>
				<th>Ssid</th>
				<th>Frequency</th>
				<th>Тип</th>
				<th>Возможности</th>
				<th>Расположения</th>
				@if(!Auth::guest())
					@if(Auth::user()->isAdmin())
						<th>Действия</th>
					@endif
				@endif
		</tr>
	</thead>

	<tbody>