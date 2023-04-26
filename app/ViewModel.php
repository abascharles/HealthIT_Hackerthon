<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewModel extends Model
{


    public function getBarcodeAttribute()
    {
        $l = strlen($this->id);
        if($l < 5) return '00000' . $this->id;
        return $this->id;
    }
    
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function view_facility()
    {
        return $this->belongsTo(ViewFacility::class, 'facility_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function final_approver()
    {
        return $this->belongsTo(User::class, 'approvedby2');
    }

    public function my_date_format($value)
    {
        if($this->$value) return date('d-M-Y', strtotime($this->$value));

        return '';
    }

    public function my_time_format($value)
    {
        if($this->$value) return date('d-M-Y H:i:s', strtotime($this->$value));

        return '';
    }


    public function scopeSample($query, $facility, $patient, $datecollected)
    {
        $min_date = date('Y-m-d', strtotime($datecollected . ' -3 days'));
        $max_date = date('Y-m-d', strtotime($datecollected . ' +3 days'));
        return $query->where(['facility_id' => $facility, 'patient' => $patient])
                        ->whereBetween('datecollected', [$min_date, $max_date]);
    }

    public function scopeExisting($query, $data_array)
    {
        // return $query->where(['facility_id' => $data_array['facility_id'], 'patient' => $data_array['patient'], 'datecollected' => $data_array['datecollected'], ]);

        $min_date = date('Y-m-d', strtotime($data_array['datecollected'] . ' -3 days'));
        $max_date = date('Y-m-d', strtotime($data_array['datecollected'] . ' +3 days'));
        return $query->where(['facility_id' => $data_array['facility_id'], 'patient' => $data_array['patient'], 'batch_complete' => 0])
                    // ->whereBetween('datecollected', [$min_date, $max_date])
                    ->where('created_at', '>', date('Y-m-d', strtotime('-1 month')))
                    ->where('patient', '!=', '')
                    ->whereRaw("(receivedstatus IS NULL OR receivedstatus = 1) ");
    }

    public function scopePatient($query, $facility, $patient)
    {
        return $query->where(['facility_id' => $facility, 'patient' => $patient]);
    }



    public function getAgeInDaysAttribute()
    {
        $now = \Carbon\Carbon::now();
        return $now->diffInDays($this->created_at);
    }

    public function getAgeInWeeksAttribute()
    {
        $now = \Carbon\Carbon::now();
        return $now->diffInWeeks($this->created_at);
    }

    public function getAgeInMonthsAttribute()
    {
        $now = \Carbon\Carbon::now();
        return $now->diffInMonths($this->created_at);
    }

    

    public function getIsReadyAttribute()
    {
        if($this->repeatt == 0){
            if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
                if(($this->dateapproved && $this->dateapproved2) || ($this->approvedby && $this->approvedby2)){
                    return true;
                }
            }
            else{
                if($this->dateapproved || $this->approvedby) return true;
            }
        }
        return false;
    }

    public function get_link($attr)
    {
        $user = auth()->user();
        $c = get_class($this);
        $c = strtolower($c);
        $c = str_replace_first('app\\', '', $c);

        $pre = '';
        if(\Str::contains($c, 'viral')) $pre = 'viral';
        if(\Str::contains($c, 'dr')) $pre = 'dr_';
        $user = auth()->user();

        if(\Str::contains($attr, 'extraction')) $url = url('dr_extraction_worksheet/gel_documentation/' . $this->$attr);
        else if(\Str::contains($attr, 'worksheet')) $url = url($pre . 'worksheet/approve/' . $this->$attr);
        else if(\Str::contains($attr, 'sample') || (\Str::contains($c, 'sample') && $attr == 'id')) $url = url($pre . 'sample/runs/' . $this->$attr);
        else{
            $a = explode('_', $attr);
            $url = url($pre . $a[0] . '/' . $this->$attr);
        }

        if($attr == 'id' && (!$user || ($user && $user->user_type_id == 5))) return null;

        if(\Str::contains($attr, ['worksheet', 'sample']) && (!$user || ($user && $user->user_type_id == 5))) return $this->$attr;

        $text = $this->$attr;

        if(\Str::contains($attr, 'patient')) $text = $this->patient;

        $full_link = "<a href='{$url}' target='_blank'> {$text} </a>";

        return $full_link;
    }

    public function get_prop_name($coll, $attr, $attr2='name')
    {
        if(!$this->$attr) return '';
        foreach ($coll as $value) {
            if($value->id == $this->$attr) return $value->$attr2;
        }
        return '';
    }


    /**
     * Get the patient's gender
     *
     * @return string
     */
    public function getGenderAttribute()
    {
        if($this->sex == 1){ return "Male"; }
        else if($this->sex == 2){ return "Female"; }
        else{ return "No Gender"; }
    }


    /**
     * Get the sample's received status name
     *
     * @return string
     */
    public function getReceivedAttribute()
    {
        if($this->receivedstatus == 1){ return "Accepted"; }
        else if($this->receivedstatus == 2){ return "Rejected"; }
        else{ return ""; }
    }

    public function my_boolean_format($value)
    {
        if($this->$value) return "Yes";
        return '';
    }

    public function getRouteNameAttribute()
    {
        $a = explode('\\', get_class($this));
        $c = end($a);
        $c =  snake_case($c);

        return str_replace('_view', '', $c);
    }

    public function getViewUrlAttribute()
    {
        return url($this->route_name . '/' . $this->id);
    }

    public function getViewLinkAttribute()
    {
        return "<a href='" . url($this->route_name . '/' . $this->id) . "'> View </a>";
    }

    public function getEditLinkAttribute()
    {
        return "<a href='" . url($this->route_name . '/' . $this->id . '/edit') . "'> Edit </a>";
    }

    public function getDeleteFormAttribute()
    {        
        $form = "<form action='" . $this->view_url . "' method='POST'>";
        $form .= csrf_field() . method_field('DELETE');
        // $form .= "<button type='submit' class='btn btn-sm btn-primary delete-btn'>Delete</button>";
        $form .= "<button class='btn btn-sm btn-primary delete-btn'>Delete</button>";
        $form .= '</form>';
        return $form;
    }
}
