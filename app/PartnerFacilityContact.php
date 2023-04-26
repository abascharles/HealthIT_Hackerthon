<?php

namespace App;

use App\BaseModel;
use App\Imports\PartnerFacilityContactsImport;
use \DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Facades\Excel;

class PartnerFacilityContact extends BaseModel
{
    use SoftDeletes;

    public function getCountyAttribute()
    {
        $county = NULL;
        if (null !== $this->county_id) {
            $db_county = DB::table('countys')->where('id', $this->county_id)->get();
            if (!$db_county->isEmpty())
                $county = $db_county->first();
        }
        return $county;
    }

    public function getSubcountyAttribute()
    {
        $sub_county = NULL;
        if (null !== $this->subcounty_id) {
            $db_subcounty = DB::table('districts')->where('id', $this->subcounty_id)->get();
            if (!$db_subcounty->isEmpty())
                $sub_county = $db_subcounty->first();
        }
        return $sub_county;
    }

    public function getPartnerAttribute()
    {
        $partner = NULL;
        if (null !== $this->partner_id) {
            $db_partner = DB::table('partners')->where('id', $this->partner_id)->get();
            if (!$db_partner->isEmpty())
                $partner = $db_partner->first();
        }
            
        return $partner;
    }

    public static function import()
    {
        Excel::import(new PartnerFacilityContactsImport(), public_path('docs/Partner Facility Contacts.xlsx'));
    }
}
// \App\PartnerFacilityContact::import();