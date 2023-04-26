<?php

namespace App;

use Carbon\Carbon;
use DB;
use Exception;


use App\OldModels\Cd4SampleView;
use App\OldModels\Cd4WorksheetView;

use App\Cd4Worksheet;
use App\Cd4Patient;
use App\Cd4Sample;


use App\Facility;


use App\OldModels\SampleView;
use App\OldModels\ViralsampleView;
use App\OldModels\FormerViralsampleView;

use App\OldModels\WorksheetView;
use App\OldModels\ViralworksheetView;
use App\OldModels\PocWorklist;

use App\Mother;
use App\Worksheet;
use App\Patient;
use App\Batch;
use App\Sample;

use App\Worklist;
use App\Viralworksheet;
use App\Viralpatient;
use App\Viralbatch;
use App\Viralsample;

use App\Lookup;
use App\Common;
use App\Misc;
use App\MiscViral;

class Copier
{
    private static $limit = 5000;

    public static function missing_facilities()
    {
        ini_set("memory_limit", "-1");
        $samples = SampleView::where('facility_id', 4575)->get();

        foreach ($samples as $key => $sample) {
            $s = Sample::find($sample->id);
            $batch = $s->batch;
            $batch->facility_id = 55073;
            $batch->pre_update();

            $patient = $s->patient;
            $patient->facility_id = 55073;
            $patient->pre_update();

            $mother = $patient->mother;
            $mother->facility_id = 55073;
            $mother->pre_update();
        }

        $viralsamples = ViralsampleView::where('facility_id', 4575)->get();

        foreach ($viralsamples as $key => $sample) {
            $s = Viralsample::find($sample->id);
            $batch = $s->batch;
            $batch->facility_id = 55073;
            $batch->pre_update();

            $patient = $s->patient;
            $patient->facility_id = 55073;
            $patient->pre_update();
        }
    }


    public static function copy_missing_facilities()
    {
        $db_name = env('DB_DATABASE');
        $facilities = DB::connection('old')->table('eid_kemri2.facilitys')->whereRaw("facilitycode not IN (select facilitycode from {$db_name}.facilitys)")->get();

        $classes = [
            \App\Mother::class,
            \App\Batch::class,
            \App\Patient::class,


            \App\Viralbatch::class,
            \App\Viralpatient::class,
        ];

        foreach ($facilities as $key => $value) {
            $fac = Facility::find($value->ID);
            if($fac){
                $facility = new Facility;
                $facility->fill(get_object_vars($value));
                $facility->synched=0;
                unset($facility->ID);
                unset($facility->wardid);
                unset($facility->districtname);
                unset($facility->ANC);
                unset($facility->partnerregion);
                unset($facility->pasco);
                unset($facility->zuia);
                unset($facility->negpilot);
                unset($facility->{'sent2'});
                unset($facility->sent2);
                unset($facility->sentmail);
                unset($facility->{'Column 33'});
                $facility->save();
                continue;

                foreach ($classes as $class) {
                    $class::where(['facility_id' => $value->ID, 'synched' => 1])->update(['facility_id' => $facility->id, 'synched' => 2]);
                    $class::where(['facility_id' => $value->ID])->update(['facility_id' => $facility->id]);
                }

                if(env('APP_LAB') == 5) \App\Cd4Sample::where(['facility_id' => $value->ID])->update(['facility_id' => $facility->id]);
            }
            else{
                $facility = new Facility;
                $facility->fill(get_object_vars($value));
                $facility->id = $value->ID;
                $facility->synched=0;
                unset($facility->ID);
                unset($facility->wardid);
                unset($facility->districtname);
                unset($facility->partnerregion);
                unset($facility->pasco);
                unset($facility->zuia);
                unset($facility->negpilot);
                unset($facility->ANC);
                unset($facility->sent2);
                unset($facility->sentmail);
                unset($facility->{'Column 33'});
                $facility->save();
            }
        }
    }

    public static function split_batches()
    {
        $fields = self::viralsamples_arrays();  

        $batches = ViralsampleView::selectRaw("original_batch_id, count(distinct facility_id) as facility_count")
                                    ->groupBy('original_batch_id')
                                    ->where("original_batch_id", '!=', 0)
                                    ->having('facility_count', '>', 1)
                                    ->get();

        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];

        foreach ($batches as $b) {
            $batch = Viralbatch::find(self::set_batch_id($b->original_batch_id));

            $samples = ViralsampleView::where('original_batch_id', $b->original_batch_id)->get();

            foreach ($samples as $s) {
                $fac = \App\OldModels\Facility::find($s->facility_id);
                $f = null;
                if($fac) $f = \App\Facility::locate($fac->facilitycode)->first();

                // $cur_sample = \App\ViralsampleView::find($s->id);

                $facility_id = $f->id ?? $s->facility_id;

                if($batch->facility_id == $facility_id) continue;

                $new_batch = Viralbatch::where(['facility_id' => $facility_id, 'id' => $b->original_batch_id])->first();

                if(!$new_batch){

                    $new_batch = new Viralbatch;
                    $new_batch->fill($s->only($fields['batch']));

                    foreach ($batch_date_array as $date_field) {
                        $new_batch->$date_field = self::clean_date($s->$date_field);
                        if($new_batch->$date_field == '1970-01-01') $new_batch->$date_field = null;
                    }

                    $new_batch->synched = 0;
                    $new_batch->facility_id = $facility_id;
                    $new_batch->save();
                }

                $current_sample = Viralsample::find($s->id);
                $current_sample->batch_id = $new_batch->id;
                $current_sample->save();

                $patient = $current_sample->patient;
                $patient->facility_id = $facility_id;
                $patient->pre_update();
            }
        }
    }

    public static function split_eid_batches()
    {
        $fields = self::samples_arrays();  

        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];

        $batches = SampleView::selectRaw("original_batch_id, count(distinct facility_id) as facility_count")
                                    ->groupBy('original_batch_id')
                                    ->where("original_batch_id", '!=', 0)
                                    ->having('facility_count', '>', 1)
                                    ->get();

        foreach ($batches as $b) {
            $batch = Batch::find(self::set_batch_id($b->original_batch_id));

            $samples = SampleView::where('original_batch_id', $b->original_batch_id)->get();

            foreach ($samples as $s) {
                $fac = \App\OldModels\Facility::find($s->facility_id);
                $f = null;
                if($fac) $f = \App\Facility::locate($fac->facilitycode)->first();

                // $cur_sample = \App\ViralsampleView::find($s->id);

                $facility_id = $f->id ?? $s->facility_id;

                if($batch && $batch->facility_id == $facility_id) continue;

                $new_batch = Batch::where(['facility_id' => $facility_id, 'id' => $b->original_batch_id])->first();

                if(!$new_batch){

                    $new_batch = new Batch;
                    $new_batch->fill($s->only($fields['batch']));

                    foreach ($batch_date_array as $date_field) {
                        $new_batch->$date_field = self::clean_date($s->$date_field);
                        if($new_batch->$date_field == '1970-01-01') $new_batch->$date_field = null;
                    }
                    
                    $new_batch->synched = 0;
                    $new_batch->facility_id = $facility_id;
                    $new_batch->save();
                }

                $current_sample = Sample::find($s->id);
                $current_sample->batch_id = $new_batch->id;
                $current_sample->save();

                $patient = $current_sample->patient;
                $patient->facility_id = $facility_id;
                $patient->pre_update();

                $mother = $patient->mother;
                $mother->facility_id = $facility_id;
                $mother->pre_update();
            }
        }
    }


    public static function copy_areaname()
    {
        ini_set("memory_limit", "-1");
        $samples = Viralsample::get();

        foreach ($samples as $sample) {
            $s = ViralsampleView::find($sample->id);
            if(!$s) continue;

            $sample->areaname = $s->areaname;
            $sample->label_id = $s->label_id;
            $sample->save();
        }
    }

    public static function copy_eid()
    {
        $start = Sample::max('id');
        ini_set("memory_limit", "-1");
        $fields = self::samples_arrays(); 
        $sample_date_array = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'created_at'];
        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];
        $offset_value = 0;
        $new_batch_id = SampleView::selectRaw("max(original_batch_id) as max_id")->first()->max_id;
        while(true)
        {
            $samples = SampleView::when($start, function($query) use ($start){
                return $query->where('id', '>', $start);
            })->limit(self::$limit)->offset($offset_value)->get();
            if($samples->isEmpty()) break;

            foreach ($samples as $key => $value) {
                $s = \App\Sample::find($value->id);
                if($s) continue;

                $patient = Patient::existing($value->facility_id, $value->patient)->get()->first();

                if(!$patient){
                    $mother = new Mother($value->only($fields['mother']));
                    $mother->save();
                    $patient = new Patient($value->only($fields['patient']));
                    $patient->mother_id = $mother->id;
                    if($patient->dob) $patient->dob = self::clean_date($patient->dob);

                    if(!$patient->dob) $patient->dob = self::previous_dob(SampleView::class, $value->patient, $value->facility_id);

                    if(!$patient->dob){
                        $patient->dob = self::calculate_dob($value->datecollected, 0, $value->age, SampleView::class, $value->patient, $value->facility_id);
                    }
                    $patient->sex = self::resolve_gender($value->gender, SampleView::class, $value->patient, $value->facility_id);
                    $patient->ccc_no = $value->enrollment_ccc_no;
                    $patient->save();
                }

                $value->original_batch_id = self::set_batch_id($value->original_batch_id);
                $batch = null;
                if($value->original_batch_id != 0) $batch = Batch::find($value->original_batch_id);


                if(!$batch){
                    $batch = new Batch($value->only($fields['batch']));
                    foreach ($batch_date_array as $date_field) {
                        $batch->$date_field = self::clean_date($value->$date_field);
                        if($batch->$date_field == '1970-01-01') $batch->$date_field = null;
                    }
                    $batch->id = $value->original_batch_id;
                    if($value->original_batch_id == 0) $batch->id = ++$new_batch_id;
                    if($batch->site_entry == 0 && !$batch->received_by) $batch->received_by = $value->user_id;
                    $batch->entered_by = $value->user_id;
                    $batch->save();
                }

                $sample = new Sample($value->only($fields['sample']));
                foreach ($sample_date_array as $date_field) {
                    $sample->$date_field = self::clean_date($value->$date_field);
                    if($sample->$date_field == '1970-01-01') $sample->$date_field = null;
                }
                if($value->original_batch_id != 0) $sample->batch_id = $value->original_batch_id ?? $new_batch_id;
                else{
                    $sample->batch_id = $new_batch_id;
                }
                $sample->patient_id = $patient->id;

                if(!$sample->age && $batch->datecollected && $patient->dob){
                    $sample->age = Lookup::calculate_age($batch->datecollected, $patient->dob);
                }
                if($sample->worksheet_id == 0) $sample->worksheet_id = null;
                if($sample->receivedstatus == 0) $sample->receivedstatus = null;
                if($sample->result == '') $sample->result = null;
                if(!$sample->eqa) $sample->eqa = 0;

                $sample->save();
            }
            $offset_value += self::$limit;
            echo "Completed eid {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
        }

        $my = new Misc;
        $my->compute_tat(\App\SampleView::class, Sample::class);
        echo "Completed eid clean at " . date('d/m/Y h:i:s a', time()). "\n";
    }


    public static function copy_vl()
    {
        $start = Viralsample::max('id');
        ini_set("memory_limit", "-1");
        $fields = self::viralsamples_arrays();  
        $sample_date_array = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'created_at'];
        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];
        $offset_value = 0;
        $sample_class = ViralsampleView::class;
        $new_batch_id = ViralsampleView::selectRaw("max(original_batch_id) as max_id")->first()->max_id;

        while(true)
        {
            // If the samples table already has values, then the former table has already been copied
            if(!$start) $sample_class = ViralsampleView::class;
            $samples = $sample_class::when($start, function($query) use ($start){
                return $query->where('id', '>', $start);
            })->limit(self::$limit)->offset($offset_value)->get();

            
            if($samples->isEmpty()){
                // If the samples table has been copied, exit the loop
                if($sample_class == 'App\OldModels\ViralsampleView') break;
                // Else, has finished copying the former table
                // Switch to the one in use and commence copying
                $sample_class = ViralsampleView::class;
                continue;
            }

            foreach ($samples as $key => $value) {
                if(!$value->patient) continue;
                $patient = Viralpatient::existing($value->facility_id, $value->patient)->get()->first();

                if(!$patient){
                    $patient = new Viralpatient($value->only($fields['patient']));
                    // if(!$patient->patient) $patient->patient = '';
                    if($patient->dob) $patient->dob = self::clean_date($patient->dob);

                    if(!$patient->dob) $patient->dob = self::previous_dob(ViralsampleView::class, $value->patient, $value->facility_id);
                    
                    if(!$patient->dob){
                        $patient->dob = self::calculate_dob($value->datecollected, $value->age, 0, ViralsampleView::class, $value->patient, $value->facility_id);
                    }
                    $patient->sex = self::resolve_gender($value->gender, ViralsampleView::class, $value->patient, $value->facility_id);
                    $patient->initiation_date = self::clean_date($patient->initiation_date);
                    $patient->save();
                }

                $value->original_batch_id = self::set_batch_id($value->original_batch_id);
                $batch = null;
                if($value->original_batch_id != 0) $batch = Viralbatch::find($value->original_batch_id);

                if(!$batch){
                    $batch = new Viralbatch($value->only($fields['batch']));
                    foreach ($batch_date_array as $date_field) {
                        $batch->$date_field = self::clean_date($value->$date_field);
                        if($batch->$date_field == '1970-01-01') $batch->$date_field = null;
                    }
                    $batch->id = $value->original_batch_id;
                    if($value->original_batch_id == 0) $batch->id = ++$new_batch_id;
                    if($batch->site_entry == 0 && !$batch->received_by) $batch->received_by = $value->user_id;
                    $batch->entered_by = $value->user_id;
                    $batch->save();
                }

                $sample = new Viralsample($value->only($fields['sample']));
                foreach ($sample_date_array as $date_field) {
                    $sample->$date_field = self::clean_date($value->$date_field);
                    if($sample->$date_field == '1970-01-01') $sample->$date_field = null;
                }
                if($value->original_batch_id != 0) $sample->batch_id = $value->original_batch_id;
                else{
                    $sample->batch_id = $new_batch_id;
                }
                $sample->patient_id = $patient->id;

                if(!$sample->age && $batch->datecollected && $patient->dob){
                    $sample->age = Lookup::calculate_viralage($batch->datecollected, $patient->dob);
                }
                if($sample->worksheet_id == 0) $sample->worksheet_id = null;
                if($sample->receivedstatus == 0) $sample->receivedstatus = null;
                if($sample->result == '') $sample->result = null;

                $sample->save();
            }
            $offset_value += self::$limit;
            echo "Completed vl {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
        }

        $my = new MiscViral;
        $my->compute_tat(\App\ViralsampleView::class, Viralsample::class);
        echo "Completed vl clean at " . date('d/m/Y h:i:s a', time()). "\n";
    }

    public static function cd4()
    {
        DB::statement("truncate table cd4worksheets");
        DB::statement("truncate table cd4patients");
        DB::statement("truncate table cd4samples");

        self::copy_cd4_worksheet();
        self::copy_cd4();
    }


    public static function copy_cd4()
    {
        $start = Cd4Sample::max('id');
        ini_set("memory_limit", "-1");
        $fields = self::cd4_arrays();  
        $sample_date_array = ['datecollected', 'datereceived', 'datedispatched', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'dateresultprinted', 'created_at'];
        $offset_value = 0;
        $sample_class = Cd4SampleView::class;

        while(true)
        {
            $samples = $sample_class::when($start, function($query) use ($start){
                return $query->where('id', '>', $start);
            })->limit(self::$limit)->offset($offset_value)->get();
            if($samples->isEmpty()) break;

            foreach ($samples as $key => $value) {

                $patient = Cd4Patient::find($value->original_patient_id);

                if(!$patient){

                    $patient = new Cd4Patient($value->only($fields['patient']));
                    if($patient->dob) $patient->dob = self::clean_date($patient->dob);
                    $patient->sex = self::resolve_gender($value->gender);
                    $patient->id = $value->original_patient_id;
                    $patient->save();

                }

                $sample = new Cd4Sample($value->only($fields['sample']));
                foreach ($sample_date_array as $date_field) {
                    $sample->$date_field = self::clean_date($value->$date_field);
                    // if($sample->$date_field == '1970-01-01') $sample->$date_field = null;
                }
                $sample->patient_id = $patient->id;

                if(!$sample->age && $sample->datecollected && $patient->dob){
                    $sample->age = Lookup::calculate_viralage($sample->datecollected, $patient->dob);
                }
                if($sample->worksheet_id == 0) $sample->worksheet_id = null;
                if($sample->receivedstatus == 0) $sample->receivedstatus = null;
                if($sample->result == '') $sample->result = null;

                if(!is_numeric($sample->user_id)) $sample->user_id = 0;

                $sample->save();
            }
            $offset_value += self::$limit;
            echo "Completed cd4 {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
        }

        // $my = new MiscViral;
        // $my->compute_tat(\App\ViralsampleView::class, Viralsample::class);
        // echo "Completed vl clean at " . date('d/m/Y h:i:s a', time()). "\n";
    }


    private static function set_batch_id($batch_id)
    {
        if($batch_id == floor($batch_id)) return $batch_id;
        return (floor($batch_id) + 0.5);
    }

    public static function copy_worksheet()
    {
        $work_array = [
            'eid' => ['model' => Worksheet::class, 'view' => WorksheetView::class],
            'vl' => ['model' => Viralworksheet::class, 'view' => ViralworksheetView::class],
        ];

        $date_array = ['kitexpirydate', 'sampleprepexpirydate', 'bulklysisexpirydate', 'controlexpirydate', 'calibratorexpirydate', 'amplificationexpirydate', 'datecut', 'datereviewed', 'datereviewed2', 'datecancelled', 'daterun', 'dateuploaded', 'created_at'];

        ini_set("memory_limit", "-1");

        foreach ($work_array as $key => $value) {
            $model = $value['model'];
            $view = $value['view'];

            $start = $model::max('id');              

            $offset_value = 0;
            while(true)
            {
                $worksheets = $view::when($start, function($query) use ($start){
                    return $query->where('id', '>', $start);
                })->limit(self::$limit)->offset($offset_value)->get();
                if($worksheets->isEmpty()) break;

                foreach ($worksheets as $worksheet_key => $worksheet) {
                    $duplicate = $worksheet->replicate();
                    $existing = $model::find($worksheet->id);
                    if($existing) continue;
                    $work = new $model;                    
                    $work->fill($duplicate->toArray());
                    foreach ($date_array as $date_field) {
                        $work->$date_field = self::clean_date($worksheet->$date_field);
                    }
                    $work->id = $worksheet->id;
                    $work->save();
                }
                $offset_value += self::$limit;
                echo "Completed {$key} worksheet {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
            }
        }
    }

    public static function copy_updated_worksheet()
    {
        $work_array = [
            'eid' => ['model' => Worksheet::class, 'view' => WorksheetView::class],
            'vl' => ['model' => Viralworksheet::class, 'view' => ViralworksheetView::class],
        ];

        $date_array = ['kitexpirydate', 'sampleprepexpirydate', 'bulklysisexpirydate', 'controlexpirydate', 'calibratorexpirydate', 'amplificationexpirydate', 'datecut', 'datereviewed', 'datereviewed2', 'datecancelled', 'daterun', 'dateuploaded', 'created_at'];

        ini_set("memory_limit", "-1");

        foreach ($work_array as $key => $value) {
            $model = $value['model'];
            $view = $value['view'];

            $start = $model::max('id');              

            $offset_value = 1000;
            while(true)
            {
                $worksheets = $view::limit(self::$limit)->offset($offset_value)->get();
                if($worksheets->isEmpty()) break;

                foreach ($worksheets as $worksheet_key => $worksheet) {
                    $duplicate = $worksheet->replicate();
                    $work = $model::find($worksheet->id);                    
                    $work->fill($duplicate->toArray());
                    foreach ($date_array as $date_field) {
                        $work->$date_field = self::clean_date($worksheet->$date_field);
                    }
                    // $work->id = $worksheet->id;
                    $work->save();
                }
                $offset_value += self::$limit;
                echo "Completed {$key} worksheet {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
            }
        }
    }

    public static function copy_cd4_worksheet()
    {
        $date_array = ['daterun', 'datereviewed', 'datereviewed2', 'datecancelled', 'dateuploaded', 'created_at'];

        ini_set("memory_limit", "-1");
        $start = Cd4Worksheet::max('id');
        $offset_value = 0;
        while(true)
        {
            $worksheets = Cd4WorksheetView::when($start, function($query) use ($start){
                return $query->where('id', '>', $start);
            })->limit(self::$limit)->offset($offset_value)->get();
            if($worksheets->isEmpty()) break;

            foreach ($worksheets as $worksheet_key => $worksheet) {
                $duplicate = $worksheet->replicate();
                $work = new Cd4Worksheet;                    
                $work->fill($duplicate->toArray());
                foreach ($date_array as $date_field) {
                    $work->$date_field = self::clean_date($worksheet->$date_field);
                }
                $work->id = $worksheet->id;
                $work->save();
            }
            $offset_value += self::$limit;
            echo "Completed cd4 worksheet {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
        }
    }

    public static function copy_worklist()
    {
        ini_set("memory_limit", "-1");
        $old_worklists = PocWorklist::all();

        foreach ($old_worklists as $key => $o) {
            $w = new Worklist;
            $w->id = $o->id;
            $w->created_at = $o->datecreated . " 00:00:00";
            $w->testtype = $o->testtype;
            $w->status_id = $o->status ?? null;
            $w->facility_id = $o->facility;
            $w->save();
        }
    }

    public static function copy_deliveries()
    {
        ini_set("memory_limit", "-1");
        $deliveries = self::deliveries();
        $unset_array = ['synchronized', 'datesynchronized', 'submitted', 'emailsent', 'lab', 'approve', 'testsdone', 'yearofrecordset', 'monthofrecordset', 'equipmentid', 'disposable1000received', 'disposable1000damaged', 'disposable200received', 'disposable200damaged', 'approved_date'];
            // 'allocatequalkit', 'allocatespexagent', 'allocateampinput', 'allocateampflapless', 'allocateampktips', 'allocateampwash', 'allocatektubes', 'allocateconsumables', 'allocatecalibration', 'allocatecontrol', 'allocatebuffer'

        foreach ($deliveries as $key => $value) {
            $offset_value = 0;
            $start = $value['class']::max('id'); 

            while(true)
            {
                $rows = DB::connection('old')->table($value['table'])
                ->when($start, function($query) use ($start){
                    return $query->where('id', '>', $start);
                })
                ->limit(self::$limit)->offset($offset_value)->get();
                if($rows->isEmpty()) break;

                foreach ($rows as $row) {
                    $del = new $value['class'];
                    $del->fill(get_object_vars($row));
                    foreach ($unset_array as $u) {
                        unset($del->$u);
                    }

                    foreach (get_object_vars($row) as $attr => $attr_val) {
                        if(\Str::startsWith($attr, 'allocate')) unset($del->$attr);
                        // if(\Str::startsWith($attr, 'allocate')) return $attr;
                    }
                    $del->lab_id = $row->lab;

                    foreach ($value['dates'] as $date_field) {
                        $del->$date_field = self::clean_date($del->$date_field);
                    }
                    if(isset($row->approve) && $row->approve == 'Y') $del->approve = 1;
                    if(isset($row->testsdone)) $del->tests = $row->testsdone;
                    if(isset($row->yearofrecordset)) $del->year = $row->yearofrecordset;
                    if(isset($row->monthofrecordset)) $del->month = $row->monthofrecordset;
                    if(isset($row->equipmentid)) $del->equipment_id = $row->equipmentid;
                    
                    if(isset($row->disposable1000received)) $del->{'1000disposablereceived'} = $row->disposable1000received;
                    if(isset($row->disposable1000damaged)) $del->{'1000disposabledamaged'} = $row->disposable1000damaged;
                    if(isset($row->disposable200received)) $del->{'200disposablereceived'} = $row->disposable200received;
                    if(isset($row->disposable200damaged)) $del->{'200disposabledamaged'} = $row->disposable200damaged;

                    $del->save();
                }
                $offset_value += self::$limit;
                echo "Completed {$key} {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
            }
        }
    }

    public static function copy_facility_contacts()
    {
        $contact_array = ['telephone', 'telephone2', 'fax', 'email', 'PostalAddress', 'contactperson', 'contacttelephone', 'contacttelephone2', 'physicaladdress', 'G4Sbranchname', 'G4Slocation', 'G4Sphone1', 'G4Sphone2', 'G4Sphone3', 'G4Sfax', 'ContactEmail'];
        $facilities = \App\Facility::all();
        foreach ($facilities as $key => $facility) {
            $old = \App\OldModels\Facility::find($facility->id);
            // $old = \App\OldModels\Facility::locate($facility->facilitycode)->get()->first();
            $contact = new \App\FacilityContact();
            if($old) $contact->fill($old->only($contact_array));
            $contact->facility_id = $facility->id;
            $contact->save();
        }
    }

    public static function match_eid_poc_batches()
    {
        ini_set("memory_limit", "-1");
        $samples = Sample::where('batch_id', 0)->get();
        $fields = self::samples_arrays(); 
        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];

        foreach ($samples as $sample) {
            $old = SampleView::find($sample->id);

            $batch = new Batch($old->only($fields['batch']));

            foreach ($batch_date_array as $date_field) {
                $batch->$date_field = self::clean_date($old->$date_field);
                if($batch->$date_field == '1970-01-01') $batch->$date_field = null;
            }
            $batch->entered_by = $old->user_id;
            $batch->save();

            // $batch = Batch::where(['site_entry' => 2, 'datereceived' => $old->datereceived, 'facility_id' => $old->facility_id,])->first();

            // if(!$batch) continue;

            $sample->batch_id = $batch->id;
            $sample->pre_update();
        }
    }

    public static function match_vl_poc_batches()
    {
        ini_set("memory_limit", "-1");
        $samples = Viralsample::where('batch_id', 0)->get();
        $fields = self::viralsamples_arrays(); 
        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];

        foreach ($samples as $sample) {
            $old = ViralsampleView::find($sample->id);

            $batch = new Viralbatch($old->only($fields['batch']));
            foreach ($batch_date_array as $date_field) {
                $batch->$date_field = self::clean_date($old->$date_field);
                if($batch->$date_field == '1970-01-01') $batch->$date_field = null;
            }
            $batch->entered_by = $old->user_id;
            $batch->save();

            // $batch = Viralbatch::where(['site_entry' => 2, 'datereceived' => $old->datereceived, 'facility_id' => $old->facility_id,])->first();
            // if(!$batch) continue;

            $sample->batch_id = $batch->id;
            $sample->pre_update();
        }
    }

    public static function return_vl_dateinitiated()
    {
        ini_set("memory_limit", "-1");
        $offset =0;

        while(true){
            $rows = ViralsampleView::select('patient', 'facility_id', 'initiation_date')
                                ->whereNotNull('initiation_date')
                                ->whereNotIn('initiation_date', ['0000-00-00', ''])
                                ->limit(5000)
                                ->offset($offset)
                                ->get();
            if($rows->isEmpty()) break;

            foreach ($rows as $key => $row) {
                $d = self::clean_date($row->initiation_date);
                if(!$d) continue;

                $patient = Viralpatient::existing($row->facility_id, $row->patient)->first();
                if(!$patient) continue;

                if($patient->initiation_date && $patient->initiation_date != '0000-00-00') continue;
                $patient->initiation_date = $d;
                $patient->save();

            }
            $offset += 5000;
        }
    }


    public static function previous_dob($class_name=null, $patient=null, $facility_id=null)
    {
        $row = $class_name::where(['patient' => $patient, 'facility_id' => $facility_id])
                    ->whereNotIn('dob', ['0000-00-00', ''])
                    ->whereNotNull('dob')
                    ->first();
        $dob = $row->dob ?? null;
        return self::clean_date($dob);
    }


    public static function clean_date($mydate)
    {
        $mydate = preg_replace("/[^<0-9-\/]/", "", $mydate);
        if(!$mydate || $mydate == '0000-00-00' || $mydate == '(NULL)') return null;

        try {
            $my = Carbon::parse($mydate);
            return $my->toDateString();
        } catch (Exception $e) {
            return null;
        }
    }

    public static function clean_createdat($mydate)
    {
        if(!$mydate) return null;

        try {
            $my = Carbon::parse($mydate);
            return $my->toDateString() . ' 00:00:01';
        } catch (Exception $e) {
            return null;
        }
    }

    public static function clean_preferred_language($val)
    {
        if(!$val) return null;

        $val = (int) $val;
        if($val == 0) return null;
        return $val;
    }


    public static function calculate_dob($datecollected, $years, $months, $class_name=null, $patient=null, $facility_id=null)
    {
        // if(Carbon::createFromFormat('Y-m-d', $date_collected) !== false){            
        //     $dc = Carbon::createFromFormat('Y-m-d', $date_collected);
        //     $dc->subYears($years);
        //     $dc->subMonths($months);
        //     return $dc->toDateString();
        // }
        // return null;

        
        $datecollected = self::clean_date($datecollected);

        if((!$years && !$months) || !$datecollected || $datecollected == '0000-00-00'){
            if(!$class_name) return null;
            $row = $class_name::where(['patient' => $patient, 'facility_id' => $facility_id])
                        ->where('age', '!=', 0)
                        ->whereNotIn('datecollected', ['0000-00-00', ''])
                        ->whereNotNull('datecollected')
                        ->get()->first();
            if($row){
                $mydate = self::clean_date($row->datecollected);
                if(!$mydate) return null;

                if($class_name == "App\OldModels\ViralsampleView"){ 
                    return self::calculate_dob($row->datecollected, $row->age, 0);
                }
                return self::calculate_dob($row->datecollected, 0, $row->age);
            }   
            return null;         
        }

        try {           
            $dc = Carbon::createFromFormat('Y-m-d', $datecollected);
            $dc->subYears($years);
            $dc->subMonths($months);
            return $dc->toDateString();
            
        } catch (Exception $e) {
            return null;
        }
        return null;
    }

    public static function resolve_gender($value, $class_name=null, $patient=null, $facility_id=null)
    {
        $value = trim($value);
        $value = strtolower($value);
        if(\Str::contains($value, ['m', '1'])){
            return 1;
        }
        else if(\Str::contains($value, ['f', '2'])){
            return 2;
        }

        else{
            if(!$class_name) return 3;
            $row = $class_name::where(['patient' => $patient, 'facility_id' => $facility_id])
                        ->whereRaw("(gender = 'M' or gender = 'F')")->get()->first();
            if($row) return self::resolve_gender($row->gender);
            return 3;
        }
    }


    public static function samples_arrays()
    {
        return [

            'batch' => ['highpriority', 'input_complete', 'batch_complete', 'site_entry', 'sent_email', 'printedby', 'user_id', 'lab_id', 'facility_id', 'datedispatchedfromfacility', 'datereceived', 'datebatchprinted', 'datedispatched', 'dateindividualresultprinted', 'synched', 'datesynched' ],

            'mother' => ['hiv_status', 'facility_id', 'ccc_no', 'synched', 'datesynched' ],

            'patient' => ['patient', 'patient_name', 'sex', 'facility_id', 'dob', 'entry_point', 'hei_validation', 'enrollment_ccc_no', 'enrollment_status', 'referredfromsite', 'otherreason', 'dateinitiatedontreatment', 'caregiver_phone', 'patient_phone_no', 'preferred_language', 'synched', 'datesynched' ],

            'sample' => ['id', 'amrs_location', 'provider_identifier', 'order_no', 'sample_type', 'receivedstatus', 'age', 'redraw', 'pcrtype', 'regimen', 'mother_prophylaxis', 'feeding', 'spots', 'comments', 'labcomment', 'parentid', 'rejectedreason', 'reason_for_repeat', 'interpretation', 'result', 'worksheet_id', 'flag', 'run', 'repeatt', 'eqa', 'approvedby', 'approvedby2', 'datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'tat1', 'tat2', 'tat3', 'tat4', 'synched', 'datesynched', 'mother_last_result', 'mother_age', 'time_result_sms_sent' ], 
        ];
    }

    public static function viralsamples_arrays()
    {
        return [

            'batch' => ['highpriority', 'input_complete', 'batch_complete', 'site_entry', 'sent_email', 'printedby', 'user_id', 'lab_id', 'facility_id', 'datedispatchedfromfacility', 'datereceived', 'datebatchprinted', 'datedispatched', 'dateindividualresultprinted', 'synched', 'datesynched' ],

            'patient' => ['patient', 'sex', 'patient_name', 'facility_id', 'patient', 'dob', 'initiation_date', 'caregiver_phone', 'patient_phone_no', 'preferred_language', 'synched', 'datesynched' ],

            'sample' => ['id', 'amrs_location', 'provider_identifier', 'order_no', 'vl_test_request_no', 'receivedstatus', 'age', 'age_category', 'justification', 'other_justification', 'sampletype', 'prophylaxis', 'regimenline', 'pmtct', 'dilutionfactor', 'dilutiontype', 'comments', 'labcomment', 'parentid', 'rejectedreason', 'reason_for_repeat', 'interpretation', 'result', 'rcategory', 'units', 'worksheet_id', 'flag', 'run', 'repeatt', 'approvedby', 'approvedby2', 'datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'tat1', 'tat2', 'tat3', 'tat4', 'synched', 'datesynched', 'time_result_sms_sent' ],
            
        ];
    }

    public static function cd4_arrays()
    {
        return [

            'patient' => ['medicalrecordno', 'sex', 'patient_name', 'dob', ],

            'sample' => ['id', 'facility_id', 'amrs_location', 'provider_identifier', 'order_no', 'receivedstatus', 'age', 'labcomment', 'parentid', 'status_id', 'rejectedreason', 'result', 'worksheet_id', 'flag', 'run', 'repeatt', 'approvedby', 'approvedby2', 'datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'datereceived', 'dateresultprinted', 'datedispatched', 'sent_email', 'tat1', 'tat2', 'tat3', 'tat4', 
                'THelperSuppressorRatio', 'AVGCD3percentLymph', 'AVGCD3AbsCnt', 'AVGCD3CD4percentLymph', 'AVGCD3CD4AbsCnt',
                    'AVGCD3CD8percentLymph', 'AVGCD3CD8AbsCnt', 'AVGCD3CD4CD8percentLymph', 'AVGCD3CD4CD8AbsCnt', 'CD45AbsCnt', ],
            
        ];
    }

    public static function deliveries()
    {
        return [
            'taqmanprocurements' => ['table' => 'taqmanprocurement', 'class' => \App\Taqmanprocurement::class, 'dates' => ['datesubmitted']],
            'abbotprocurements' => ['table' => 'abbottprocurement', 'class' => \App\Abbotprocurement::class, 'dates' => ['datesubmitted']],

            'lab_equipment_trackers' => ['table' => 'lab_equipment_tracker', 'class' => \App\LabEquipmentTracker::class, 'dates' => ['datesubmitted', 'dateemailsent', 'datebrokendown', 'datereported', 'datefixed']],
            'lab_performance_trackers' => ['table' => 'lab_perfomance_tracker', 'class' => \App\LabPerformanceTracker::class, 'dates' => ['datesubmitted', 'dateemailsent']],

            'abbotdeliveries' => ['table' => 'abbottdeliveries', 'class' => \App\Abbotdeliveries::class, 'dates' => ['datereceived', 'dateentered']],
            'taqmandeliveries' => ['table' => 'taqmandeliveries', 'class' => \App\Taqmandeliveries::class, 'dates' => ['datereceived', 'dateentered']],

            // 'requisitions' => ['table' => 'taqmandeliveries', 'dates' => ['datereceived', 'dateentered']],
        ];
    }




















    public static function copy_updated_eid()
    {
        $start = Sample::max('id');
        ini_set("memory_limit", "-1");
        $fields = self::samples_arrays(); 
        $sample_date_array = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'created_at'];
        $batch_date_array = ['datedispatchedfromfacility', 'datereceived', 'datedispatched', 'dateindividualresultprinted', 'datebatchprinted', 'created_at'];
        $offset_value = 60000;
        $new_batch_id = SampleView::selectRaw("max(original_batch_id) as max_id")->first()->max_id;
        while(true)
        {
            $samples = SampleView::limit(self::$limit)->offset($offset_value)->get();
            if($samples->isEmpty()) break;

            foreach ($samples as $key => $value) {
                $sample = \App\Sample::find($value->id);
                if($sample){
                    $patient = $sample->patient;

                    $mother = $patient->mother;
                    $mother->fill($value->only($fields['mother']));
                    $mother->save();

                    $patient->fill($value->only($fields['patient']));

                    if($patient->dob) $patient->dob = self::clean_date($patient->dob);

                    if(!$patient->dob) $patient->dob = self::previous_dob(SampleView::class, $value->patient, $value->facility_id);

                    if(!$patient->dob){
                        $patient->dob = self::calculate_dob($value->datecollected, 0, $value->age, SampleView::class, $value->patient, $value->facility_id);
                    }

                    $patient->sex = self::resolve_gender($value->gender, SampleView::class, $value->patient, $value->facility_id);
                    $patient->ccc_no = $value->enrollment_ccc_no;
                    $patient->save();

                    $batch = $sample->batch;
                    $batch->fill($value->only($fields['batch']));
                    foreach ($batch_date_array as $date_field) {
                        $batch->$date_field = self::clean_date($value->$date_field);
                        if($batch->$date_field == '1970-01-01') $batch->$date_field = null;
                    }
                    $batch->save();

                    $sample->fill($value->only($fields['sample']));
                    foreach ($sample_date_array as $date_field) {
                        $sample->$date_field = self::clean_date($value->$date_field);
                        if($sample->$date_field == '1970-01-01') $sample->$date_field = null;
                    }

                    if($sample->worksheet_id == 0) $sample->worksheet_id = null;
                    if($sample->receivedstatus == 0) $sample->receivedstatus = null;
                    if($sample->result == '') $sample->result = null;
                    if(!$sample->eqa) $sample->eqa = 0;

                    $sample->save();
                }
            }
            $offset_value += self::$limit;
            echo "Completed eid {$offset_value} at " . date('d/m/Y h:i:s a', time()). "\n";
        }
    }


}
