<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abbotdeliveries extends BaseModel
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    // protected $fillable = ['testtype','lab','quarter','year','source','labfrom','qualkitlotno','calibrationlotno','controllotno','bufferlotno','preparationlotno','qualkitexpiry','calibrationexpiry','controlexpiry','bufferexpiry','preparationexpiry','qualkitreceived','calibrationreceived','controlreceived','bufferreceived','preparationreceived','adhesivereceived','deepplatereceived','mixtubereceived','reactionvesselsreceived','reagentreceived','reactionplatereceived','1000disposablereceived','200disposablereceived','qualkitdamaged','calibrationdamaged','controldamaged','bufferdamaged','preparationdamaged','adhesivedamaged','deepplatedamaged','mixtubedamaged','reactionvesselsdamaged','reagentdamaged','reactionplatedamaged','1000disposabledamaged','200disposabledamaged','receivedby','datereceived','status','enteredby','dateentered','flag','datesynchronized'];
    
    public function lab()
    {
        return $this->belongsTo('App\Lab');
    }

    public function lab_from()
    {
        return $this->belongsTo('App\Lab', 'labfrom');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receivedby');
    }

    public function enterer()
    {
        return $this->belongsTo(User::class, 'enteredby');
    }
    
    public function scopeExisting($query, $year, $quarter, $testtype)
    {
        return $query->where(['year' => $year, 'quarter' => $quarter, 'testtype' => $testtype]);
    }
    
}
