<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>BSSID
				<span class="glyphicon glyphicon-question-sign" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="BSSID - или basic service set identification, основной идентификатор сети, в случае точки WiFi обычно является также MAC-адресом." data-original-title="BSSID"></span>
			</th>
			<th>SSID
				<span class="glyphicon glyphicon-question-sign" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="SSID - или service set identification, идентификатор сети, в случае WiFi может быть задан владельцем" data-original-title="SSID"></span>
			</th>
			<th>Frequency
				<span class="glyphicon glyphicon-question-sign" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="Частота канала, на котором работает точка WiFi" data-original-title="Frequency"></span>
			</th>
			<th>Тип
				<span class="glyphicon glyphicon-question-sign" data-html="true" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="Тип точки. <span class='glyphicon glyphicon-signal'></span> - WiFi, <span class='glyphicon glyphicon-phone'></span> - базовая станция GSM. Кликните на иконку для вывода всех точек данного типа." data-original-title="Тип"></span>
			</th>
			<th>Возможности
				<span class="glyphicon glyphicon-question-sign" data-html="true" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="Для точек WiFi - виды аутентификации и шифрования, поддерживаемые данной точкой. Для точек GSM - стандарт сотовой сети. Кликните для вывода всех точек с данной возможностью." data-original-title="Возможности"></span>
			</th>
			<th>Местоположения
				<span class="glyphicon glyphicon-question-sign" data-html="true" data-placement="auto top" data-trigger="hover" data-toggle="popover" title="" data-content="Все сохраненные местоположения для данной точки. На странице точки также показаны на индивидуальной мини-карте. Цветом выделено местоположение с наилучшим уровнем сигнала." data-original-title="Местоположения"></span>
			</th>
			@if(!Auth::guest())
				@if(Auth::user()->isAdmin())
					<th>Действия</th>
				@endif
			@endif
		</tr>
	</thead>

	<tbody>