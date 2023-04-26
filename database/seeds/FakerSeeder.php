<?php

use Illuminate\Database\Seeder;

class FakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		$batches = factory(App\Batch::class, 30)->create()
		->each(function ($b){
			// $b->sample()->saveMany(
			// 	factory(App\Sample::class, 10)->create(['batch_id' => $b->id])
			// 	->each(function ($s) use ($b){
			// 		$patient = factory(App\Patient::class)->create(['facility_id' => $b->facility_id]);
			// 	})
			// );

			$patient = factory(App\Patient::class, 10)->create(['facility_id' => $b->facility_id])
			->each(function ($p) use ($b){
				$sample = factory(App\Sample::class)->create(['patient_id' => $p->id, 'batch_id' => $b->id]);
			});
		});

		$mothers = factory(App\Mother::class, 50)->create();

		$viralbatches = factory(App\Viralbatch::class, 30)->create()
		->each(function ($b){
			// $b->sample()->saveMany(
			// 	factory(App\Viralsample::class, 10)->create(['batch_id' => $b->id])
			// 	->each(function ($s) use ($b){
			// 		$patient = factory(App\Viralpatient::class)->create(['facility_id' => $b->facility_id]);
			// 	})
			// );

			$patient = factory(App\Viralpatient::class, 10)->create(['facility_id' => $b->facility_id])
			->each(function ($p) use ($b){
				$sample = factory(App\Viralsample::class)->create(['patient_id' => $p->id, 'batch_id' => $b->id]);
			});
		});
    }
}
