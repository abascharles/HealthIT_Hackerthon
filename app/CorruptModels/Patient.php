<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class Patient extends BaseModel
{
    public function corrupt_version()
    {
        $mother = Mother::where('old_id', '=', $this->mother_id)->first();
        if (isset($mother))
        	$this->mother_id = $mother->id;
        $this->save();
    }
}
