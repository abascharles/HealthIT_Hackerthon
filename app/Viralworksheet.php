<?php

namespace App;

class Viralworksheet extends BaseModel
{
    protected $dates = ['datecut', 'datereviewed', 'datereviewed2', 'dateuploaded', 'datecancelled', 'daterun', 'kitexpirydate',  'sampleprepexpirydate',  'bulklysisexpirydate',  'controlexpirydate',  'calibratorexpirydate',  'amplificationexpirydate', ];

    // protected $withCount = ['sample'];  
    
    // public $timestamps = false;

    public function sample()
    {
    	return $this->hasMany(Viralsample::class, 'worksheet_id');
    }

    public function runner()
    {
    	return $this->belongsTo(User::class, 'runby');
    }

    public function sorter()
    {
        return $this->belongsTo(User::class, 'sortedby');
    }

    public function bulker()
    {
        return $this->belongsTo(User::class, 'bulkedby');
    }

    public function quoter()
    {
        return $this->belongsTo(User::class, 'alliquotedby');
    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'createdby');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploadedby');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelledby');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewedby');
    }

    public function reviewer2()
    {
        return $this->belongsTo(User::class, 'reviewedby2');
    }
    public function lab()
    {
        return $this->belongsTo('App\Lab');
    }

    public function getFailedAttribute()
    {
        if(in_array('Failed', [$this->neg_control_result, $this->highpos_control_result, $this->lowpos_control_result])) return true;
        return false;
    }

    public function getReversibleAttribute()
    {
        if(!in_array($this->status_id, [3,7]) || $this->daterun->lessThan(date('Y-m-d', strtotime('-2 days')))){
            return false;
        }
        if(!in_array(auth()->user()->id, [$this->reviewedby, $this->reviewedby2])) return false;

        return true;
    }


    public function getSampleTypeNameAttribute()
    {
        if($this->sampletype == 1) return "DBS";
        if($this->sampletype == 2) return "Plasma / EDTA";
    }

    public function getDumpLinkAttribute()
    {
        if(env('APP_LAB') == 9){
            $s = \App\ViralsampleView::where('worksheet_id', $this->id)->whereIn('facility_id', [50001, 3475])->first();
            if(!$s) return '';
            $url = url('viralworksheet/download_dump/' . $this->id);
            return "<a href='{$url}'> Download For EMR (IQCare) </a> |";
        }
        if(env('APP_LAB') == 8){
            // $s = \App\ViralsampleView::where('worksheet_id', $this->id)->whereIn('facility_id', [50001, 3475])->first();
            // if(!$s) return '';
            $url = url('viralworksheet/download_dump/' . $this->id);
            return "<a href='{$url}'> Download For EMR (IQCare) </a> |";
        }
        return '';
    }

    public function release_as_redraw()
    {
        $today = date('Y-m-d');

        $this->status_id = 3;
        $this->daterun = $this->datereviewed = $this->datereviewed2 = $today;
        $this->save();

        $samples = \App\ViralsampleView::where('worksheet_id', $this->id)->where('site_entry', '!=', 2)->get();

        foreach ($samples as $key => $sample) {
            $s = \App\Viralsample::find($sample->id);

            $s->labcomment = "Failed Test";
            $s->repeatt = 0;
            $s->result = "Collect New Sample";
            $s->dateapproved = $s->dateapproved2 = $today; 

            $s->save();
            \App\MiscViral::check_batch($s->batch_id);
        }
    }

    public function scopeExisting($query, $createdby, $created_at)
    {
        return $query->where(['createdby' => $createdby, 'created_at' => $created_at]);
    }

    public static function dump()
    {
        $getBakasa = User::where('email', 'like', '%bakasa%')->first();
        $batches = Viralbatch::where('received_by', $getBakasa->id)->get();
        // Purge the samples
        if (!$batches->isEmpty()){
            foreach ($batches as $key => $batch) {
                $samples = $batch->sample;
                if (!$samples->isEmpty()){
                    foreach ($samples as $key => $sample) {
                        $sample->delete();
                    }
                }
                $batch->delete();
            }
        }

        // if (!$batches->isEmpty()) {
        //    foreach ($batches as $key => $batch) {
        //         $samples = $batch->sample
        //         foreach ($batch->sample as $key => $sample) {
        //             $worksheet = $sample->worksheet;
        //             $worksheet->createdby = $batch->received_by;
        //             $worksheet->save();
        //         }
        //     }
        // }
        
        return true;
    }
}
