<?php

class Type extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required'
	);

	public function networks() {
		return $this->belongsToMany('Network');
	}
}
