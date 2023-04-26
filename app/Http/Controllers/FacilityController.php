<?php

namespace App\Http\Controllers;

use App\Jobs\SyncFacilityByUpdateTime;
use App\Jobs\SyncFacilityUpdate;
use App\Jobs\SyncUpdateFacility;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Facility;
use App\Lookup;
use App\PartnerFacilityContact;
use App\ViewFacility;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$facilities = DB::table('facilitys')
                            ->select('facilitys.id','facilitys.facilitycode','facilitys.name as facility','districts.name as district', 'countys.name as county','facilitys.ftype','facilitys.telephone','facilitys.telephone2','facilitys.email','facilitys.contactperson','facilitys.PostalAddress','facilitys.contacttelephone','facilitys.contacttelephone2','facilitys.ContactEmail','partners.name as partner','facilitys.SMS_printer_phoneNo AS smsprinterphoneno','facilitys.G4Sbranchname','facilitys.G4Slocation')
                            ->join('districts', 'districts.id', '=', 'facilitys.district')
                            ->join('countys', 'countys.id', '=', 'districts.county')
                            ->join('partners', 'partners.id', '=', 'facilitys.partner')
                            ->where('facilitys.flag', '=', 1)
                            ->get();*/

        $facilities = ViewFacility::when((env('APP_LAB') == 7), function($query){ 
            return $query->with(['facility_user']);
        })->get();
        $table = '';
        $user = auth()->user();
        if($user->user_type_id == 5) abort(403);
        foreach ($facilities as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->subcounty.'</td>';
            $table .= '<td>'.$value->telephone.'</td>';
            $table .= '<td>'.$value->telephone2.'</td>';
            $table .= '<td>'.$value->email.'</td>';
            $table .= '<td>'.$value->covid_email.'</td>';
            $table .= '<td>'.$value->sms_printer_phoneno.'</td>';
            $table .= '<td>'.$value->contactperson.'</td>';
            $table .= '<td>'.$value->contacttelephone.'</td>';
            $table .= '<td>'.$value->contacttelephone2.'</td>';
            $table .= '<td>'.$value->ContactEmail.'</td>';
            $table .= '<td>'.$value->G4Sbranchname.'</td>';
            $table .= '<td><a href="'.route('facility.show',$value->id).'">View</a>|
                        <a href="'.route('facility.edit',$value->id).'">Edit</a>';
            if(env('APP_LAB') == 7 && $user->is_lab_user()) $table .= '|<a target="_blank" href="'.url('user/passwordReset/' . md5($value->facility_user->id)).'">Edit Login</a>';
            $table .= '</td>';
            $table .= '</tr>';
        }
        $columns = parent::_columnBuilder(['MFL Code','Facility Name','County','Sub-county','Facility Phone 1','Facility Phone 2','Facility Email', 'Covid Email', 'Facility SMS Printer','Contact Person Names','Contact Phone 1','Contact Phone 2','Contact Email','G4S Branch','Task']);
        
        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Facilities');
    }

    public function filled_contacts()
    {
        $facilities = ViewFacility::whereRaw("((email is not null and email!='') or (telephone is not null and telephone!='') or (telephone2 is not null and telephone2!=''))")->get();
        $table = '';
        foreach ($facilities as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->subcounty.'</td>';
            $table .= '<td>'.$value->telephone.'</td>';
            $table .= '<td>'.$value->telephone2.'</td>';
            $table .= '<td>'.$value->email.'</td>';
            $table .= '<td>'.$value->sms_printer_phoneno.'</td>';
            $table .= '<td>'.$value->contactperson.'</td>';
            $table .= '<td>'.$value->contacttelephone.'</td>';
            $table .= '<td>'.$value->contacttelephone2.'</td>';
            $table .= '<td>'.$value->ContactEmail.'</td>';
            $table .= '<td>'.$value->G4Sbranchname.'</td>';
            $table .= '<td><a href="'.route('facility.show',$value->id).'">View</a>|<a href="'.route('facility.edit',$value->id).'">Edit</a></td>';
            $table .= '</tr>';
        }
        $columns = parent::_columnBuilder(['MFL Code','Facility Name','County','Sub-county','Facility Phone 1','Facility Phone 2','Facility Email','Facility SMS Printer','Contact Person Names','Contact Phone 1','Contact Phone 2','Contact Email','G4S Branch','Task']);
        
        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Facilities With Contacts');
    }

    public function lab()
    {
        // $facilities = ViewFacility::join('batches', 'batches.facility_id', '=', 'view_facilitys.id')
        //                             ->join('viralbatches', 'viralbatches.facility_id', '=', 'view_facilitys.id')
        //                             ->selectRaw("distinct view_facilitys.id, view_facilitys.name, view_facilitys.facilitycode, view_facilitys.county, view_facilitys.subcounty, view_facilitys.email, view_facilitys.telephone, view_facilitys.telephone2")->get();


        $facilities = ViewFacility::selectRaw("view_facilitys.*, count(batches.id) as eid_batches, count(viralbatches.id) as vl_batches ")
                                    ->leftJoin('batches', 'batches.facility_id', '=', 'view_facilitys.id')
                                    ->leftJoin('viralbatches', 'viralbatches.facility_id', '=', 'view_facilitys.id')
                                    ->groupBy('view_facilitys.id')
                                    ->havingRaw("eid_batches > 0 or vl_batches > 0 ")
                                    ->get();
        $table = '';
        foreach ($facilities as $key => $facility) {
            if ((!isset($facility->email) || $facility->email == '') || (!isset($facility->telephone) || $facility->telephone == '') || (!isset($facility->telephone2) || $facility->telephone2 == '')){
                $contact = "<span class='label label-danger'>Unavailable</span>";
            } else {
                $contact = "<span class='label label-success'>Contact</span>";
            }
            $table .= '<tr>';
            $table .= '<td>'.$facility->facilitycode.'</td>';
            $table .= '<td>'.$facility->name.'</td>';
            $table .= '<td>'.$facility->county.'</td>';
            $table .= '<td>'.$facility->subcounty.'</td>';
            $table .= '<td>'.$facility->email.'</td>';
            $table .= '<td>'.$facility->telephone.'</td>';
            $table .= '<td>'.$facility->telephone2.'</td>';
            $table .= '<td>'.$contact.'</td>';
            $table .= '<td><a href="'.route('facility.show',$facility->id).'">View</a>|<a href="'.route('facility.edit',$facility->id).'">Edit</a></td>';
            $table .= '</tr>';
        }
        $columns = parent::_columnBuilder(['MFL Code','Facility Name', 'County', 'Sub-county', 'Facility Email', 'Facility Phone 1', 'Facility Phone 2', 'Contacts Available', 'Task']);
        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Facilites Sending Samples');
    }

    public function served()
    {
        /*$facilities = DB::table('facilitys')
                            ->select('facilitys.id','facilitys.facilitycode','facilitys.name as facility','districts.name as district', 'countys.name as county','ftype','telephone','telephone2','facilitys.email','facilitys.contactperson','facilitys.PostalAddress','facilitys.contacttelephone','facilitys.contacttelephone2','facilitys.ContactEmail','partners.name as partner','facilitys.SMS_printer_phoneNo AS smsprinterphoneno','facilitys.G4Sbranchname','facilitys.G4Slocation')
                            ->join('districts', 'districts.id', '=', 'facilitys.district')
                            ->join('countys', 'countys.id', '=', 'districts.county')
                            ->join('partners', 'partners.id', '=', 'facilitys.partner')
                            ->where('facilitys.flag', '=', 1)
                            ->where('facilitys.lab', '=', Auth()->user()->lab_id)
                            ->get();*/

        $facilities = ViewFacility::whereRaw("id in (SELECT DISTINCT facility_id FROM viralbatches WHERE site_entry != 2 AND year(datereceived) > {$min_year} AND lab_id = {$lab_id})")->get();
        $count = 0;
        $table = '';
        foreach ($facilities as $key => $value) {
            $count++;
            $table .= '<tr>';
            $table .= '<td>'.$count.'</td>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->subcounty.'</td>';
            $table .= '<td>'.$value->telephone.'</td>';
            $table .= '<td>'.$value->email.'</td>';
            $table .= '<td>'.$value->contactperson.'</td>';
            $table .= '<td>'.$value->contacttelephone.'</td>';
            $table .= '<td>'.$value->ContactEmail.'</td>';
            $table .= '<td>'.$value->partner.'</td>';
            $table .= '</tr>';
        }
        $columns = parent::_columnBuilder(['#','MFL Code', 'Facility Name', 'County', 'Sub-county', 'Mobile', 'Email Address', 
                    'Contact Person', 'CP Telephone', 'CP Email', 'Supporting Partner']);
        
        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Facilities Served');
    }

    public function smsprinters()
    {
        $facilities = DB::table('facilitys')
                            ->select('facilitys.id','facilitys.facilitycode','facilitys.name as facility','districts.name as district', 'countys.name as county','ftype','telephone','telephone2','facilitys.email','facilitys.contactperson','facilitys.PostalAddress','facilitys.contacttelephone','facilitys.contacttelephone2','facilitys.ContactEmail','partners.name as partner','facilitys.SMS_printer_phoneNo AS smsprinterphoneno','facilitys.serviceprovider')
                            ->join('districts', 'districts.id', '=', 'facilitys.district')
                            ->join('countys', 'countys.id', '=', 'districts.county')
                            ->join('partners', 'partners.id', '=', 'facilitys.partner')
                            ->where('facilitys.flag', '=', 1)
                            ->where('facilitys.lab', '=', Auth()->user()->lab_id)
                            ->where('facilitys.smsprinter', '<>', '')
                            ->get();
        
        $columns = parent::_columnBuilder(['#','MFL Code', 'Facility Name', 'County', 'Sub-county', 'Email Address', 
                    'Contact Person', 'CP Telephone', 'CP Email', 'Supporting Partner', 'SMS Printer No.', 'Service Provider']);
        
        $count = 0;
        $table = '';
        foreach ($facilities as $key => $value) {
            $count++;
            $table .= '<tr>';
            $table .= '<td>'.$count.'</td>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->facility.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->district.'</td>';
            $table .= '<td>'.$value->email.'</td>';
            $table .= '<td>'.$value->contactperson.'</td>';
            $table .= '<td>'.$value->contacttelephone.'</td>';
            $table .= '<td>'.$value->ContactEmail.'</td>';
            $table .= '<td>'.$value->partner.'</td>';
            $table .= '<td>'.$value->smsprinterphoneno.'</td>';
            $table .= '<td>'.$value->serviceprovider.'</td>';
            $table .= '</tr>';
        }
        
        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'With SMS Printers');
    }

    public function withoutemails()
    {
        $columns = parent::_columnBuilder(['Facility Code', 'Facility Name', 'Mobile No', 'Email Address', 'Contact Person', 'CP Telephone', 'CP Email']);
        
        /*$facilities = DB::table('facilitys')
                            ->select('facilitys.id','facilitys.facilitycode','facilitys.name as facility','districts.name as district', 'countys.name as county', 'partners.name as partner','ftype','telephone','telephone2','facilitys.email','facilitys.contactperson','facilitys.PostalAddress','facilitys.contacttelephone','facilitys.contacttelephone2','facilitys.ContactEmail','partners.name as partner','facilitys.SMS_printer_phoneNo AS smsprinterphoneno','facilitys.serviceprovider')
                            ->join('districts', 'districts.id', '=', 'facilitys.district')
                            ->join('countys', 'countys.id', '=', 'districts.county')
                            ->join('partners', 'partners.id', '=', 'facilitys.partner')
                            ->where('facilitys.flag', '=', 1)
                            ->where('facilitys.lab', '=', Auth()->user()->lab_id)
                            ->whereRaw("((facilitys.email = '' and facilitys.ContactEmail ='') or (facilitys.email = '' and facilitys.ContactEmail is null) or (facilitys.email is null and facilitys.ContactEmail ='') or ((facilitys.email is null and facilitys.ContactEmail is null)))")
                            ->get();*/

        $facilities = ViewFacility::whereRaw("((email = '' and ContactEmail ='') or (email = '' and ContactEmail is null) or (email is null and ContactEmail ='') or ((email is null and ContactEmail is null)))")->get();
        // dd($facilities);
        $table = '';
        foreach ($facilities as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->telephone.'</td>';
            $table .= '<td>
                            <input type="hidden" name="id[]" value="'.$value->id.'">
                            <input type="text" class="form-control m-b input-sm" size="20" name="email[]" value="'.$value->email.'">
                        </td>';
            $table .= '<td><input type="text" class="form-control m-b input-sm" size="20" name="contactperson[]" value="'.$value->contactperson.'"></td>';
            $table .= '<td><input type="text" class="form-control m-b input-sm" size="20" name="contacttelephone[]" value="'.$value->contacttelephone.'"></td>';
            $table .= '<td><input type="text" class="form-control m-b input-sm" size="20" name="ContactEmail[]" value="'.$value->ContactEmail.'"></td>';
            $table .= '</tr>';
        }
        return view('tables.editable', ['row' => $table, 'columns' => $columns, 'function' => 'update'])->with('pageTitle', 'Without Emails');
    }

    public function withoutG4S()
    {
        $columns = parent::_columnBuilder(['Facility Code', 'Facility Name', 'County', 'Sub-county', 'G4S Branch Name', 'G4S Branch Location']);
        
        /*$facilities = DB::table('facilitys')
                            ->select('facilitys.id','facilitys.facilitycode','facilitys.name as facility','districts.name as district', 'countys.name as county', 'facilitys.G4Sbranchname','facilitys.G4Slocation')
                            ->join('districts', 'districts.id', '=', 'facilitys.district')
                            ->join('countys', 'countys.id', '=', 'districts.county')
                            ->join('partners', 'partners.id', '=', 'facilitys.partner')
                            ->where('facilitys.flag', '=', 1)
                            ->where('G4Sbranchname', '=', '')
                            ->where('G4Slocation', '=', '')
                            ->get();*/

        $facilities = ViewFacility::where('G4Sbranchname', '')->where('G4Slocation', '')->get();
        $table = '';
        foreach ($facilities as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->district.'</td>';
            $table .= '<td>
                            <input type="hidden" name="id[]" value="'.$value->id.'">
                            <input type="text" class="form-control m-b input-sm" size="20" name="G4Sbranchname[]" value="'.$value->G4Sbranchname.'">
                        </td>';
            $table .= '<td><input type="text" class="form-control m-b input-sm" size="20" name="G4Slocation[]" value="'.$value->G4Slocation.'"></td>';
            $table .= '</tr>';
        }
        return view('tables.editable', ['row' => $table, 'columns' => $columns/*, 'function' => ''*/])->with('pageTitle', 'Without G4S');
    }

    public function partnercontacts()
    {
        $columns = parent::_columnBuilder(['Name', 'Email', 'Mobile No', 'County', 'Sub-County', 'Partner', 'Critical Results']);
        
        
        $partners = PartnerFacilityContact::get();
        if ($partners->isEmpty()) {
            $table = '<tr><th colspan="7"><center>No Contact Available</center></th></tr>';
        }
        
        $table = '';
        foreach ($partners as $key => $partner) {
            $county_label = $partner->county->name ?? '<strong>N/A</strong>';
            $subcounty_label = $partner->subcounty->name ?? '<strong>N/A</strong>';
            $partner_label = $partner->partner->name ?? '<strong>N/A</strong>';
            $critical_results_label = "<label class'label label-danger'>Not Allowed</label>";
            if ($partner->critical_results)
                $critical_results_label = "<label class'label label-success'>Allowed</label>";
            $table .= '<tr>';
            $table .= '<td>'.$partner->name ?? '<strong>N/A</strong>' .'</td>';
            $table .= '<td>'.$partner->email ?? '<strong>N/A</strong>' .'</td>';
            $table .= '<td>'.$partner->telephone ?? '<strong>N/A</strong>' .'</td>';
            $table .= '<td>'.$county_label.'</td>';
            $table .= '<td>'.$subcounty_label.'</td>';
            $table .= '<td>'.$partner_label.'</td>';
            $table .= '<td>'.$critical_results_label.'</td>';
            $table .= '</tr>';
        }
        return view('tables.editable', ['row' => $table, 'columns' => $columns, 'function' => 'update'])->with('pageTitle', 'Partner Facility Contacts');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->auth_user([2]);
        $facilitytype = DB::table('facilitytype')->get();
        $districts = DB::table('districts')->get();
        $wards = DB::table('wards')->get();
        $partners = DB::table('partners')->get();
        $data = (object)[
                'facilitytype' => $facilitytype,
                'districts' => $districts,
                'wards' => $wards,
                'partners' => $partners
            ];
        
        return view('forms.facility', compact('data'))->with('pageTitle', 'Add Facility');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->auth_user([2]);
        $facility = new Facility();
        $facility->fill($request->except(['_token', 'submit_type']));
        $fac = Facility::where(['facilitycode' => $facility->facilitycode])->first();
        if($fac){
            session(['toast_error' => 1, 'toast_message'=>'The facility that you are trying to create already exists.']);
            return back();            
        }
        foreach ($facility->toArray() as $key => $value) {
            if(!$value) unset($facility->$key);
        }
        $facility->save();

        session(['toast_message'=>'Facility Created Successfully']);
        return back();
    }

    public function getFacility($id)
    {
        // return DB::table('facilitys')->select('facilitys.id','facilitys.facilitycode', 'facilitys.name as facility', 'districts.name as subcounty', 'countys.name as county', 'labs.name as lab','facilitys.physicaladdress', 'facilitys.PostalAddress','facilitys.telephone', 'facilitys.telephone2', 'facilitys.fax','facilitys.email', 'facilitys.contactperson', 'facilitys.ContactEmail', 'facilitys.contacttelephone', 'facilitys.contacttelephone2','facilitys.smsprinterphoneno', 'facilitys.G4Sbranchname', 'facilitys.G4Slocation', 'facilitys.G4Sphone1', 'facilitys.G4Sphone2', 'facilitys.G4Sphone3', 'facilitys.G4Sfax')
        //                 ->join('labs' ,'labs.id', '=', 'facilitys.lab')
        //                 ->join('districts', 'districts.id', '=', 'facilitys.district')
        //                 ->join('view_facilitys', 'view_facilitys.id', '=', 'facilitys.id')
        //                 ->join('countys', 'countys.id', '=', 'view_facilitys.county')
        //                 ->where('facilitys.id', '=', $id)
        //                 ->get();

        return DB::table('view_facilitys')->select('*', 'name as facility')->where('id', $id)->get();              
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $facility = Facility::find($id);
        // dd($facility);
        $facility = $this->getFacility($id);
        // dd($facility);
        return view('facilities.facility', ['facility' => $facility[0], 'disabled' => 'disabled'])->with('pageTitle', 'Facilities');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->user_type_id == 5 && auth()->user()->facility_id != $id) abort(403);
        $facility = $this->getFacility($id);
        
        return view('facilities.facility', ['facility' => $facility[0], 'disabled' => ''])
                        ->with('edit', true)
                        ->with('pageTitle', 'Facilities');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facility $facility)
    {
        $success = 'Update was successful';
        $failed = 'Updated failed try again later';

        $data = $request->except(['_token', 'id', '_method']);
        if(auth()->user()->user_type_id == 5)
            $data = $request->except(['_token', 'id', '_method', 'name', 'facilitycode']);
        $facility->fill($data);
        if($facility->covid_email) $facility->covid_email = str_replace(' ', '', $facility->covid_email);
        $facility->pre_update();
        session(['toast_message' => 'The update has been made.']);
        if(auth()->user()->user_type_id == 5) return redirect('sample/create');
        return redirect()->route('facility.index')->with('success', $success);

        // $this->validate($request, [
        //     'facilitycode' => 'required',
        //     'name' => 'required',
        //     'subconty' => 'required',
        //     'county' => 'required',
        //     'lab' => 'required',
        // ]);
        // dd($request->contacttelephone[199]);
        // if (gettype($id == "array") {//From the bulk update views
        //     if (isset($request->G4Sbranchname)||isset($request->G4Slocation)) {// update the G4S details
        //         foreach ($id as $key => $value) {
        //             $data = ['G4Sbranchname' => $request->G4Sbranchname[$key],'G4Slocation' => $request->G4Slocation[$key]];


        // if (gettype($id) == "array") {//From the bulk update views
        //     if (isset($request->G4Sbranchname)||isset($request->G4Slocation)) {// update the G4S details
        //         foreach ($id as $key => $value) {
        //             $data = ['G4Sbranchname' => $request->G4Sbranchname[$key],'G4Slocation' => $request->G4Slocation[$key]];

        //             $update = DB::table('facilitys')
        //                 ->where('id', $request->id[$key])
        //                 ->update($data);
        //         }
        //         if ($update) {
        //             return redirect()->route('withoutG4S')
        //                         ->with('success', $success);
        //         } else {
        //             return redirect()->route('withoutG4S')
        //                         ->with('failed', $failed);
        //         }
        //     } else { //Updating the facilities contact details
        //         foreach ($request->id as $key => $value) {
        //             $data = ['email' => $request->email[$key],'contactperson' => $request->contactperson[$key],
        //                         'contacttelephone' => $request->contacttelephone[$key],'ContactEmail' => $request->ContactEmail[$key]];
        //             $update = DB::table('facilitys')
        //                 ->where('id', $request->id[$key])
        //                 ->update($data);
        //         }
        //         if ($update) {
        //             return redirect()->route('withoutemails')
        //                         ->with('success', $success);
        //         } else {
        //             return redirect()->route('withoutemails')
        //                         ->with('failed', $failed);
        //         }
        //     }
        // } else {//From the single row update views
        //     // $data = ['facilitycode' => $request->facilitycode, 'name' => $request->name,
        //     //     'PostalAddress' => $request->PostalAddress, 'physicaladdress' => $request->physicaladdress,
        //     //     'telephone' => $request->telephone, 'fax' => $request->fax,
        //     //     'telephone2' => $request->telephone2, 'email' => $request->email,
        //     //     'smsprinterphoneno' => $request->smsprinterphoneno, 'contactperson' => $request->contactperson,
        //     //     'contacttelephone' => $request->contacttelephone, 'ContactEmail' => $request->ContactEmail,
        //     //     'contacttelephone2' => $request->contacttelephone2, 'G4Sbranchname' => $request->G4Sbranchname,
        //     //     'G4Sphone1' => $request->G4Sphone1, 'G4Sphone3' => $request->G4Sphone3,
        //     //     'G4Slocation' => $request->G4Slocation, 'G4Sphone2' => $request->G4Sphone2,'G4Sfax' => $request->G4Sfax];
        //     $data = $request->except(['_token', 'id', '_method']);
        //     $fac = \App\Facility::find($id);
        //     $fac->fill($data);
        //     $fac->save();
        //     // $update = DB::table('facilitys')
        //     //         ->where('id', $id)
        //     //         ->update($data);
        //     // if ($update) {
        //         return redirect()->route('facility.index')
        //                     ->with('success', $success);
        //     // } else {
        //     //     return redirect()->route('facility.index')
        //     //                 ->with('failed', $failed);
        //     // }
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {
        $div_id = $request->input('div_id');
        $search = $request->input('search');
        $search = addslashes($search);

        $poc = false;
        if($div_id == "#lab_id") $poc = true;

        $user = auth()->user();
        
        $facilities = \App\ViewFacility::select('id', 'name', 'facilitycode', 'county')
            ->whereRaw("(name like '%" . $search . "%' OR  facilitycode like '" . $search . "%')")
            ->when($poc, function($query){
                return $query->where(['poc' => 1]);
            })->when(($user && $user->is_partner), function($query) use ($user){
                return $query->where(['partner_id' => $user->facility_id]);
            })->paginate(10);

        $facilities->setPath(url()->current());
        return $facilities;
    }
           /*Controller to update and create facility from KMHFL*/
    public function integration_update()
    {
       //SyncFacilityByUpdateTime::dispatch();
       // SyncFacilityUpdate::dispatch();
    }

              /*Controller to get Flagged facilities without code from KMHFL*/
    public function noCode()
    {
        $facilities = DB::table('facilitys')->where('facilitys.status', '=', '0')
            ->get();

        //$facilities = ViewFacility::when((env('APP_LAB') == 7), function($query){
        //  return $query->with(['facility_user']);
        //})->get();
        $table = '';
        $user = auth()->user();
        if($user->user_type_id == 5) abort(403);
        foreach ($facilities as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.$value->facilitycode.'</td>';
            $table .= '<td>'.$value->name.'</td>';
            //$table .= '<td>'.$value->county.'</td>';
            // $table .= '<td>'.$value->subcounty.'</td>';
            //$table .= '<td>'.$value->telephone.'</td>';
            //$table .= '<td>'.$value->telephone2.'</td>';
            //$table .= '<td>'.$value->email.'</td>';
            // $table .= '<td>'.$value->covid_email.'</td>';
            // $table .= '<td>'.$value->sms_printer_phoneno.'</td>';
            // $table .= '<td>'.$value->contactperson.'</td>';
            // $table .= '<td>'.$value->contacttelephone.'</td>';
            // $table .= '<td>'.$value->contacttelephone2.'</td>';
            // $table .= '<td>'.$value->ContactEmail.'</td>';
            // $table .= '<td>'.$value->G4Sbranchname.'</td>';
            $table .= '<td><a href="'.route('facility.show',$value->id).'">View</a>|
                        <a href="'.route('facility.edit',$value->id).'">Edit</a>';
            if(env('APP_LAB') == 7 && $user->is_lab_user()) $table .= '|<a target="_blank" href="'.url('user/passwordReset/' . md5($value->facility_user->id)).'">Edit Login</a>';
            $table .= '</td>';
            $table .= '</tr>';
        }
        $columns = parent::_columnBuilder(['MFL Code','Facility Name','Task']);

        return view('tables.facilities', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Facilities');
    }



}
