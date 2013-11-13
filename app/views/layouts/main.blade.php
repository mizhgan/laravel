<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title> @yield('title') </title>
		<meta name="description" content="@yield('description')">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-theme.min.css') }}">
		<link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />
		 <!--[if lte IE 8]>
		     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
		 <![endif]-->
		<link rel="stylesheet" href="http://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css" />
		<link rel="stylesheet" href="http://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css" />
		<!--[if lte IE 8]><link rel="stylesheet" href="http://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.ie.css" /><![endif]-->

		<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.awesome-markers.css') }}">

		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.css" />

		@yield('styles')
	</head>

	<body>

		<div style="position: fixed; top: 150px; left: 5px; z-index: 1000;" class="pluso" data-background="transparent" data-options="medium,square,line,vertical,counter,theme=06" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,print"></div>

		<div id="wrap">

	      <!-- Fixed navbar -->
	      <div class="navbar navbar-default navbar-fixed-top">
	        <div class="container">
	          <div class="navbar-header">
	            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	              <span class="icon-bar"></span>
	              <span class="icon-bar"></span>
	              <span class="icon-bar"></span>
	            </button>
	            <a class="navbar-brand" href="{{ route('home') }}">Wi-Fi и GSM в Кирово-Чепецке</a>
	          </div>
	          <div class="collapse navbar-collapse">
	            <ul class="nav navbar-nav">
	              <li>
	              	<!-- Button trigger modal -->
					<button title="Я ничего не понимаю!" class="btn btn-default btn-lg" data-toggle="modal" data-target="#HelpModal">
						<span class="glyphicon glyphicon-question-sign"></span> 
					</button>
				  </li>
	              <li>{{ link_to_route('networks.index', 'Точки') }}</li>
	              <li class="dropdown">
		                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Показывать... <b class="caret"></b></a>
		                <ul class="dropdown-menu">
		                	<li>
	                			<label style="font-weight: normal;">
  									&nbsp;&nbsp;&nbsp;{{Form::checkbox('showselect', 'G', true)}}
  									GSM
  								</label>
  							</li>
				            <li>
				            	<label style="font-weight: normal;">
				            		&nbsp;&nbsp;&nbsp;{{Form::checkbox('showselect', 'W', true)}}
				            		WiFi
				            	</label>
				            </li>
				            <li>
				            	<label style="font-weight: normal;">
				            		&nbsp;&nbsp;&nbsp;{{Form::checkbox('showselect', 'open', true)}}
				            		Открытые
				            	</label>
				            </li>
				            <li>
				            	<label style="font-weight: normal;">
				            		&nbsp;&nbsp;&nbsp;{{Form::checkbox('showselect', 'close', true)}}
				            		Закрытые
				            	</label>
				            </li>
						</ul>
		          </li>
		          
	              @if(!Auth::guest())
	              	@if(Auth::user()->isAdmin())
		              <li class="dropdown">
		                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Администрирование <b class="caret"></b></a>
		                <ul class="dropdown-menu">
			              <li>{{ link_to_route('capabilities.index', 'Возможности') }}</li>
			              <li>{{ link_to_route('types.index', 'Типы') }}</li>
			              <li>{{ link_to_route('locations.index', 'Местоположения') }}</li>
						  <li>{{ link_to_route('imports.index', 'Импортирование') }}</li>
		                  <li class="divider"></li>
		                  <li class="dropdown-header">Пользователи</li>
		                  <li>{{ link_to_route('users.index', 'Пользователи') }}</li>
		                  <li>{{ link_to_route('roles.index', 'Роли') }}</li>
		                  <li class="divider"></li>
		                  <li class="dropdown-header">Дебаг</li>
		                  <li id="debug"></li>
		                </ul>
		              </li>
		            @endif
		              <li class="dropdown">
		                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> {{Auth::user()->username}}<b class="caret"></b></a>
		                <ul class="dropdown-menu">
		                  <li>{{ link_to_route('login.logout', 'Logout') }}</li>
		                </ul>
		              </li>
	              @else
	              	  <li class="pull-right">{{ link_to_route('login.index', 'Login') }}</li>
	              @endif

	            </ul>
		        <form class="navbar-form navbar-right" role="search">
				    <div class="form-group">
					    <input id="main-search" type="text" class="form-control" placeholder="Поиск...">
				    </div>
				</form>
	            
	          </div><!--/.nav-collapse -->
	        </div>
	      </div>
	      <!-- Begin page content -->

		    @yield('main')

	    </div>

	    <div id="footer">
	      <div class="container">
	      	<div class="row">
			  <div class="col-md-4"><p class="text-muted credit">Карта точек Wi-Fi Кирово-Чепецка</p></div>
			  @include('partials.stats')
	        </div>
	      </div>
	    </div>

		<script type="text/javascript" src="//code.jquery.com/jquery.min.js"></script>

        <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>

		<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>        

		<script src="http://twitter.github.io/typeahead.js/releases/latest/typeahead.js"></script>

        <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>

        <script src="http://fgnass.github.io/spin.js/dist/spin.min.js"></script>

        <script src="http://makinacorpus.github.io/Leaflet.Spin/leaflet.spin.js"></script>

        <script src="http://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>

        <script type="text/javascript" src="http://mlevans.github.io/leaflet-hash/javascripts/leaflet-hash.js"></script>

        <script type="text/javascript" src="{{ asset('js/leaflet.awesome-markers.js') }}"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.js"></script>    

        <script type="text/javascript">
        	$.jGrowl.defaults.closerTemplate = '<div>Скрыть все</div>';
	      	$.jGrowl.defaults.position = 'top-right';
	      	$.jGrowl.defaults.themeState = 'none';
	      	@if (Session::has('state'))
	      		$.jGrowl.defaults.theme = "alert alert-{{ Session::get('state') }}";
	      	@else
	      		$.jGrowl.defaults.theme = "alert alert-danger";
	      	@endif
	      	@if (Session::has('sticky'))
	      		$.jGrowl.defaults.sticky = {{ Session::get('sticky') }};
	      	@else
	      		$.jGrowl.defaults.sticky = false;
	      	@endif
	      	$.jGrowl.defaults.header = '<span class="glyphicon glyphicon-warning-sign"></span> Внимание!';
	      	$.jGrowl.defaults.life = 5000;
        </script>

        @yield('scripts')

        @if (Session::has('message'))
	      	<script>
				$.jGrowl("<p>{{ Session::get('message') }}</p>");
	      	</script>
	    @endif
	    <script type="text/javascript">(function() {
		  if (window.pluso)if (typeof window.pluso.start == "function") return;
		  if (window.ifpluso==undefined) { window.ifpluso = 1;
		    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
		    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
		    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
		    var h=d[g]('body')[0];
		    h.appendChild(s);
		  }})();
		</script>
		
	</body>

</html>