<?php

namespace App;

use App\BaseModel;

class LabPerformanceTracker extends BaseModel
{
    // protected $fillable = ['lab_id','month','year','dateemailsent','testtype','sampletype','received','rejected','loggedin','notlogged','tested','reasonforbacklog','datesubmitted','submittedBy'];

    public function lab()
    {
        return $this->belongsTo('App\Lab');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submittedBy');
    }

    public function scopeExisting($query, $year, $month, $testtype, $sampletype)
    {
        return $query->where(['year' => $year, 'month' => $month, 'testtype' => $testtype, 'sampletype' => $sampletype]);
    }

    public static function resetemail($year, $month, $restrict = false)
    {
        $today = date('Y-m-d');
        $threemonthsago = date('Y-m-d', strtotime("-3 Months", strtotime($today)));
        
        $performances = LabPerformanceTracker::where('year', $year)
                            ->when($month, function($query) use($month){
                                return $query->where('month', $month);
                            })->whereNull('dateemailsent')
                            ->when($restrict, function($query) use ($threemonthsago){
                                return $query->whereDate('datesubmitted', '<', $threemonthsago);
                            })->get();
        echo "==> Records found {$performances->count()}";
        foreach ($performances as $key => $performance) {
            $performance->dateemailsent = $performance->datesubmitted;
            $performance->save();
        }
        return true;
    }
}


