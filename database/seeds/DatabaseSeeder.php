<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        factory(Concert::class)->states('published')->create(
            [
                'title' => 'The Red Chord',
                'subtitle' => 'with Animosity and Lethargy',
                'date' => Carbon::parse('+2 weeks'),
                'ticket_price' => 3250, //store price as cents to avoid floating point errors - it can just be an int!
                'venue' => 'The Mosh Pit',
                'venue_address' => '123 Example Lane',
                'city' => 'Laraville',
                'state' => 'ON',
                'zip' => '17916',
                'additional_information' => 'This concert is 19+.'
            ]
        )->addTickets(10);
    }
}
