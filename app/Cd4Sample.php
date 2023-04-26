<?php

namespace App;

use App\BaseModel;

class Cd4Sample extends BaseModel
{
	protected $table = 'cd4samples';

	public function first_approver(){
		return $this->belongsTo(User::class, 'approvedby');
	}

	public function second_approver(){
		return $this->belongsTo(User::class, 'approvedby2');
	}

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function patient(){
    	return $this->belongsTo('App\Cd4Patient', 'patient_id');
    }



    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\Cd4Sample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\Cd4Sample', 'parentid');
    }

    
    public function remove_rerun()
    {
        if($this->parentid == 0) $this->remove_child();
        else{
            $this->remove_sibling();
        }
    }

    public function remove_child()
    {
        $children = $this->child;

        foreach ($children as $s) {
            $s->delete();
        }

        $this->repeatt=0;
        $this->save();
    }

    public function remove_sibling()
    {
        $parent = $this->parent;
        $children = $parent->child;

        foreach ($children as $s) {
            if($s->run > $this->run) $s->delete();            
        }

        $this->repeatt=0;
        $this->save();
    }
}
