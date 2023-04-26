<?php

namespace App;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    // use \Venturecraft\Revisionable\RevisionableTrait;
    // protected $revisionEnabled = true;
    // protected $revisionCleanup = true; 
    // protected $historyLimit = 500; 
    
    use Notifiable;
    use SoftDeletes;


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'password', '_token', 'created_at', 'updated_at'];

    /**
     * The attributes that are automatically mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function routeNotificationForMail($notification)
    {
        return $this->facility_email ?? $this->email;
    }


    public function scopeLabUser($query)
    {
        return $query->whereIn('user_type_id', [1, 4])->where('email', '!=', 'rufus.nyaga@ken.aphl.org');
    }

    public function scopeCovidLabUser($query)
    {
        return $query->whereIn('user_type_id', [1, 4, 13])->where('email', '!=', 'rufus.nyaga@ken.aphl.org');
    }
    

    /**
     * Get the user's full name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->surname} {$this->oname}";
    }

    public function getIsLabUserAttribute()
    {
        if(in_array($this->user_type_id, [0, 1, 4])) return true;
        return false;
    }

    public function getIsAdminAttribute()
    {
        if(in_array($this->user_type_id, [0, 2])) return true;
        return false;
    }

    public function getIsFacilityAttribute()
    {
        if($this->user_type_id == 5) return true;
        return false;
    }

    public function getIsPartnerAttribute()
    {
        if($this->user_type_id == 10) return true;
        return false;
    }

    public function getIsNotLabUserAttribute()
    {
        if(in_array($this->user_type_id, [5,10,11])) return true;
        return false;
    }

    public function user_type()
    {
        return $this->belongsTo('App\UserType');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab_id');        
    }

    public function quarantine()
    {
        return $this->belongsTo('App\QuarantineSite', 'facility_id');
    }

    public function twg()
    {
        return $this->belongsTo('App\UlizaTwg', 'twg_id');        
    }

    public function getUlizaSuperAdminAttribute()
    {
        if($this->user_type_id == 101) return true;
        return false;
    }

    public function getUlizaAdminAttribute()
    {
        if($this->user_type_id == 101 || $this->user_type_id == 102) return true;
        return false;
    }

    public function getUlizaSecretariatAttribute()
    {
        if($this->user_type_id == 103) return true;
        return false;
    }

    public function getUlizaReviewerAttribute()
    {
        if($this->user_type_id == 104) return true;
        return false;
    }

    public function set_last_access()
    {
        $this->last_access = date('Y-m-d H:i:s');
        $this->save();
    }

    public function getLastLoginAttribute()
    {
        if ($this->last_access)
            return date('d M, Y', strtotime($this->last_access));
        return null;
    }

    public function is_lab_user()
    {
        if(in_array($this->user_type_id, [0, 1, 4])) return true;
        return false;
    }

    public function is_covid_lab_user()
    {
        if(in_array($this->user_type_id, [0, 1, 4, 12, 13, 14, 15, 16])) return true;
        return false;
    }

    public function is_admin()
    {
        if(in_array($this->user_type_id, [0, 2])) return true;
        return false;
    }

    public function is_facility()
    {
        if(in_array($this->user_type_id, [5])) return true;
        return false;
    }

    public function getQuarantineSiteAttribute()
    {
        if(in_array($this->user_type_id, [11])) return true;
        return false;
    }

    public function getOtherLabAttribute()
    {
        if(in_array($this->user_type_id, [12])) return true;
        return false;
    }

    public function getFacilityUserAttribute()
    {
        if(in_array($this->user_type_id, [5])) return true;
        return false;
    }

    public function getLabUserAttribute()
    {
        if(in_array($this->user_type_id, [0,1,4])) return true;
        return false;
    }

    public function notifiedAllocation()
    {
        return $this->where('allocation_notification', 1);
    }

    private function getSampleTable($testtype)
    {
        $testtype = strtoupper($testtype);
        if ($testtype == 'EID') {
            $model = SampleView::selectRaw("COUNT(*) as samples");
        } else if ($testtype == 'VL') {
            $model = ViralsampleView::selectRaw("COUNT(*) as samples");
        } else {
            return null;
        }
        return $model;
    }

    public function samples_entered($testtype, $year=null, $month=null, $date=null) {
        $id = $this->id;
        $model = $this->getSampleTable($testtype);
        if ($model) {
            $model = $model->when($year, function($query) use ($year, $month){
                        return $query->whereYear('created_at', $year)
                                ->when($month, function($innerQuery) use ($month) {
                                    return $innerQuery->whereMonth('datereceived', $month);
                                });
                    })->when($date, function($query) use ($date){
                        return $query->whereDate('datereceived', "$date");
                    })->where('user_id', '=',$id);

            return number_format($model->first()->samples);
        }
        return 0;
    }

    public function sitesamplesapproved($testtype, $year, $month=null) {
        $id = $this->id;
        $model = $this->getSampleTable($testtype);
        if ($model) {
            $model = $model->whereYear('datereceived', $year)
                    ->when($month, function($query) use ($month) {
                        return $query->whereMonth('datereceived', $month);
                    })->where('received_by', '=',$id)->where('site_entry', '=', 1);

            return number_format($model->first()->samples);
        }
        return 0;
    }

    public function uploaded($from_date, $to_date = null) {
        $user = $this->id;
        $eid_worksheets = \App\Worksheet::selectRaw("COUNT(*) AS worksheets")
                            ->where('uploadedby', $user)
                            ->when(true, function($query) use ($from_date, $to_date){
                                if (isset($to_date)) {
                                    $query->whereBetween('dateuploaded', [$from_date, $to_date]);
                                } else {
                                    $query->where('dateuploaded', $from_date);
                                }                                
                            })->first()->worksheets;

        $vl_worksheets = \App\Viralworksheet::selectRaw("COUNT(*) AS worksheets")
                            ->where('uploadedby', $user)
                            ->when(true, function($query) use ($from_date, $to_date){
                                if (isset($to_date)) {
                                    $query->whereBetween('dateuploaded', [$from_date, $to_date]);
                                } else {
                                    $query->where('dateuploaded', $from_date);
                                }                                
                            })->first()->worksheets;
        return (object)['eid' => $eid_worksheets, 'vl' => $vl_worksheets];
    }

    public function reviewed($from_date, $to_date = null) {
        $user = $this->id;
        $eid_worksheets = \App\Worksheet::selectRaw("COUNT(*) AS worksheets")
                            ->where('reviewedby', $user)->orWhere('reviewedby2', $user)
                            ->when(true, function($query) use ($from_date, $to_date){
                                if (isset($to_date)) {
                                    $query->whereBetween('dateuploaded', [$from_date, $to_date]);
                                } else {
                                    $query->where('dateuploaded', $from_date);
                                }                                
                            })->first()->worksheets;

        $vl_worksheets = \App\Viralworksheet::selectRaw("COUNT(*) AS worksheets")
                            ->where('reviewedby', $user)->orWhere('reviewedby2', $user)
                            ->when(true, function($query) use ($from_date, $to_date){
                                if (isset($to_date)) {
                                    $query->whereBetween('datereviewed', [$from_date, $to_date])->orWhereBetween('datereviewed', [$from_date, $to_date]);
                                } else {
                                    $query->where('datereviewed', $from_date)->orWhere('datereviewed', $from_date);
                                }                                
                            })->first()->worksheets;
        return (object)['eid' => $eid_worksheets, 'vl' => $vl_worksheets];
    }

    public function samples_recevied($from_date, $to_date = null){
        $user = $this->id;
        // $eid_sampels = \App\Sample::
    }

    public function samplesLoggedToday($date)
    {
        return ($this->samples_entered('EID', null, null, $date) + $this->samples_entered('VL', null, null, $date));
    }

    public function samplesApprovedToday($date)
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getSampleTable($testtype);
            if ($model) {
                $model = $model->whereRaw("(approvedby = $user or approvedby2 = $user)")
                            ->whereRaw("(date(dateapproved) = '" . $date . "' or date(dateapproved2) = '" . $date . "')");
                $total += $model->first()->samples;
            }
        }
        return number_format($total);
    }

    public function samplesDispatchedToday()
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getSampleTable($testtype);
            if ($model) {
                $model = $model->whereDate('datedispatched', date('Y-m-d'))
                            ->where('user_id', $user);
                $total += $model->first()->samples;
            }
        }
        return $total;
    }

    private function getWorksheetTable($testtype)
    {
        $testtype = strtoupper($testtype);
        if ($testtype == 'EID') {
            $model = \App\Worksheet::selectRaw("COUNT(*) AS worksheets");
        } else if ($testtype == 'VL') {
            $model = \App\Viralworksheet::selectRaw("COUNT(*) AS worksheets");
        } else {
            return null;
        }
        return $model;
    }

    public function worksheetsSortedToday($date)
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getWorksheetTable($testtype);
            if ($model) {
                $model = $model->where('sortedby', $user)->whereDate('created_at', "$date");
                $total += $model->first()->worksheets;
            }
        }
        return number_format($total);
    }

    public function worksheetsAliquotedToday($date)
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getWorksheetTable($testtype);
            if ($model) {
                $model = $model->where('alliquotedby', $user)->whereDate('created_at', "$date");
                $total += $model->first()->worksheets;
            }
        }
        return number_format($total);
    }

    public function worksheetsRunToday($date)
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getWorksheetTable($testtype);
            if ($model) {
                $model = $model->where('runby', $user)->whereDate('daterun', "$date");
                $total += $model->first()->worksheets;
            }
        }
        return number_format($total);
    }

    public function worksheetReviewedToday($date)
    {
        $user = $this->id;
        $total = 0;
        $testtypes = ['EID', 'VL'];
        foreach ($testtypes as $key => $testtype) {
            $model = $this->getWorksheetTable($testtype);
            if ($model) {
                $model = $model->whereRaw("reviewedby = {$user} or reviewedby2 = {$user}")
                            ->whereRaw("datereviewed = '" . $date . "' or datereviewed2 = '" . $date . "'");
                $total += $model->first()->worksheets;
            }
        }
        return number_format($total);
    }

    public function dailyLogs()
    {
        $data = [];
        for ($i=0; $i < 60; $i++) { 
            $date = date("Y-m-d", strtotime("-" .$i. " days"));
            $data[] = (object)[
                    'date' => $date,
                    'samples_logged' => $this->samplesLoggedToday($date),
                    'samples_approved' => $this->samplesApprovedToday($date),
                    'worksheets_sorted' => $this->worksheetsSortedToday($date),
                    'worksheets_aliquoted' => $this->worksheetsAliquotedToday($date),
                    'worksheets_run' => $this->worksheetsRunToday($date),
                    'samples_dispatched' => $this->samplesDispatchedToday($date)
                ];
        }        
        
        return $data;
    }
}
