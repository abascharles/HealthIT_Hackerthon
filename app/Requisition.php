<?php

namespace App;

use App\BaseModel;

class Requisition extends BaseModel
{
    // protected $fillable = ['facility','lab','request','supply','comments','createdby','created_at','approvedby','approvecomments','disapprovecomments','status','flag','parentid','requisitiondate','datesubmitted','submittedby','dateapproved','datesynchronized'];


    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility', 'facilitycode');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submittedby');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    
}
