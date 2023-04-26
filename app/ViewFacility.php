<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewFacility extends Model
{
    //
    protected $table = "view_facilitys";

    public function facility_user()
    {
        return $this->hasOne(User::class, 'facility_id');
    }


    public function getFacilityPartnersAttribute()
    {
        $partners = [];
        $county_partners = PartnerFacilityContact::where('county_id', $this->county_id)->where('critical_results', 1)->get();
        foreach($county_partners as $partner) {
            $partners[] = $partner;
        }

        $subcounty_partners = PartnerFacilityContact::where('subcounty_id', $this->subcounty_id)->where('critical_results', 1)->get();
        foreach($subcounty_partners as $partner) {
            $partners[] = $partner;
        }

        $partner_partners = PartnerFacilityContact::where('partner_id', $this->partner_id)->where('critical_results', 1)->get();
        foreach($partner_partners as $partner) {
            $partners[] = $partner;
        }
        return collect($partners);
    }
}
