<?php

namespace App;

class DrWorksheet extends BaseModel
{
    

    public function sample()
    {
        return $this->hasMany(DrSample::class, 'worksheet_id');
    }

    public function sample_view()
    {
        return $this->hasMany(DrSampleView::class, 'worksheet_id');
    }

    public function warning()
    {
        return $this->hasMany(DrWorksheetWarning::class, 'worksheet_id');
    }

    public function extraction_worksheet()
    {
        return $this->belongsTo(DrExtractionWorksheet::class, 'extraction_worksheet_id');
    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'createdby');
    }

    public function runner()
    {
        return $this->belongsTo(User::class, 'runby');
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

}
