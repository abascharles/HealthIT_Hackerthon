<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralConsumables extends Model
{
    protected $fillable = ['name'];
   
    public function commodities()
    {
        return $this->morphMany('App\AllocationDetailsBreakdown', 'breakdown');
    }
   
    public function deliveredkits()
    {
        return $this->morphMany('App\DeliveryDetail', 'kit');
    }
}
