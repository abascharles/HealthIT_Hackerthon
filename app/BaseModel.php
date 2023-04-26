<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; 
    protected $historyLimit = 500; 
    
    protected $guarded = ['id', 'jitenge_sample', 'border_point', 'time_sent_to_cif', 'time_sent_to_nphl', 'sent_to_nphl', 'time_sent_to_kilifi', 'age_unit', 'created_at', 'updated_at', 'deleted_at', '_token', '_method', 'submit_type'];
    // protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope('siteentry', function(Builder $builder){
        //     $builder->where('synched', '!=', 3);
        // });
    }

    public function carbon_date($date_column)
    {
        return \Carbon\Carbon::create($this->$date_column);
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

    public function getBarcodeAttribute()
    {
        $l = strlen($this->id);
        if($l < 5) return '00000' . $this->id;
        return $this->id;
    }

    public function getHyperlinkAttribute()
    {
        $user = auth()->user();
        /*$c = get_class($this);
        // $c = strtolower($c);
        $c = str_replace_first('App\\', '', $c);
        $c = snake_case($c);*/

        $c = $this->route_name;

        $url = url($c . '/' . $this->id);
        // if(\Str::contains($c, 'sample')) $url = url($c . '/runs/' . $this->id);
        if(\Str::contains($c, 'worksheet')){
            if(in_array($this->status_id, [2, 3])) $url = url($c . '/approve/' . $this->id);
            else{
                $url = url($c . '/find/' . $this->id);
            }
        }

        if(\Str::contains($c, ['worksheet', 'sample']) && (!$user || ($user && $user->user_type_id == 5))) return $this->id;

        $text = $this->id;

        if(\Str::contains($c, 'patient')) $text = $this->patient;
        if(\Str::contains($c, 'patient') && \Str::contains($c, 'covid')) $text = $this->identifier;
        if(\Str::contains($c, 'sample')) $text = "View Runs";

        $full_link = "<a href='{$url}' target='_blank'> {$text} </a>";

        return $full_link;
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

    public function get_prop_name($coll, $attr, $attr2='name')
    {
        if(!$this->$attr) return '';
        foreach ($coll as $value) {
            if($value->id == $this->$attr) return $value->$attr2;
        }
        return '';
    }
    

    protected function date_modifier($value)
    {
    	if($value) return date('d-M-Y', strtotime($value));
    	return $value;
    }

    public function my_date_format($value, $format='d-M-Y')
    {
        if($this->$value) return date($format, strtotime($this->$value));
        return '';
    }

    public function my_time_format($value)
    {
        if($this->$value) return date('d-M-Y H:i:s', strtotime($this->$value));
        return '';
    }

    public function my_boolean_format($value)
    {
        if($this->$value) return "Yes";
        return 'No';
    }

    public function my_string_format($value, $default='0')
    {
        if($this->$value) return (string) $this->$value;
        return $default;
    }

    public function output_array($value)
    {
        return eval('return ' . $this->value . ';');
    }

    public function pre_update()
    {
        if($this->synched == 1 && $this->isDirty()) $this->synched = 2;
        $this->save();
    }

    public function pre_delete()
    {
        if($this->synched == 1){
            $this->synched = 3;
        }else{
            $this->delete();
        }
        
    }

    public function edarp()
    {
        if(!$this->synched){
            $this->synched = 5;
            $this->save();
        }
        else{
            $this->pre_update();
        }
    }

    public function getRouteNameAttribute()
    {
        $a = explode('\\', get_class($this));
        $c = end($a);
        $c =  snake_case($c);
        if($c == "cancer_worksheet")
            $c = "cancerworksheet";

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
    

    protected function getPreviousWeek()
    {
        $date = strtotime('-7 days', strtotime(date('Y-m-d')));
        return $this->getStartAndEndDate(date('W', $date),
                                date('Y', $date));
    }

    protected function getStartAndEndDate($week, $year) {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        $ret['week'] = date('W', strtotime($ret['week_start']));
        return (object)$ret;
    }

    public function getLastMonth($year, $month)
    {
        $constructed_date = $year . '-' . $month . '-01';
        return [
                'year' => date('Y', strtotime("-1 Month", strtotime($constructed_date))),
                'month' => date('m', strtotime("-1 Month", strtotime($constructed_date))),
            ];

    }
}
