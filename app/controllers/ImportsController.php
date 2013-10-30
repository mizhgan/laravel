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
		$hash = $this->import->getHash();

		if ($this->import->Needed()) { 
			return View::make('imports.create', compact('hash'));
		}
		
		return Redirect::route('imports.index')
			->with('message', 'Исходная база похоже не изменилась с последнего импорта.');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$result_networks = array();
		$result_locations = array();
		$count_new_networks = 0;
		$count_new_locations = 0;
		$count_new_capabilities = 0;
		$count_new_types = 0;

		$input = Input::all();
		$validation = Validator::make($input, Import::$rules);

		if ($validation->passes())
		{

			if ($db = $this->import->getDb()) {
				$r_networks =  $db->query('select * from network');
				
			    while($result_network = $r_networks->fetchArray()) {
			    		if ($new_network = Network::where('bssid', '=', $result_network['bssid'])->first()) { //если точка уже есть в базе
		                    $result_networks[] = $new_network;
		                } else { //есле нет, создаем точку и сохраняем
				    		$new_network = new Network;
							$new_network->bssid = $result_network['bssid'];
							$new_network->ssid = $result_network['ssid'];
							$new_network->frequency = $result_network['frequency'];
							$new_network->save();
							$result_networks[] = $new_network;
							$count_new_networks++;
						}
						
			    	
						$network_capabilities = array();
						$capabilities = explode('][', $result_network['capabilities']) ? explode('][', $result_network['capabilities']) : $result_network['capabilities'] ;
						if (is_array($capabilities)) { //если вайфай
							foreach ($capabilities as $key => $capability) {
								$capability = trim($capability, '[] ');
								if ($new_capability = Capability::where('name', '=', $capability)->first()) { //если возможность уже есть в базе
				                    $network_capabilities[] = $new_capability->id;
				                } else { // если нету в базе, создаем, сохраняем
				                	$new_capability = new Capability;
				                	$new_capability->name = $capability;
				                	$new_capability->save();
				                	$network_capabilities[] = $new_capability->id;
				                	$count_new_capabilities++;
				                }
							}
						} else { //есле джиэсэм
							
						}

						$network_types = array();
						$type = $result_network['type'];
						if ($new_type = Type::where('name', '=', $type)->first()) { //если тип уже есть в базе
		                    $network_types[] = $new_type->id;
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
							
							if ($new_location = Location::where('bssid', '=', $new_network->bssid)->where('level', '=', $result_location['level'])->where('time', '=', date('Y-m-d H:i:s', floor($result_location['time']/1000)))->first()) { //если местоположение уже есть в базе
			                    $network_locations[] = $new_location->id;
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
			                $result_locations[] = $new_location;

		            	}

		                $new_network->capabilities()->sync($network_capabilities);
		                $new_network->types()->sync($network_types);
		                $new_network->locations()->sync($network_locations);
			    }
			} else {
			    die();
			}

			$this->import->create($input);
			Cache::forever('lastimport', Input::get('hash'));
			$db->close();

			return Redirect::route('imports.index')
				->with('message', "Импорт успешен. Всего обработано $count_new_networks новых точек, $count_new_locations новых местоположений, $count_new_capabilities новых возможностей и $count_new_types новых типов.")
				->with('state', 'success')
				->with('sticky', 'true');
		}

		return Redirect::route('imports.create')
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
