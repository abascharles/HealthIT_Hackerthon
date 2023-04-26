<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'user_type_id' => rand(1, 5),
        'lab_id' => env('APP_LAB'),
        'surname' => $faker->lastName,
        'oname' => $faker->firstName,
        'email' => $faker->unique()->safeEmail,
        // 'password' => $password ?: $password = bcrypt('secret'),
        'password' => env('MASTER_PASSWORD', 12345678),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Batch::class, function (Faker $faker) {
    return [
        'received_by' => 1,
        'user_id' => 1,
        'batch_full' => 1,
        'input_complete' => 1,
        // 'lab_id' => rand(1, 7),
        'lab_id' => env('APP_LAB'),
        'facility_id' => rand(1, 1000),
        'datereceived' => $faker->dateTimeThisYear($max = 'now'),
        // 'datereceived' => $faker->numerify('2017-0#-1#'),
        // 'datereceived' => $faker->regexify('2017\-+0[1-9]+\-[1-2]+[0-9]'),
        // 'datereceived' => $faker->regexify("^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$"),
    ];
});

$factory->define(App\Sample::class, function (Faker $faker) {
    return [
        'patient_id' => rand(1, 100),
        'batch_id' => rand(1, 20),
        'receivedstatus' => 1,
        'age' => rand(1, 12),
        'pcrtype' => rand(1, 3),
        'regimen' => $faker->randomElement([12, 13, 14, 15, 16]),
        'mother_prophylaxis' => $faker->randomElement([5, 6, 7, 18, 17, 19, 20, 21, 22, 23, 24]),
        'mother_age' => rand(18, 35),
        'feeding' => rand(1, 5),
        'spots' => rand(1, 5),
        'datecollected' => $faker->dateTimeThisYear($max = 'now'),
    ];
});

$factory->define(App\Patient::class, function (Faker $faker) {
    return [
        'patient' => $faker->bothify('#####-?????'),
        'mother_id' => rand(1, 50),
        'facility_id' => rand(1, 1000),
        'entry_point' => rand(1, 5),
        'dob' => $faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now'),
        'sex' => rand(1, 2),
    ];
});

$factory->define(App\Mother::class, function (Faker $faker) {
    return [
        'ccc_no' => $faker->bothify('#####-?????'),
        'facility_id' => rand(1, 1000),
        'hiv_status' => rand(1, 2),
        'mother_dob' => $faker->dateTimeBetween($startDate = '-35 years', $endDate = '-18 years'),        
    ];
});


$factory->define(App\Viralbatch::class, function (Faker $faker) {
    return [
        'received_by' => 1,
        'user_id' => 1,
        'batch_full' => 1,
        'input_complete' => 1,
        // 'lab_id' => rand(1, 7),
        'lab_id' => env('APP_LAB'),
        'facility_id' => rand(1, 1000),
        'datereceived' => $faker->dateTimeThisYear($max = 'now'),
        // 'datereceived' => $faker->numerify('2017-0#-1#'),
        // 'datereceived' => $faker->regexify('2017\-+0[1-9]+\-[1-2]+[0-9]'),
    ];
});

$factory->define(App\Viralsample::class, function (Faker $faker) {
    return [
        'patient_id' => rand(1, 100),
        'batch_id' => rand(1, 20),
        'receivedstatus' => 1,
        'age' => rand(1, 60),
        // 'age_category' => rand(6, 11),
        'justification' => rand(1, 7),
        'sampletype' => rand(1, 4),
        'prophylaxis' => rand(1, 20),
        'regimenline' => rand(1, 2),
        'pmtct' => rand(1, 3),
        'datecollected' => $faker->dateTimeThisYear($max = 'now'),
    ];
});

$factory->define(App\Viralpatient::class, function (Faker $faker) {
    return [
        'patient' => $faker->bothify('#####-?????'),
        'facility_id' => rand(1, 1000),
        'dob' => $faker->dateTimeBetween($startDate = '-60 years', $endDate = 'now'),
        'sex' => rand(1, 2),
    ];
});
