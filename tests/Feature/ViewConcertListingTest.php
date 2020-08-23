<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;


class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    /** @test */
    public function user_can_view_a_concert_listing()
    {
        /**
         * Use Direct Model Access design for tests - no UI required, directly access the models in our domain logic
         */

        // Arrange
        // Create a concert
        $concert = Concert::create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13th, 2016 8:00pm'),
            'ticket_price' => 3250, //store price as cents to avoid floating point errors - it can just be an int!
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.'
        ]);

        // Act
        // View the concert listing


        // Assert
        // See the concert details

    }
}