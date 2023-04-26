<?php
namespace App\CustomClass;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\t_log;

class record_log
{
    public static function  save_log($sample_id,$patient_id,$batch_id,$action,$trail_description)
    {
        try{
            $user = auth()->user();
            $date=date('Y-m-d h:i:s');
            $id=Str::uuid()->toString();
            DB::table('sample_log_trail')->insert(
                [
                    'id'=>$id,
                    'sample_id'=>$sample_id,
                    'user_id'=>$user->id,
                    'action'=>$action,
                    'patient_id'=>$patient_id,
                    'batch_id'=>$batch_id,
                    'created_at'=>$date,
                    'updated_at'=>$date,
                     'log_description'=>$trail_description


                ]
            );
            return $id;

        } catch(\Illuminate\Database\QueryException $ex){
           Log::error($ex->getMessage());
        }

    }
}