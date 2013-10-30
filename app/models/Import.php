<?php

class Import extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'hash' => 'required'
	);

	public static $db_path = "c:\Winginx\home\wifimap.dev\public_html\data\wiglewifi.sqlite";

	public function getHash() {
		return hash_file('md5', $this::$db_path);
	}

	public function Needed()
	{
		return (!($this->first()) || ($this->getHash() !== $this->orderBy('created_at', 'desc')->first()->hash));
	}

	public function getDb() {
		return new SQLite3($this::$db_path);
	}
}
