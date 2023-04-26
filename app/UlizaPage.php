<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use DB;
use Str;
use Exception;
use App\Notifications\NewUlizaUserNotification;

class UlizaPage extends Model
{
	public $timestamps = false;

    public function save_raw($page_content, $attr=null)
    {
    	$file = $attr ?? $this->link;
    	if(!is_dir(storage_path('app/uliza/pages'))) mkdir(storage_path('app/uliza/pages'), 0777, true);

    	$filename = storage_path('app/uliza/pages') . '/' . $file . '.html';

    	file_put_contents($filename, $page_content);
    }

    public function get_raw($attr=null)
    {
    	$file = $attr ?? $this->link;
    	if(!is_dir(storage_path('app/uliza/pages'))) mkdir(storage_path('app/uliza/pages'), 0777, true);

    	$filename = storage_path('app/uliza/pages') . '/' . $file . '.html';
    	if(!file_exists($filename)) return null;
    	return file_get_contents($filename);
    }

    // 

    public function seed()
    {
    	$this->create_table();
    	$this->create_rows();
    }

    public function create_pages()
    {
    	\DB::statement('DROP TABLE IF EXISTS uliza_pages');
    	\DB::statement("
    		CREATE TABLE uliza_pages(
    			id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    			link varchar(30) UNIQUE,
    			title varchar(30) DEFAULT NULL,
                PRIMARY KEY (`id`)
    		)
    	");
        \DB::table('uliza_pages')->insert([
            ['link' => 'home', 'title' => 'Executive Summary'],
            ['link' => 'uliza', 'title' => 'Uliza-NASCOP'],
            ['link' => 'ushauri', 'title' => 'Ushauri'],
            ['link' => 'trainsmart', 'title' => 'TrainSMART'],
            ['link' => 'echo', 'title' => 'Echo'],
            ['link' => 'faqs', 'title' => 'Frequently Asked Questions'],
            ['link' => 'contactus', 'title' => 'Contact Us'],

            ['link' => 'home2', 'title' => 'Functions'],
        ]);
    }

    public function create_reasons()
    {
        \DB::statement('DROP TABLE IF EXISTS uliza_reasons');
        \DB::statement("
            CREATE TABLE uliza_reasons(
                id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(100),
                public tinyint(3) DEFAULT 1,
                PRIMARY KEY (`id`)
            )
        ");

        \DB::table('uliza_reasons')->insert([
            ['name' => 'Adverse drug reaction'],
            ['name' => 'Private cross-over patient'],
            ['name' => 'Single TDF out of stock'],
            ['name' => 'Drug interactions'],
            ['name' => 'Second line failure'],
            ['name' => 'Study cross-over patient'],
            ['name' => 'First line failure'],
            ['name' => 'Single AZT out of stock'],
            ['name' => 'Other (Please Specify)'],
        ]);

        \DB::table('uliza_reasons')->insert([
            ['name' => 'Confirmed first line failure', 'public' => 0],
            ['name' => 'Confirmed first line PI based regimen failure', 'public' => 0],
            ['name' => 'Confirmed second line failure', 'public' => 0],
            ['name' => 'Suspected First line failure', 'public' => 0],
            ['name' => 'Suspected first line PI regimen failure', 'public' => 0],
            ['name' => 'Suspected Second line failure', 'public' => 0],
        ]);
    }

    public function create_recommendations()
    {
        \DB::statement('DROP TABLE IF EXISTS uliza_recommendations');
        \DB::statement("
            CREATE TABLE uliza_recommendations(
                id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(100),
                PRIMARY KEY (`id`)
            )
        ");

        \DB::table('uliza_recommendations')->insert([
            ['id' => 1, 'name' => 'Additional Information Required From Facility'],
            ['id' => 5, 'name' => 'Additional Information Required From RTWG'],
            ['id' => 3, 'name' => 'Provide Feedback To Facility Directly'],
            ['id' => 2, 'name' => 'Refer To Technical Reviewer'],
            ['id' => 6, 'name' => 'Send Feedback To RTWG'],
        ]);

    }

    public function create_case_statuses()
    {
        \DB::statement('DROP TABLE IF EXISTS uliza_case_statuses');
        \DB::statement("
            CREATE TABLE uliza_case_statuses(
                id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(100),
                PRIMARY KEY (`id`)
            )
        ");

        \DB::table('uliza_case_statuses')->insert([
            ['id' => 1, 'name' => 'Pending'],
            ['id' => 2, 'name' => 'Under Review'],
            ['id' => 3, 'name' => 'Completed'],
            ['id' => 4, 'name' => 'Finalised'],
            ['id' => 5, 'name' => 'Approved For DRT'],
            ['id' => 6, 'name' => 'Undergoing DRT'],
        ]);
    }

    public function create_facility_feedbacks()
    {
        \DB::statement('DROP TABLE IF EXISTS uliza_facility_feedbacks');
        \DB::statement("
            CREATE TABLE uliza_facility_feedbacks(
                id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(150),
                PRIMARY KEY (`id`)
            )
        ");

        \DB::table('uliza_facility_feedbacks')->insert([
            ['id' => 1, 'name' => '2nd line Approved'],
            ['id' => 2, 'name' => '3rd line Recommended'],
            ['id' => 3, 'name' => 'Continue Current Regimen'],
            ['id' => 4, 'name' => 'DRT Recommended'],
            ['id' => 5, 'name' => 'Enhance adherence and repeat viral load after 3/12 of good adherence, if detectable, send for a DRT'],
            ['id' => 6, 'name' => 'Repeat viral load'],
            ['id' => 7, 'name' => 'Substitute ART drugs'],
        ]);
    }


    public static function entry_pdf($ulizaClinicalForm, $file_path=null, $download=false)
    {
        if(!$file_path) $file_path = $ulizaClinicalForm->entry_path;

        if(file_exists($file_path)) unlink($file_path);

        $file_name = explode('/', $file_path);
        $file_name = array_pop($file_name);


        $mpdf = new Mpdf();
        $reasons = DB::table('uliza_reasons')->where('public', 1)->get();
        $regimens = DB::table('viralregimen')->get();
        $view_data = view('uliza.exports.clinical_form', compact('reasons', 'regimens', 'ulizaClinicalForm'))->render();
        $mpdf->WriteHTML($view_data);

        if($download) return $mpdf->Output($file_name, \Mpdf\Output\Destination::DOWNLOAD);
        else{
            $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);
        }
    }

    public static function add_users(){
        // ALTER TABLE `users` ADD COLUMN `sent_email` tinyint default 1 AFTER twg_id;
        $twg_id = 5;

        $file = base_path('uliza_nyawest_reviewer.csv');

        $line = fgets(fopen($file, 'r'));
        $delimiter = ',';
        if(Str::contains($line, ';')) $delimiter = ';';

        $handle = fopen($file, "r");

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE){
            if($row[0] == '#') continue;

            $user = User::where('email', $row[2])->first();
            if(!$user) $user = new User;
            
            $user->twg_id = $twg_id;
            $user->receive_emails = true;
            $user->sent_email = false;

            $names = explode(' ', $row[1]);
            $user->surname = Str::title(array_pop($names));
            $user->oname = Str::title(implode(' ', $names));

            $user->email = $row[2];
            $user->telephone = $row[3];

            if(Str::startsWith($row[4], 'Tech')) $user->user_type_id = 104;
            else{
                $user->user_type_id = 103;
            }
            $password = \Str::random(15);
            $user->password = $password;
            $user->save();
        }
        self::send_user_emails();
    }

    public static function send_user_emails()
    {
        $users  = User::where('sent_email', false)->where('user_type_id', '>', 100)->get();
        foreach ($users as $key => $user) {
            $password = Str::random(15);
            $user->password = $password;
            $user->save();

            try {
                $user->notify(new NewUlizaUserNotification($password));  
                $user->sent_email = true;
                $user->save();              
            } catch (Exception $e) {
                
            }
        }
    }


}
