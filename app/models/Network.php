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
}
