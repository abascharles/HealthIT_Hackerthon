<?php

namespace App;

use App\BaseModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client;
use App\Mail\CustomMail;
use App\Mail\CustomEmailFiles;
use Exception;

class Email extends BaseModel
{
    use SoftDeletes;

    public function county()
    {
        return $this->belongsTo('App\County');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->hasMany('App\Attachment');
    }

    /**
     * Get the user's full name
     *
     * @return string
     */
    public function getContentAttribute()
    {
        return $this->get_raw();
    }

    public function getSendingHourAttribute()
    {
        if($this->time_to_be_sent) return date('H', strtotime($this->time_to_be_sent));
        return null;
    }

    public function getSendingDayAttribute()
    {
        if($this->time_to_be_sent) return date('Y-m-d', strtotime($this->time_to_be_sent));
        return null;
    }

    public function demo_email($recepient)
    {
        $this->save_blade();
        $comm = new CustomMail($this, null);
        Mail::to([$recepient])->send($comm);
        $this->delete_blade();
    }

    public function dispatch()
    {
        if(in_array(env('APP_LAB'), [1, 2, 3, 6])) $this->request_files();
        $this->save_blade();
        ini_set("memory_limit", "-1");
        $min_date = date('Y-m-d', strtotime('-2years'));
        $supported_query = "(id IN (select distinct facility_id from viralbatches where site_entry != 2 and datereceived > '{$min_date}') OR id IN (select distinct facility_id from batches where site_entry != 2 and datereceived > '{$min_date}'))"; 
        $county_id = $this->county_id;

        $facilities = Facility::where('flag', 1)
            ->whereRaw($supported_query)
            ->when($this->county_id, function($query) use ($county_id){
                return $query->whereRaw("id IN (select id from view_facilitys where county_id = {$county_id})");
            })
            ->get();
            
        $this->sent = true;
        $this->save();

        $this->load(['attachment']);

        $cc_array = $this->comma_array($this->cc_list);
        $bcc_array = $this->comma_array($this->bcc_list);
        // $bcc_array = array_merge($bcc_array, ['joel.kithinji@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke']);

        foreach ($facilities as $key => $facility) {
        	$mail_array = $facility->email_array;
            if(!$mail_array) continue;
        	// $mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'baksajoshua09@gmail.com');
        	$comm = new CustomMail($this, $facility);
        	try {
	        	Mail::to($mail_array)->cc($cc_array)->bcc($bcc_array)->send($comm);
	        } catch (Exception $e) {
        	
	        }
        	// break;
        }
        
        $this->send_files();
        $this->delete_blade();
    }

    public function send_files()
    {
        $comm = new CustomEmailFiles($this);
        $mail_array = array('joelkith@gmail.com', 'tngugi@clintonhealthaccess.org', 'baksajoshua09@gmail.com');
        Mail::to($mail_array)->send($comm);
    }

    public function save_raw($email_string)
    {
    	if(!is_dir(storage_path('app/emails'))) mkdir(storage_path('app/emails'), 0777, true);

    	$filename = storage_path('app/emails') . '/' . $this->id . '.txt';

    	file_put_contents($filename, $email_string);
    }

    public function get_raw()
    {
    	if(!is_dir(storage_path('app/emails'))) mkdir(storage_path('app/emails'), 0777, true);

    	$filename = storage_path('app/emails') . '/' . $this->id . '.txt';
    	if(!file_exists($filename)) return null;
    	return file_get_contents($filename);
    }

    public function save_blade()
    {
    	$filename = storage_path('app/emails') . '/' . $this->id . '.txt';
    	$blade = base_path('resources/views/emails') . '/' . $this->id . '.blade.php';

    	$str = file_get_contents($filename);
        $fac_name = '{{ $facility->name ?? ' . "'(Facility Name Here)'"  . ' }}';
        $str = str_replace(':facilityname', $fac_name, $str);
    	if($this->lab_signature) $str .= " @include('emails.lab_signature') ";
    	file_put_contents($blade, $str);
    }

    public function delete_blade()
    {
    	$blade = base_path('resources/views/emails') . '/' . $this->id . '.blade.php';
    	unlink($blade);
    }

    public function delete_raw()
    {
        $filename = storage_path('app/emails') . '/' . $this->id . '.txt';
        if(file_exists($filename)) unlink($filename);
    }

    public function comma_array($str)
    {
        if(!$str || $str == '') return [];
        $emails = explode(',', $str);

        $mail_array = [];

        foreach ($emails as $key => $value) {
            if(\Str::contains($value, '@')) $mail_array[] = trim($value);
        }
        return $mail_array;
    }

    public function request_files()
    {
        $base = env('APP_URL') . '/api/';
        $client = new Client(['base_uri' => $base]);

        $response = $client->request('post', 'auth/login', [
            // 'http_errors' => false,
            // 'debug' => true,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => env('MASTER_USERNAME', null),
                'password' => env('LAB_PASSWORD', null),
            ],
        ]);
        $status_code = $response->getStatusCode();
        $body = json_decode($response->getBody());
        if($status_code > 399) {
            dd($body);
        }

        $body = json_decode($response->getBody());
        $token = $body->token;

        $response = $client->request('post', 'email', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => $this->id,
                'lab_id' => env('APP_LAB'),
            ],
        ]);

        $body = json_decode($response->getBody());
        $this->save_raw($body->email_contents);
        if(!is_dir(storage_path('app/attachments'))) mkdir(storage_path('app/attachments'), 0777, true);

        $attachments = $body->attachments;

        if($attachments)
        {
            for ($i=0; $i < $attachments; $i++) { 

                $response = $client->request('post', 'attachment', [
                    'stream' => true,
                    // 'debug' => true,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'json' => [
                        'email' => $this->id,
                        'attachment' => $i,
                        'lab_id' => env('APP_LAB'),
                    ],
                ]);
                $body = $response->getBody();
                $pdf = '';
                while (!$body->eof()){
                    $pdf .= $body->read(1024);
                }
                $attachment = $this->attachment()->offset($i)->first();
                if(file_exists($attachment->path)) unlink($attachment->path);
                file_put_contents($attachment->path, $pdf);


                // dd($body);
            }
        }
    }
}
