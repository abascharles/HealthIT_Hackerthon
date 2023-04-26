<?php

namespace App\OldModels;

use Illuminate\Database\Eloquent\Model;

class Cd4WorksheetView extends Model
{
    protected $connection = 'cd4';

    public $timestamps = false;
    
	protected $table = 'old_cd4_worksheets_view';
}
