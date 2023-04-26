<?php

namespace App;

use App\BaseModel;

class Abbotprocurement extends BaseModel
{
    // protected $fillable = ['month','year','testtype','received','tests','datesubmitted','submittedBy','lab_id','datesynchronized','comments','issuedcomments','approve','disapproverreason','endingqualkit','endingcalibration','endingcontrol','endingbuffer','endingpreparation','endingadhesive','endingdeepplate','endingmixtube','endingreactionvessels','endingreagent','endingreactionplate','ending1000disposable','ending200disposable','wastedqualkit','wastedcalibration','wastedcontrol','wastedbuffer','wastedpreparation','wastedadhesive','wasteddeepplate','wastedmixtube','wastedreactionvessels','wastedreagent','wastedreactionplate','wasted1000disposable','wasted200disposable','issuedqualkit','issuedcalibration','issuedcontrol','issuedbuffer','issuedpreparation','issuedadhesive','issueddeepplate','issuedmixtube','issuedreactionvessels','issuedreagent','issuedreactionplate','issued1000disposable','issued200disposable','requestqualkit','requestcalibration','requestcontrol','requestbuffer','requestpreparation','requestadhesive','requestdeepplate','requestmixtube','requestreactionvessels','requestreagent','requestreactionplate','request1000disposable','request200disposable','posqualkit','poscalibration','poscontrol','posbuffer','pospreparation','posadhesive','posdeepplate','posmixtube','posreactionvessels','posreagent','posreactionplate','pos1000disposable','pos200disposable'];
    
    

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
