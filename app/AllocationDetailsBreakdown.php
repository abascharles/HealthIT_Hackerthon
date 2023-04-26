<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationDetailsBreakdown extends BaseModel
{
    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    public function breakdown()
    {
        return $this->morphTo();
    }
}
