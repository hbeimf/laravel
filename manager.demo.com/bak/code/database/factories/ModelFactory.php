<?php

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

use Faker\Generator as Faker;

// $factory->define(App\User::class, function (Faker\Generator $faker) {
//     static $password;

//     return [
//         'name' => $faker->name,
//         'email' => $faker->unique()->safeEmail,
//         'password' => $password ?: $password = bcrypt('secret'),
//         'remember_token' => str_random(10),
//     ];
// });


$factory->define(\App\Http\Model\AdminInfo::class, function (Faker $faker){

    return [
        'nickname' => $faker->name(),
        'img_id' => $faker->randomDigit,
        'status' => 1
    ];


});


$factory->define(App\Http\Model\Test::class, function (Faker $faker){

    return [
        'type' => $faker->randomDigit,
        'user_name' => $faker->name(),
        'bank_name' => $faker->company,
        'bank_number' => $faker->bankAccountNumber,
    ];


});

// 线下出款 out_money
$factory->define(App\Http\Model\OutMoney::class, function (Faker $faker){

    return [
        'withdraw_type' => $faker->randomElement(\App\Http\Model\OutMoney::$withdrawType),
        'withdraw_money' => $faker->randomDigit,
        'withdraw_money_actual' => $faker->randomDigit,
        'is_first'  => $faker->boolean,
        'discount_removed' => $faker->boolean,
        'bank_name' => $faker->company,
    ];


});
