<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('users')->truncate();

		$users = array(
            array(
                'username' => 'mizhgan',
                'email'	=> 'i@mizhgan.ru',
                'password' => Hash::make('mm11hhaa'),
                'updated_at' => DB::raw('NOW()'),
                'created_at' => DB::raw('NOW()'),
                )
        );

		// Uncomment the below to run the seeder
		DB::table('users')->insert($users);
	}

}
