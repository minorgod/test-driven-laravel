<?php

/** @var Factory $factory */


use App\Concert;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Concert::class, function (Faker $faker) {
    return [
        'title' => 'Example Band',
        'subtitle' => 'with the Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000, //store price as cents to avoid floating point errors - it can just be an int!
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example Lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some additional information.'
    ];
});

$factory->state(Concert::class, 'published', function (Faker $faker) {
    return [
        'published_at' => Carbon::parse('-1 weeks'),
    ];
});

$factory->state(Concert::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null,
    ];
});
