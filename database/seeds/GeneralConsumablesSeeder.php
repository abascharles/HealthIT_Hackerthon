<?php

use Illuminate\Database\Seeder;

class GeneralConsumablesSeeder extends Seeder
{
    public $consumables = [
        ['name' => 'FILTERED PIPETTE TIPS', 'unit' => '200UL(pack of 960)'],
        ['name' => 'FILTERED PIPETTE TIPS', 'unit' => '1000UL(pack of 960)'],
        ['name' => 'GLOVES LATEX EXAMINATION DISPOSABLE', 'unit' => 'MEDIUM(pack of 50 pair)'],
        ['name' => 'TUBE CRYIOVIALS STERILE WITH SCREW CAP', 'unit' => '2ML(pack of 1000s)'],
        ['name' => 'PLASMA PREPARATION TUBES', 'unit' => 'ppt tubes pack of 1000 tubes'],
        ['name' => 'KIM WIPES', 'unit' => ''],
        ['name' => 'BENCH GUARDS', 'unit' => ''],
        ['name' => 'BIO HAZARD BAG', 'unit' => 'LARGE'],
        ['name' => 'RNA AWAY', 'unit' => ''],
        ['name' => 'DNA AWAY', 'unit' => ''],
        ['name' => 'PASTEUR PIPETTE STERILE', 'unit' => '3ML'],
        ['name' => 'LABORATORY COATS', 'unit' => 'Pack of 50s'],
        ['name' => 'ETHANOL', 'unit' => '2.5 L'],
        ['name' => 'DBS BUNDLES', 'unit' => '50`S CARD'],
        ['name' => 'DBS BUNDLES', 'unit' => '20`S CARD'],
        ['name' => 'NEEDLE HOLDERS', 'unit' => 'Pack of 1000'],
        ['name' => 'Needles', 'unit' => 'Pack of 480'],
        ['name' => 'Hypochloride', 'unit' => '5L'],
        ['name' => 'GAUZE ROLLS', 'unit' => ''],
        ['name' => 'PHOSPHATE BUFFER SALINE', 'unit' => '10X'],
        ['name' => 'PHOSPHATE BUFFER SALINE', 'unit' => '1X'],
        ['name' => 'Falcon tubes', 'unit' => ''],
        ['name' => 'Marker pens', 'unit' => ''],
        ['name' => 'centrifuge tubes', 'unit' => '']
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\GeneralConsumables::truncate();
        foreach($this->consumables as $consumable){
            \App\GeneralConsumables::create($consumable);
        }
    }
}