<?php

use Illuminate\Database\Seeder;

class KitsSeeder extends Seeder
{
	public $taqmanKits = [
        ['name'=>"Ampliprep, HIV-1 REPLACE Test kits HIVQCAP", 'alias'=>'qualkit', 'unit'=>'48 Tests' ,'factor'=>1, 'testFactor' => ['EID'=>44,'VL'=>42]],
        ['name'=>"Ampliprep Specimen Pre-Extraction Reagent", 'alias'=>'spexagent', 'unit'=>'350 Tests' ,'factor'=>0.15, 'testFactor' =>['EID'=>44,'VL'=>42]],
        ['name'=>"Ampliprep Input S-tube", 'alias'=>'ampinput', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => ['EID'=>44,'VL'=>42]],
        ['name'=>"Ampliprep SPU", 'alias'=>'ampflapless', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => ['EID'=>44,'VL'=>42]],
        ['name'=>"Ampliprep K-Tips", 'alias'=>'ampktips', 'unit'=>'5.1L' ,'factor'=>0.15, 'testFactor' => ['EID'=>44,'VL'=>42]],
        ['name'=>"Ampliprep Wash Reagent", 'alias'=>'ampwash', 'unit'=>'1.2mm, 12 * 36' ,'factor'=>0.5, 'testFactor' => ['EID'=>44,'VL'=>42]],
        ['name'=>"K-Tubes", 'alias'=>'ktubes', 'unit'=>'12 * 96Pcs' ,'factor'=>0.05, 'testFactor' => ['EID'=>44,'VL'=>42]]
    ];

    public $c8800kits = [
        ['name'=>"Ampliprep, HIV-1 REPLACE Test kits HIVQCAP", 'alias'=>'qualkit', 'unit'=>'48 Tests' ,'factor'=>1, 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Ampliprep Specimen Pre-Extraction Reagent", 'alias'=>'spexagent', 'unit'=>'350 Tests' ,'factor'=>0.15, 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Ampliprep Input S-tube", 'alias'=>'ampinput', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Ampliprep SPU", 'alias'=>'ampflapless', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Ampliprep K-Tips", 'alias'=>'ampktips', 'unit'=>'5.1L' ,'factor'=>0.15, 'testFactor' =>['EID'=>94,'VL'=>93]],
        ['name'=>"Ampliprep Wash Reagent", 'alias'=>'ampwash', 'unit'=>'1.2mm, 12 * 36' ,'factor'=>0.5, 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"K-Tubes", 'alias'=>'ktubes', 'unit'=>'12 * 96Pcs' ,'factor'=>0.05, 'testFactor' => ['EID'=>94,'VL'=>93]]
    ];

    public $abbottKits = [
        ['name'=>"ABBOTT RealTime HIV-1 REPLACE Amplification Reagent Kit", 'alias'=>'qualkit','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT m2000rt Optical Calibration Kit", 'alias'=>'calibration','factor'=>['EID'=>0,'VL'=>0], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT RealTime HIV-1 REPLACE Control Kit", 'alias'=>'control', 'factor'=>['EID'=>(2*(2/24)),'VL'=>(3/24)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Bulk mLysisDNA Buffer (for DBS processing only)", 'alias'=>'buffer','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT mSample Preparation System RNA", 'alias'=>'preparation','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT Optical Adhesive Covers", 'alias'=>'adhesive','factor'=>['EID'=>(2/100),'VL'=>(1/100)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT 96-Deep-Well Plate", 'alias'=>'deepplate','factor'=>['EID'=>(2*(2/4)),'VL'=>(3/4)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Saarstet Master Mix Tube", 'alias'=>'mixtube','factor'=>['EID'=>(2*(1/25)),'VL'=>(1/25)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"Saarstet 5ml Reaction Vessels", 'alias'=>'reactionvessels','factor'=>['EID'=>(192/500),'VL'=>(192/500)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"200mL Reagent Vessels", 'alias'=>'reagent','factor'=>['EID'=>(2*(5/6)),'VL'=>(6/6)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"ABBOTT 96-Well Optical Reaction Plate", 'alias'=>'reactionplate','factor'=>['EID'=>(192/500),'VL'=>(1/20)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"1000 uL Eppendorf (Tecan) Disposable Tips (for 1000 tests)", 'alias'=>'1000disposable','factor'=>['EID'=>(2*(421/192)),'VL'=>(841/192)], 'testFactor' => ['EID'=>94,'VL'=>93]],
        ['name'=>"200 ML Eppendorf (Tecan) Disposable Tips", 'alias'=>'200disposable','factor'=>['EID'=>(2*(48/192)),'VL'=>(96/192)], 'testFactor' => ['EID'=>94,'VL'=>93]]
                        ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Kits::truncate();
		$roche = [1,3];
		// dd($this->taqmanKits);
    	foreach ($this->taqmanKits as $key => $kit) {
			$kit = (object) $kit;
            $data = [
                'name' => $kit->name,
                'alias' => $kit->alias,
                'unit' => $kit->unit,
                'machine_id' => 1,
                'factor' => json_encode($kit->factor),
                'testFactor' => json_encode($kit->testFactor)
            ];
            $model = new \App\Kits;
            $model->fill($data);
            $model->save();
		}

    	foreach ($this->abbottKits as $key => $kit) {
			$kit = (object) $kit;
            $model = new \App\Kits;
			$model->fill([
				'name' => $kit->name,
				'alias' => $kit->alias,
				'machine_id' => 2,
				'factor' => json_encode($kit->factor),
				'testFactor' => json_encode($kit->testFactor)
			]);
            $model->save();
		}

        foreach ($this->c8800kits as $key => $kit) {
            $kit = (object) $kit;
            $data = [
                'name' => $kit->name,
                'alias' => $kit->alias,
                'unit' => $kit->unit,
                'machine_id' => 3,
                'factor' => json_encode($kit->factor),
                'testFactor' => json_encode($kit->testFactor)
            ];
            $model = new \App\Kits;
            $model->fill($data);
            $model->save();
        }
    }
}