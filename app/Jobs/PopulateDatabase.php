<?php

namespace App\Jobs;

use App\Facility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $results;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($results)
    {
        $this->results=$results;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $this->populateDatabase($this->results);
    }

    private function populateDatabase($results)
    {
        foreach ($results as $result)
        {
            $code=$result['code'];
            $name=$result['official_name'];
            $date_created=$result['created'];
            $date_updated=$result['updated'];
            $status=(boolean)$result['is_approved'];
            if(Facility::where('facilitycode','=',$code)->exists())
            {
                $facility=Facility::where('facilitycode','=',$code)->first();
                if($facility->name != $name)
                {
                    Facility::where('facilitycode','=',$code)->update(
                        [
                            'name'=>$name,
                        ]
                    );
                }
                Facility::where('facilitycode','=',$code)->update(
                    [
                           'status'=>'1',
                           'created_at'=>$date_created,
                           'updated_at'=>$date_updated
                    ]
                );
            }
            else
            {
                try
                {
                    if($code!=null && $status==true)
                    {
                        DB::table('facilitys')->insert(
                            ['facilitycode' => $code, 'name' => $name,'status'=>'1','created_at'=>$date_created,'updated_at'=>$date_updated ]
                        );
                    }
                }
               catch (\Exception $exception)
               {
                   Log::error($exception->getMessage());
               }
            }
        }

    }
}
