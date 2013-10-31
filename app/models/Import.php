<?php

class Import extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'hash' => 'required'
	);

	public static $db_path = "../../data/wigle/mizhgan/wiglewifi.sqlite";

	public static $upload_path = "../../data/upload";

	public function getHash() {
		return hash_file('md5', $this::$db_path);
	}

	public function Needed()
	{
		//return (!($this->first()) || ($this->getHash() !== $this->orderBy('created_at', 'desc')->first()->hash));
		return (!($this->first()) || !($this->where('hash', '=', $this->getHash())->first()));
	}

	public function getDb() {
		return new SQLite3($this::$db_path);
	}
}
