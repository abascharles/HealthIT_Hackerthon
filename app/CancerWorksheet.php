<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancerWorksheet extends BaseModel
{
    protected $dates = ['datecut', 'datereviewed', 'datereviewed2', 'dateuploaded', 'datecancelled', 'daterun', 'kitexpirydate',  'sampleprepexpirydate',  'bulklysisexpirydate',  'controlexpirydate',  'calibratorexpirydate',  'amplificationexpirydate', ];

    // protected $withCount = ['sample'];
    
    // public $timestamps = false;

    public function sample()
    {
    	return $this->hasMany('App\CancerSample', 'worksheet_id');
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

    public function scopeExisting($query, $createdby, $created_at)
    {
        return $query->where(['createdby' => $createdby, 'created_at' => $created_at]);
    }


    public function getFailedAttribute()
    {
        if(!in_array($this->neg_control_result, [1,6]) || !in_array($this->pos_control_result, [2,6])) return true;
        return false;
    }

    public function getReversibleAttribute()
    {
        // if(!in_array($this->status_id, [3,7]) || $this->daterun->lessThan(date('Y-m-d', strtotime('-2 days')))){
        //     return false;
        // }
        if(!in_array(auth()->user()->id, [$this->reviewedby, $this->reviewedby2])) return false;

        return true;
    }
}
