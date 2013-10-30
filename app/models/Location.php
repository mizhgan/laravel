<?php

class Location extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'bssid' => 'required',
		'level' => 'required',
		'lat' => 'required',
		'lon' => 'required',
		'altitude' => 'required',
		'accuracy' => 'required',
		'time' => 'required'
	);

	public function networks() {
		return $this->belongsToMany('Network');
	}
}
