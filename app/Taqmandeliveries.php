<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taqmandeliveries extends BaseModel
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    // protected $fillable = ['testtype','lab','quarter','year','source','labfrom','kitlotno','expirydate','qualkitreceived','spexagentreceived','ampinputreceived','ampflaplessreceived','ampktipsreceived','ampwashreceived','ktubesreceived','consumablesreceived','qualkitdamaged','spexagentdamaged','ampinputdamaged','ampflaplessdamaged','ampktipsdamaged','ampwashdamaged','ktubesdamaged','consumablesdamaged','receivedby','datereceived','status','enteredby','dateentered','flag','datesynchronized'];



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
