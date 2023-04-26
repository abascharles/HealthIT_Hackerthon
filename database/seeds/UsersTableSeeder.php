<?php

use Illuminate\Database\Seeder;
use \App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	
		DB::statement("DROP TABLE IF EXISTS `user_types`;");
		DB::statement("
			CREATE TABLE `user_types` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`user_type` varchar(100) DEFAULT NULL,
				  `created_at` timestamp NULL DEFAULT NULL,
				  `updated_at` timestamp NULL DEFAULT NULL,
				  `deleted_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

        DB::table('user_types')->insert([
		    ['id' => '1', 'user_type' => 'Lab User'],
		    ['id' => '2', 'user_type' => 'System Administrator'],
		    ['id' => '3', 'user_type' => 'Program Officers'],
		    ['id' => '4', 'user_type' => 'Data Clerk'],
		    ['id' => '5', 'user_type' => 'Facility Users'],
		    ['id' => '6', 'user_type' => 'Hub Data Uploaders'],
		    ['id' => '7', 'user_type' => 'POC Admin'],
		    ['id' => '8', 'user_type' => 'EDARP Admin'],
		    ['id' => '9', 'user_type' => 'NHRL Admin'],
		    ['id' => '10', 'user_type' => 'Partner'],
		    ['id' => '11', 'user_type' => 'Quarantine Site'],
		    ['id' => '12', 'user_type' => 'Other Lab User'],
		    ['id' => '13', 'user_type' => 'Covid Data Clerk'],
		    ['id' => '14', 'user_type' => 'Covid Lab User'],
		    ['id' => '15', 'user_type' => 'Other Data Clerk'],
		    ['id' => '16', 'user_type' => 'Covid Viewer'],
		    ['id' => '20', 'user_type' => 'DR Dashboard Viewer'],
		]);

		// return;

		// $old_users = DB::connection('old')->table('users')->get();

		// foreach ($old_users as $old_user) {
		// 	$user = new User;
		// 	$user->id = $old_user->ID;
		// 	$user->user_type_id = $old_user->account;
		// 	$user->lab_id = $old_user->lab;
		// 	$user->surname = $old_user->surname;
		// 	$user->oname = $old_user->oname;
		// 	$user->email = $old_user->email;

		// 	if($old_user->flag == 0) $user->deleted_at = date('Y-m-d H:i:s');

		// 	$existing = User::withTrashed()->where('email', $old_user->email)->get()->first();
		// 	if($existing) $user->email = str_random(5) . $user->email;

		// 	$user->password = env('DEFAULT_PASSWORD', 12345678);
		// 	$user->save();
		// }


		User::truncate();
        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 0,
	        'surname' => 'Kithinji',
	        'oname' => 'Joel',
	        'email' => 'joelkith@gmail.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 0,
	        'surname' => 'Bakasa',
	        'oname' => 'Joshua',
	        'email' => 'bakasa@gmail.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 0,
	        'surname' => 'Ngugi',
	        'oname' => 'Tim',
	        'email' => 'tim@gmail.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 0,
	        'surname' => 'Lusike',
	        'oname' => 'Judy',
	        'email' => 'judy@gmail.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 2,
	        'surname' => 'Default',
	        'oname' => 'Admin',
	        'email' => 'admin@admin.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 7,
	        'surname' => 'POC',
	        'oname' => 'Admin',
	        'email' => 'poc@gmail.com',
    	]);

        $users = factory(App\User::class, 1)->create([
	        'user_type_id' => 0,
	        'surname' => 'Orando',
	        'oname' => 'Walter',
	        'email' => 'walter.orando@dataposit.co.ke',
    	]);

    	if (env('APP_LAB') == 7){
	        $users = factory(App\User::class, 1)->create([
		        'user_type_id' => 1,
		        'surname' => 'Kingwara',
		        'oname' => 'Leonard',
		        'email' => 'leonard.kingwara@gmail.com',
		        'password' => '12345678'
	    	]);
    	}

        if (env('APP_LAB') == 2){ // EDARP user to approve samples which are staged in Kisumu
	        $users = factory(App\User::class, 1)->create([
		        'user_type_id' => 8,
		        'surname' => 'EDARP',
		        'oname' => 'Admin',
		        'email' => 'edarp@gmail.com',
		        'password' => encrypt('edarp'.env('APP_KEY').'@edarp')
	    	]);
	    	$users = factory(App\User::class, 1)->create([
	    		'user_type_id' => 9,
	    		'surname' => 'NHRL',
	    		'oname' => 'Admin',
	    		'email' => 'nhrl@gmail.com',
	    		'password' => encrypt('nhrl'.env('APP_KEY').'@nhrl')
	    	]);
	    }

    	$facilitys = DB::table('facilitys')->get();

    	$i=0;
    	$data= null;

    	foreach ($facilitys as $key => $facility) {
    		$fac = factory(App\User::class, 1)->create([
		        'user_type_id' => 5,
		        'surname' => '',
		        'oname' => '',
		        'facility_id' => $facility->id,
		        'email' => 'facility' . $facility->id . '@nascop-lab.com',
		        'password' => encrypt($facility->name)
	    	]);
    	}
    }
}
