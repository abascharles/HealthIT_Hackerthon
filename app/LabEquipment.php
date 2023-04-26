<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LabEquipment extends Model
{
    protected $table = "lab_equipment_mapping";

    protected $guarded = ['id'];

    public $timestamps = false;
}
