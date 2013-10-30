<?php

class RolesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('roles')->truncate();

		$roles = array(
            array(
                'role' => 'admin', 
                'updated_at' => DB::raw('NOW()'),
                'created_at' => DB::raw('NOW()')
                ),
            array(
                'role' => 'manager', 
                'updated_at' => DB::raw('NOW()'),
                'created_at' => DB::raw('NOW()')
                ),
            array(
                'role' => 'moderator', 
                'updated_at' => DB::raw('NOW()'),
                'created_at' => DB::raw('NOW()')
                )

        );

		// Uncomment the below to run the seeder
		DB::table('roles')->insert($roles);
	}

}
