<?php

namespace App;

use App\BaseModel;

class Taqmanprocurement extends BaseModel
{
    // protected $fillable = ['month','year','testtype','received','tests','datesubmitted','submittedBy','lab_id','datesynchronized','comments','issuedcomments','approve','disapproverreason','endingqualkit','endingspexagent','endingampinput','endingampflapless','endingampktips','endingampwash','endingktubes','endingconsumables','wastedqualkit','wastedspexagent','wastedampinput','wastedampflapless','wastedampktips','wastedampwash','wastedktubes','wastedconsumables','issuedqualkit','issuedspexagent','issuedampinput','issuedampflapless','issuedampktips','issuedampwash','issuedktubes','issuedconsumables','requestqualkit','requestspexagent','requestampinput','requestampflapless','requestampktips','requestampwash','requestktubes','requestconsumables','posqualkit','posspexagent','posampinput','posampflapless','posampktips','posampwash','posktubes','posconsumables'];

    

    public function lab()
    {
        return $this->belongsTo('App\Lab');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submittedBy');
    }
    
    public function scopeExisting($query, $year, $month, $testtype)
    {
        return $query->where(['year' => $year, 'month' => $month, 'testtype' => $testtype]);
    }
}
