<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UlizaTwg extends BaseModel
{

    public function county()
    {
        return $this->hasMany('App\County', 'twg_id');
    }

    public function clinical_form()
    {
        return $this->hasMany('App\UlizaClinicalForm', 'twg_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'twg_id');
    }

    public function getEmailArrayAttribute()
    {
        $admin_emails = User::where(['user_type_id' => 102, 'receive_emails' => true])->get()->pluck('email')->toArray();
        $secretariats = $this->user()->where(['user_type_id' => 103, 'receive_emails' => true])->get()->pluck('email')->toArray();
        return array_merge($admin_emails, $secretariats, ['joelkith@gmail.com']);
    }
}
