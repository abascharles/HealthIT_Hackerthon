<?php

use App\CovidConsumption;
use App\CovidConsumptionDetail;
use App\CovidKit;
use Illuminate\Database\Seeder;

class CovidKitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CovidConsumption::truncate();
        // CovidConsumptionDetail::truncate();
    	CovidKit::truncate();
        $kits = [
            // Taqman kits
        		['material_no' => '9175431190', 'product_description' => 'Cobas® SARS-CoV-2 Test',
        		'pack_size' => 192, 'calculated_pack_size' => 192, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '9175440190', 'product_description' => 'Cobas® SARS-CoV-2 Control Kit',
        		'pack_size' => 16, 'calculated_pack_size' => 1536, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '7002238190', 'product_description' => 'Cobas® Buffer Negative Control Kit',
        		'pack_size' => 16, 'calculated_pack_size' => 1536, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '5534917001', 'product_description' => 'Cobas OMNI Processing Plate',
        		'pack_size' => 32, 'calculated_pack_size' => 1536, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '5534925001', 'product_description' => 'Cobas OMNI Pipette Tips',
        		'pack_size' => 1536, 'calculated_pack_size' => 768, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '5534941001', 'product_description' => 'Cobas OMNI Amplification Plate',
        		'pack_size' => 32, 'calculated_pack_size' => 3072, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '6997503190', 'product_description' => 'Kit Cobas 6800/8800 Wash Reagent IVD (4.2 L)',
        		'pack_size' => 288, 'calculated_pack_size' => 288, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '6997511190', 'product_description' => 'Kit Cobas 6800/8800 SPEC DIL REAGENT IVD (4 x 875 mL)',
        		'pack_size' => 1152, 'calculated_pack_size' => 1152, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '6997538190', 'product_description' => 'Kit Cobas 6800/8800 LYS REAGENT IVD (4 x 875 mL)',
        		'pack_size' => 1152, 'calculated_pack_size' => 1152, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '6997546190', 'product_description' => 'Kit Cobas 6800/8800 MGP IVD',
        		'pack_size' => 480, 'calculated_pack_size' => 480, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '8030073001', 'product_description' => 'Solid Waste Bag Set of 20',
        		'pack_size' => 20, 'calculated_pack_size' => 7680, 'type' => 'Kit', 'machine' => 3],
        		['material_no' => '6438776001', 'product_description' => 'Cobas omni Secondary Tubes 13x75 (optional)*',
        		'pack_size' => 1500, 'calculated_pack_size' => 1500, 'type' => 'Kit', 'machine' => 3],

            // Consumables Kits
        		['material_no' => 'P1', 'product_description' =>  'Oral pharyngeal swabs (Without media)', 'unit'=> 'pack', 'pack_size' => 25, 'type' => 'Consumable'],
                ['material_no' => 'P2', 'product_description' =>  'Nasopharyngeal swabs (Without media)', 'unit'=> 'pack', 'pack_size' => 1000, 'type' => 'Consumable'],
                ['material_no' => 'P3', 'product_description' =>  'Ziplock bags small 6*9inch', 'unit'=> 'pack', 'pack_size' => 1000, 'type' => 'Consumable'],
                ['material_no' => 'P4', 'product_description' =>  'ziplock bags Large 9*12 inch', 'unit'=> 'pack', 'pack_size' => 1000, 'type' => 'Consumable'],
                ['material_no' => 'P5', 'product_description' =>  'UTM', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P6', 'product_description' =>  'Cryovial 4.5ml (pack of 300)', 'unit'=> 'ml', 'pack_size' => 4.5, 'type' => 'Consumable'],
                ['material_no' => 'P7', 'product_description' =>  'Full PPE kit (Tyvek ,shoe cover, one pair of gloves -Nitrile, googles, N95 mask, water proof apron, biohazard bag)', 'type' => 'Consumable'],
                ['material_no' => 'P8', 'product_description' =>  'Ultrafine tip marker pen', 'unit'=> 'pack', 'pack_size' => 12, 'type' => 'Consumable'],
                ['material_no' => 'P9', 'product_description' =>  'Scissors', 'type' => 'Consumable'],
                ['material_no' => 'P10', 'product_description' => 'Printing paper', 'type' => 'Consumable'],
                ['material_no' => 'P11', 'product_description' =>  'Lab coats-Disposable', 'type' => 'Consumable'],
                ['material_no' => 'P12', 'product_description' => 'Face shields', 'type' => 'Consumable'],
                ['material_no' => 'P13', 'product_description' => 'Eye goggles(medical )', 'type' => 'Consumable'],
                ['material_no' => 'P14', 'product_description' => 'Gloves (Nitrile) Large powder free', 'unit'=> 'pack', 'pack_size' => 50, 'type' => 'Consumable'],
                ['material_no' => 'P15', 'product_description' => 'Gloves (Nitrile) Medium powder free', 'unit'=> 'pack', 'pack_size' => 50, 'type' => 'Consumable'],
                ['material_no' => 'P16', 'product_description' => 'Gloves (Nitrile) Small powder free', 'unit'=> 'pack', 'pack_size' => 50, 'type' => 'Consumable'],
                ['material_no' => 'P17', 'product_description' => 'Biohazard Spill Kit (1 unit)', 'type' => 'Consumable'],
                ['material_no' => 'P18', 'product_description' => 'Biohazard bags (large) heavy duty-red', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P19', 'product_description' => 'Biohazard bags (large) heavy duty-yellow', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P20', 'product_description' => 'Lint free tissue wipes', 'unit'=> 'pack', 'pack_size' => 100/150, 'type' => 'Consumable'],
                ['material_no' => 'P21', 'product_description' => 'Bench Guards', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P22', 'product_description' => 'RNA Away', 'unit'=> 'ml', 'pack_size' => 250, 'type' => 'Consumable'],
                ['material_no' => 'P23', 'product_description' => 'DNA Away', 'unit'=> 'ml', 'pack_size' => 250, 'type' => 'Consumable'],
                ['material_no' => 'P24', 'product_description' => 'Eppendorf tubes', 'unit'=> 'Boxes', 'pack_size' => 500, 'type' => 'Consumable'],
                ['material_no' => 'P25', 'product_description' => 'Chlorine, HTH 70%', 'unit'=> 'Litres', 'pack_size' => 5, 'type' => 'Consumable'],
                ['material_no' => 'P27', 'product_description' => 'Alcohol-based sanitizer', 'unit'=> 'Boxes', 'pack_size' => 30, 'type' => 'Consumable'],
                ['material_no' => 'P28', 'product_description' => 'Theatre gown, protective,disposable  (blue)', 'unit'=> 'pack', 'pack_size' => 20, 'type' => 'Consumable'],
                ['material_no' => 'P29', 'product_description' => 'plastic apron, disposable', 'unit'=> 'pack', 'pack_size' => 250, 'type' => 'Consumable'],
                ['material_no' => 'P30', 'product_description' => 'Cryovial 2ml', 'unit'=> 'pack', 'pack_size' => 500, 'type' => 'Consumable'],
                ['material_no' => 'P31', 'product_description' => 'cryoracks', 'unit'=> 'racks', 'pack_size' => 81, 'type' => 'Consumable'],
                ['material_no' => 'P32', 'product_description' => 'Mask, particulate respirator(N95)', 'unit'=> 'pack', 'pack_size' => 10, 'type' => 'Consumable'],
                ['material_no' => 'P33', 'product_description' => 'Mask, medical', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P34', 'product_description' => 'Shoe covers(disposable)', 'unit'=> 'pack', 'pack_size' => 10, 'type' => 'Consumable'],
                ['material_no' => 'P35', 'product_description' => 'Head covers(disposable)', 'unit'=> 'pack', 'pack_size' => 100, 'type' => 'Consumable'],
                ['material_no' => 'P36', 'product_description' => 'Paper towels', 'type' => 'Consumable'],
                ['material_no' => 'P37', 'product_description' => 'Tongue depressors', 'type' => 'Consumable'],

                // Manual Kits
                ['material_no' => 'M1', 'product_description' => 'SARS-COV-2 Extraction Kits',
                'pack_size' => 240, 'calculated_pack_size' => 240, 'type' => 'Manual', 'unit' => 'tests'],
                ['material_no' => 'M2', 'product_description' => 'SARS-Cov2 Primers and probes- 96 tests',
                'pack_size' => 96, 'calculated_pack_size' => 96, 'type' => 'Manual', 'unit' => 'tests'],
                ['material_no' => 'M3', 'product_description' => '10µl Sterile Filtered Pipette tips ',
                'pack_size' => 960, 'calculated_pack_size' => 960, 'type' => 'Manual', 'unit' => 'tips'],
                ['material_no' => 'M4', 'product_description' => '100µl Sterile Filtered Pipette tips',
                'pack_size' => 960, 'calculated_pack_size' => 960, 'type' => 'Manual', 'unit' => 'tips'],
                ['material_no' => 'M5', 'product_description' => '200µl Sterile Filtered Pipette tips',
                'pack_size' => 960, 'calculated_pack_size' => 960, 'type' => 'Manual', 'unit' => 'tips'],
                ['material_no' => 'M6', 'product_description' => '1000µl Sterile Filtered Pipette tips',
                'pack_size' => 960, 'calculated_pack_size' => 960, 'type' => 'Manual', 'unit' => 'tips'],

            // Abbott kits
                ['material_no' => '09N77-090', 'product_description' => 'Abbott RealTimen SARS-COV-2 Amplification kit  4x24 amplifications', 'machine' => 2],
                ['material_no' => '09N77-080', 'product_description' => 'Abbott RealTime SARS-COV-2 Control Kit 8 sets of 2 controls', 'machine' => 2],
                ['material_no' => '09N77-001', 'product_description' => 'Abbott RealTime SARS-COV-2 Combined Application Discto be installed on the relevant m2000 sp and rt systems.', 'machine' => 2],
                ['material_no' => '06K12-24', 'product_description' => 'Abbott m™ Sample Preparation System DNA Reagents 4x24 extractions', 'machine' => 2],
                ['material_no' => '4J71-10', 'product_description' => 'Disposable Tips DiTi’s 1000ul', 'machine' => 2],
                ['material_no' => '4J71-17', 'product_description' => 'Disposable Tips DiTi’s 200ul', 'machine' => 2],
                ['material_no' => '4J71-45', 'product_description' => 'Biohazard Bags', 'machine' => 2],
                ['material_no' => '4J71-20', 'product_description' => '5ml Reaction vessels', 'machine' => 2],
                ['material_no' => '4J71-60', 'product_description' => '200ml Reagent vessels', 'machine' => 2],
                ['material_no' => '4J71-30', 'product_description' => '96 Deep Well Plates', 'machine' => 2],
                ['material_no' => '4J71-70', 'product_description' => 'Optical Reaction Plates  (PCR Plates)', 'machine' => 2],
                ['material_no' => '4J71-75', 'product_description' => 'Optical Adhesive Covers', 'machine' => 2],
                ['material_no' => '4J71-80', 'product_description' => 'Master Mix Tube', 'machine' => 2],
        	];
        foreach ($kits as $key => $kit)
        	CovidKit::create($kit);
    }
}