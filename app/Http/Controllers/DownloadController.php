<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{


    public function help_desk(){
        $path = public_path('downloads/help_desk_sop.pdf');
        return response()->download($path, 'Support Helpdesk How To Guide.pdf');
    }

    public function covid_sop(){
        $path = public_path('downloads/quarantine_site_sop.pdf');
        return response()->download($path, 'COVID-19 Quarantine Sites Remote Log In Job Aid.pdf');
    }

    public function covid(){
        // $path = public_path('downloads/COVID-19_LRF_RB.pdf');
        $path = public_path('downloads/Kenya-COVID19_CIF_v5.docx');
        return response()->download($path, 'Covid-19 LRF.docx');
    }

	public function user_guide(){
    	$path = public_path('downloads/PartnerLoginUserGuide.pdf');
    	return response()->download($path);
    }

	public function consumption(){
    	$path = public_path('downloads/CONSUMPTION_GUIDE.pdf');
    	return response()->download($path);
    }

	public function hei(){
    	$path = public_path('downloads/HEIValidationToolGuide.pdf');
    	return response()->download($path);
    }

	public function poc(){
    	$path = public_path('downloads/POC_USERGUIDE.pdf');
    	return response()->download($path);
    }


	public function eid_req(){
    	$path = public_path('downloads/EID_REQUISITION_FORM.pdf');
    	return response()->download($path);
    }

	public function vl_req(){
    	$path = public_path('downloads/VL_REQUISITION_FORM.pdf');
    	return response()->download($path);
    }

    public function collection_guidelines(){
        $path = public_path('downloads/collection_manual.pdf');
        return response()->download($path, 'KEMRI Nairobi HIV Lab sample collection manual 2019.pdf');
    }

    public function api(){
        $path = public_path('downloads/Lab.postman_collection.json');
        return response()->download($path);
    }

    public function hit_api(){
        $path = public_path('downloads/HIT.postman_collection.json');
        return response()->download($path);
    }

    public function remotelogin() {
        $path = public_path('downloads/NASCOP_Remote_Login_SOP.pdf');
        return response()->download($path, 'NASCOP Lab Remote Login SOP.pdf');
    }


}
