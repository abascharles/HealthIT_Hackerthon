<?php

namespace App\CovidModels;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

	protected $connection = 'covid';
    protected $guarded = ['id', 'created_at', 'updated_at', '_token', '_method', 'kemri_id', 'national_sample_id', 'national_patient_id', 'national_batch_id', 'national_travel_id'];
    // protected $guarded = ['id', 'created_at', 'updated_at', '_token', '_method'];

    public function get_prop_name($coll, $attr, $attr2='name')
    {
        if(!$this->$attr) return '';
        foreach ($coll as $value) {
            if($value->id == $this->$attr) return $value->$attr2;
        }
        return '';
    }

    public function my_date_format($value, $format='d-M-Y')
    {
        if($this->$value) return date($format, strtotime($this->$value));
        return '';
    }
    
    public function get_link($attr)
    {
        $user = auth()->user();
        $c = $this->route_name;

        $pre = '';
        if(\Str::contains($c, 'viral')) $pre = 'viral';
        if(\Str::contains($c, 'dr')) $pre = 'dr_';
        if(\Str::contains($c, 'covid')) $pre = 'covid_';
        $user = auth()->user();

        if(\Str::contains($attr, 'extraction')) $url = url('dr_extraction_worksheet/gel_documentation/' . $this->$attr);
        else if(\Str::contains($attr, 'worksheet')) $url = url($pre . 'worksheet/approve/' . $this->$attr);
        // else if(\Str::contains($attr, 'sample') || (\Str::contains($c, 'sample') && $attr == 'id')) $url = url($c . '/runs/' . $this->$attr);
        else{
            $a = explode('_', $attr);
            $url = url($pre . $a[0] . '/' . $this->$attr);
            // if(\Str::contains($c, 'patient')) $url = url($pre . $a[0] . '/' . $this->patient_id);
        }

        if($attr == 'id' && (!$user || ($user && $user->user_type_id == 5))) return null;

        if(\Str::contains($attr, ['worksheet', 'sample']) && (!$user || ($user && $user->user_type_id == 5))) return $this->$attr;

        $text = $this->$attr;

        if(\Str::contains($c, 'patient')) $text = $this->patient;

        $full_link = "<a href='{$url}' target='_blank'> {$text} </a>";

        return $full_link;
    }
}
