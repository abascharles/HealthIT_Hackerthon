<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationDetail extends BaseModel
{
    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    public function allocation() {
        return $this->belongsTo('App\Allocation');
    }
    
    public function breakdowns() {
        return $this->hasMany('App\AllocationDetailsBreakdown');
    }

    public function machine(){
        return $this->belongsTo('App\Machine');
    }
}
