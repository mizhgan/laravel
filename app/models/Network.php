<?php

class Network extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'bssid' => 'required'
	);

	public function locations() {
		return $this->belongsToMany('Location');
	}

	public function capabilities() {
		return $this->belongsToMany('Capability');
	}

	public function types() {
		return $this->belongsToMany('Type');
	}

	public function loudest_location() {
		return $this->locations()->orderBy('level', 'desc')->first();
	}

	public function latest_location() {
		return $this->locations()->orderBy('time', 'desc')->first();
	}

	public function getBssid() {
		return str_replace(':', '', $this->bssid);
	}

	public function getCapabilitiesToStr($separator = " ") {
		$capabilities = '';
		foreach ($this->capabilities as $capability) {
			$capabilities .= $capability->name.$separator;	
		}
		return trim($capabilities,$separator);
	}

	public function isOpen() {
		$capabilities = $this->getCapabilitiesToStr();
		if (stristr($capabilities, 'WPA') || stristr($capabilities, 'WEP') || stristr($capabilities, 'WPS')) {
			//closed
			return false;
		} else {
			//opened
			return true;
		}
	}

	public function getGeojsonFeature() {
		$prop = array(
						'bssid' => $this->bssid,
						//'ssid' => $this->ssid,
						//'level' => $this->loudest_location()->level,
						//'time' => $this->latest_location()->time,
						'type' => $this->types->first()->name,
						//'capabilities' => $this->getCapabilitiesToStr(),
						'open' => $this->isOpen()
						//Just for debugging
					);
		return array(
					    'type' => 'Feature',
					    'properties' => $prop,
					    'geometry' => array(
					      'type' => 'Point',
					      'coordinates' => array( 
					        floatval($this->loudest_location()->lon),
					        floatval($this->loudest_location()->lat)
					      )
					    )
					  );
	}

}
