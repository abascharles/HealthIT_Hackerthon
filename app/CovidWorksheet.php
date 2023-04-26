<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidWorksheet extends BaseModel
{

	protected $dates = ['datecut', 'datereviewed', 'datereviewed2', 'dateuploaded', 'datecancelled', 'daterun'];

    public function pool()
    {
        return $this->belongsTo('App\CovidPool', 'pool_id');
    }

    public function sample()
    {
    	return $this->hasMany('App\CovidSample', 'worksheet_id');
    }

    public function sample_view()
    {
        return $this->hasMany('App\CovidSampleView', 'worksheet_id');
    }

    public function runner()
    {
    	return $this->belongsTo(User::class, 'runby');
    }

    public function sorter()
    {
        return $this->belongsTo(User::class, 'sortedby');
    }

    public function bulker()
    {
        return $this->belongsTo(User::class, 'bulkedby');
    }

    public function quoter()
    {
        return $this->belongsTo(User::class, 'alliquotedby');
    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'createdby');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploadedby');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelledby');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewedby');
    }

    public function reviewer2()
    {
        return $this->belongsTo(User::class, 'reviewedby2');
    }

    public function kit_type()
    {
        return $this->belongsTo('App\CovidKitType', 'covid_kit_type_id');
    }

    

    public function getFailedAttribute()
    {
        if(!in_array($this->neg_control_result, [1,6]) || !in_array($this->pos_control_result, [2,6])) return true;
        return false;
    }

    public function getReversibleAttribute()
    {
        if(!in_array($this->status_id, [3,7]) || $this->daterun->lessThan(date('Y-m-d', strtotime('-2 days')))){
            return false;
        }
        if(!in_array(auth()->user()->id, [$this->reviewedby, $this->reviewedby2])) return false;

        return true;
    }

	public function other_samples($id = null){
		if(!$this->combined) return null;
		if($this->combined == 1){
			$class = Sample::class;
		}else{
			$class = Viralsample::class;			
		}
		$dateuploaded = $this->dateuploaded;
		$samples = $class::where(['worksheet_id' => $this->id])
			->when($this->status_id == 1, function($query){
				return $query->whereNull('datetested');
			})
			->when(in_array($this->status_id, [2,3]), function($query) use($dateuploaded){
				return $query->where('datemodified', $dateuploaded);
			})
			->when($id, function($query) use($id){
				return $query->where('id', $id);
			})
			->get();
		if($id) return $samples->first();
		return $samples;
	}

	public function update_other_samples($update_data){
		if(!$this->combined) return null;
		if($this->combined == 1){
			$class = Sample::class;
		}else{
			$class = Viralsample::class;			
		}
		$dateuploaded = $this->dateuploaded;
		$class::where(['worksheet_id' => $this->id])
			->when($this->status_id == 1, function($query){
				return $query->whereNull('datetested');
			})
			->when(in_array($this->status_id, [2,3]), function($query) use($dateuploaded){
				return $query->where('datemodified', $dateuploaded);
			})
			->update($update_data);
	}
}
