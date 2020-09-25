<?php

/** @var Factory $factory */


use App\Concert;
use App\Ticket;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Ticket::class, function (Faker $faker) {
    // Lazily create a concert so we can assign a concert_id to the ticket.
    // This way we can pass in a concert_id if we want.
    return [
        'concert_id' => function () {
            return factory(App\Concert::class)->create()->id;
        }
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

$factory->state(Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
