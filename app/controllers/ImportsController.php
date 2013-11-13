<?php

class ImportsController extends BaseController {

	/**
	 * Import Repository
	 *
	 * @var Import
	 */
	protected $import;

	public function __construct(Import $import)
	{
		$this->import = $import;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$imports = $this->import->all();
		if ($this->import->Needed()) {
			$message = "Похоже есть обновления для импорта, нажми добавить импорт.";
			Session::flash('message', $message);
		}

		return View::make('imports.index', compact('imports'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ($hash = $this->import->getHash()) { //есть ли локальный файл с бд
			$total = $this->countAllNetworksSQLite(Import::$db_path);
		} else {
			$total = false;
		}

		return View::make('imports.create', compact('hash', 'total'));
				
	}

	/**
	 * Count total rows in networks table in wigle sqlite db
	 *
	 * @return int total
	 */
	public function countAllNetworksSQLite($filename) {
		if ($db = new SQLite3($filename)) {
			$r_networks =  $db->query('select COUNT(*) as count from network');
			$row = $r_networks->fetchArray();
			return $row['count'];
		} else {
			return false;
		}
	}

	/**
	 * Perform uploading file and moving it into upload dir
	 *
	 * @return Response
	 */
	public function uploadDbFile()
    {
        // $rules = array('file' => 'mimes:jpeg,png');

        // $validator = Validator::make(Input::all(), $rules);

        // if ($validator->fails()) {
        //     return Response::json(array('message' => $validator->messages()->first('file')));
        // }

    	if(!Auth::guest()) {
    		$username = Auth::user()->username;
    	} else {
    		$username = 'guest';
    	}

        $dir = Import::$upload_path.'/'.$username.date('/Y/m/d/');
        
        do {
            $filename = str_random(30).'.sqlite';
        } while (File::exists($dir.$filename));

        Input::file('file')->move($dir, $filename);

        if (Import::where('hash', '=', hash_file('md5', $dir.$filename))->first()) {
        	$hashexist = true;
        } else {
        	$hashexist = false;
        }

        return Response::json(array('filelink' => $dir.$filename, 'total' => $this->countAllNetworksSQLite($dir.$filename), 'hash' => hash_file('md5', $dir.$filename), 'hashexist' => $hashexist));
    }

	/**
	 * Store a given range of imported resource.
	 *
	 * @return string Stats
	 */
	public function performStore()
	{	
		$filename = Input::get('filename');
		$offset = Input::get('offset');
		$count = Input::get('count');
		$type = Input::get('type');

		$count_new_networks = 0;
		$count_new_locations = 0;
		$count_new_capabilities = 0;
		$count_new_types = 0;

		$count_exist_networks = 0;
		$count_exist_locations = 0;
		$count_exist_capabilities = 0;
		$count_exist_types = 0;

		$count_updated_networks = 0;

		if ($db = new SQLite3($filename)) {
			$r_networks =  $db->query('select * from network limit '.$count.' offset '.$offset);
			
		    while($result_network = $r_networks->fetchArray()) {
		    		if ($new_network = Network::where('bssid', '=', $result_network['bssid'])->first()) { //если точка уже есть в базе
		    			if ($new_network->ssid != $result_network['ssid'] || $new_network->frequency != $result_network['frequency']) { //если изменились ssid или freq - обновляем данные о точке
		    				$new_network->ssid = $result_network['ssid'];
							$new_network->frequency = $result_network['frequency'];
							$new_network->save();
							$count_updated_networks++;
		    			}
		    			$count_exist_networks++;
	                } else { //есле нет, создаем точку и сохраняем
			    		$new_network = new Network;
						$new_network->bssid = $result_network['bssid'];
						$new_network->ssid = $result_network['ssid'];
						$new_network->frequency = $result_network['frequency'];
						$new_network->save();
						$count_new_networks++;
					}
					
		    	
					$network_capabilities = array();
					$capabilities = explode('][', $result_network['capabilities']);
					
					foreach ($capabilities as $key => $capability) {
						$capability = trim($capability, '[] ');
						if ($new_capability = Capability::where('name', '=', $capability)->first()) { //если возможность уже есть в базе
		                    $network_capabilities[] = $new_capability->id;
		                    $count_exist_capabilities++;
		                } else { // если нету в базе, создаем, сохраняем
		                	$new_capability = new Capability;
		                	$new_capability->name = $capability;
		                	$new_capability->save();
		                	$network_capabilities[] = $new_capability->id;
		                	$count_new_capabilities++;
		                }
					}

					$network_types = array();
					$type = $result_network['type'];

					if ($new_type = Type::where('name', '=', $type)->first()) { //если тип уже есть в базе
	                    $network_types[] = $new_type->id;
	                    $count_exist_types++;
	                } else { // если нету в базе, создаем, сохраняем
	                	$new_type = new Type;
	                	$new_type->name = $type;
	                	$new_type->save();
	                	$network_types[] = $new_type->id;
	                	$count_new_types++;
	                }

	                //начинаем искать местоположения для точек
	                $r_locations = $db->query('select * from location where bssid = \''.$new_network->bssid.'\'');
	                $network_locations = array();

				    while($result_location = $r_locations->fetchArray()) {
						if ($result_location['time']) {
							if ($new_location = Location::where('bssid', '=', $new_network->bssid)->where('level', '=', $result_location['level'])->where('time', '=', date('Y-m-d H:i:s', floor($result_location['time']/1000)))->first()) { //если местоположение уже есть в базе
			                    $network_locations[] = $new_location->id;
			                    $count_exist_locations++;
			                } else { // если нету в базе, создаем, сохраняем
			                	$new_location = new Location;
			                	$new_location->bssid = $result_location['bssid'];
			                	$new_location->level = $result_location['level'];
			                	$new_location->lat = $result_location['lat'];
			                	$new_location->lon = $result_location['lon'];
			                	$new_location->altitude = $result_location['altitude'];
			                	$new_location->accuracy = $result_location['accuracy'];
			                	$new_location->time = date('Y-m-d H:i:s', floor($result_location['time']/1000));
			                	$new_location->save();
			                	$network_locations[] = $new_location->id;
			                	$count_new_locations++;
			                }
			            }

	            	}

	                $new_network->capabilities()->sync($network_capabilities);
	                $new_network->types()->sync($network_types);
	                $new_network->locations()->sync($network_locations);
		    }

		    return json_encode(array('error' => false, 'new_networks' => $count_new_networks, 'exist_networks' => $count_exist_networks, 'new_locations' => $count_new_locations, 'exist_locations' => $count_exist_locations, 'new_types' => $count_new_types, 'exist_types' => $count_exist_types, 'new_capabilities' => $count_new_capabilities, 'exist_capabilities' => $count_exist_capabilities, 'updated_networks' => $count_updated_networks));
		} else {
		    return json_encode(array('error' => true, 'err_message' => 'Error with SQLite3 database'));
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Import::$rules);

		if ($validation->passes())
		{
			$this->import->create($input);
			Cache::forever('lastimport', Input::get('hash'));
			Cache::forget('geojsonpoints');

			// return Redirect::route('imports.index')
			// 	->with('message', "Импорт успешен. Всего обработано $count_new_networks новых точек, $count_new_locations новых местоположений, $count_new_capabilities новых возможностей и $count_new_types новых типов.")
			// 	->with('state', 'success')
			// 	->with('sticky', 'true');
			return json_encode(array('success' => true));
		}

		// return Redirect::route('imports.create')
		// 	->withInput()
		// 	->withErrors($validation)
		// 	->with('message', 'There were validation errors.');
		return json_encode(array('success' => false));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$import = $this->import->findOrFail($id);

		return View::make('imports.show', compact('import'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$import = $this->import->find($id);

		if (is_null($import))
		{
			return Redirect::route('imports.index');
		}

		return View::make('imports.edit', compact('import'));
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
		$validation = Validator::make($input, Import::$rules);

		if ($validation->passes())
		{
			$import = $this->import->find($id);
			$import->update($input);

			return Redirect::route('imports.show', $id);
		}

		return Redirect::route('imports.edit', $id)
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
		$this->import->find($id)->delete();

		return Redirect::route('imports.index');
	}

}
