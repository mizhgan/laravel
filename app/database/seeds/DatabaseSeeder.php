<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('NetworksTableSeeder');
		$this->call('CapabilitiesTableSeeder');
		$this->call('TypesTableSeeder');
		$this->call('LocationsTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('RolesTableSeeder');
		$this->call('ImportsTableSeeder');
		$this->call('RoleUserTableSeeder');
	}

}