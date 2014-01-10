<?php

class NetworksController extends BaseController {

	/**
	 * Network Repository
	 *
	 * @var Network
	 */
	protected $network;

	public function __construct(Network $network)
	{
		$this->network = $network;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Request::ajax()) {
			/*Поиск*/
			if (Input::get('q')) {
				$networks = $this->network->where('ssid', 'like', '%'.Input::get('q', '').'%')->get();
				return $networks;
			}
			/*Поиск*/

			/*Если не поиск, то отдаем все в формате GeoJSON, используя кэш*/
			$geojson = '';
			$returngeojson = Cache::rememberForever('geojsonpoints', function()
			{
				$networks = $this->network->all();//take(100)->get(); //Дебаг! Изменить на $this->network->all();
				$features = array();
				foreach ($networks as $key => $network) {

					if ($network->locations->all()) {
						$loudest_location = $network->loudest_location();
						$latest_location = $network->latest_location();

						$type = $network->types->first()->name;
						$capabilities = '';
						foreach ($network->capabilities as $capability) {
							$capabilities .= $capability->name.' ';	
						}

						if (stristr($capabilities, 'WPA') || stristr($capabilities, 'WEP') || stristr($capabilities, 'WPS')) {
							//closed
							$open = false;
						} else {
							//opened
							$open = true;
						}

						$prop = array('bssid' => $network->bssid, 'ssid' => $network->ssid, 'level' => $loudest_location->level, 'time' => $latest_location->time, 'type' => $type, 'capabilities' => $capabilities, 'open' => $open);
						
						$features[] = new \GeoJson\Feature\Feature(new \GeoJson\Geometry\Point([floatval($loudest_location->lon), floatval($loudest_location->lat)]), $prop);				
					}

				}
				$geojson = json_encode(new \GeoJson\Feature\FeatureCollection($features));
				return $geojson;
			});
			return $returngeojson;
			/*Если не поиск*/
		}

		$networks = $this->network->paginate(20);
		$title = 'Все точки';
		return View::make('networks.index', compact('networks', 'title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('networks.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Network::$rules);

		if ($validation->passes())
		{
			$this->network->create($input);

			return Redirect::route('networks.index')
				->with('title', 'Все точки');
		}

		return Redirect::route('networks.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$network = $this->network->findOrFail($id);

		return View::make('networks.show', compact('network'));
	}

	/**
	 * Display the specified resource by bssid.
	 *
	 * @param  str  $dssid
	 * @return Response
	 */
	public function showBssid($bssid)
	{
		if (Request::ajax()) {
			$json = file_get_contents("http://www.macvendorlookup.com/api/AQzWBUT/$bssid");
			return $json;
		}

		if ($network = $this->network->where('bssid', '=', $bssid)->first()) {
			return View::make('networks.show', compact('network'));
		}

		return Redirect::route('networks.index')
			->with('message', 'Неверный BSSID '.$bssid.'');
	}

	/**
	 * Request for OSM Nominatim service.
	 *
	 * @param  str  $request
	 * @return json Response
	 */
	public function requestNominatim($request)
	{
		if (Request::ajax()) {
			$json = file_get_contents("http://nominatim.openstreetmap.org/search.php?q=".urlencode($request)."&format=json");
			return $json;
		}

		return Redirect::route('home')
			->with('message', 'Эту страницу нельзя запрашивать напрямую.');
	}

	/**
     * Display a listing of networks that belongs to type.
     *
     * @param  string  $name
     * @return Response
     */
    public function byType($name)
    {
        $type = Type::whereName($name)->firstOrFail();

        $networks = $type->networks()->paginate(20);
        $title = "Все сети с типом: " . $type->name;

        return View::make('networks.index', compact('networks', 'title'));
    }

    /**
     * Display a listing of networks that belongs to capability.
     *
     * @param  string  $name
     * @return Response
     */
    public function byCapability($name)
    {
        $capability = Capability::whereName($name)->firstOrFail();

        $networks = $capability->networks()->paginate(20);
        $title = "Все сети с возможностью: " . $capability->name;

        return View::make('networks.index', compact('networks', 'title'));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$network = $this->network->find($id);

		if (is_null($network))
		{
			return Redirect::route('networks.index')
				->with('title', 'Все точки');
		}

		return View::make('networks.edit', compact('network'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Network::$rules);

		if ($validation->passes())
		{
			$network = $this->network->find($id);
			$network->update($input);

			return Redirect::route('networks.show', $id);
		}

		return Redirect::route('networks.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->network->find($id)->delete();

		return Redirect::route('networks.index')
			->with('title', 'Все точки');
	}


	public function debug()
	{

			// $networks = $this->network->all();
			// foreach ($networks as $network) {
			// 	if ($network->locations->all()) {

			// 	} else {
			// 		return var_dump($network);
			// 	}
			// }
			// return "ok";
			$geojson = '';
			$returngeojson = Cache::remember('geojsonpoints', 60, function()
			{
				$networks = $this->network->all();//take(100)->get(); //Дебаг! Изменить на $this->network->all();
				$features = array();
				foreach ($networks as $key => $network) {
					if ($network->locations->all()) {
						$loudest_location = $network->loudest_location();
						$latest_location = $network->latest_location();

						$type = $network->types->first()->name;
						$capabilities = '';
						foreach ($network->capabilities as $capability) {
							$capabilities .= $capability->name.' ';	
						}
						$prop = array('bssid' => $network->bssid, 'ssid' => $network->ssid, 'frequency' => $network->frequency, 'level' => $loudest_location->level, 'altitude' => $loudest_location->altitude, 'accuracy' => $loudest_location->accuracy, 'time' => $latest_location->time, 'type' => $type, 'capabilities' => $capabilities);
						
						$features[] = new \GeoJson\Feature\Feature(new \GeoJson\Geometry\Point([floatval($loudest_location->lon), floatval($loudest_location->lat)]), $prop);				

					}
				}
				$geojson = json_encode(new \GeoJson\Feature\FeatureCollection($features));
				return $geojson;
			});
			return $returngeojson;

	}
}
