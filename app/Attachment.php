<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends BaseModel
{
    use SoftDeletes;


    public function email()
    {
        return $this->belongsTo('App\Email');
    }

    public function getPathAttribute()
    {
    	return storage_path('app/' . $this->attachment_path);
    }


    public function pre_delete()
    {
    	if(file_exists($this->path)) unlink($this->path);
    	$this->delete();
    }
}
