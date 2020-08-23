<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Concert;
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
    public function user_can_view_a_published_concert_listing()
    {
        /**
         * Use Direct Model Access design for tests - no UI required, directly access the models in our domain logic
         */

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->states('published')->create([
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
        $this->visit('/concerts/' . $concert->id);

        // Assert
        // See the concert details
        $this->see('The Red Chord');
        $this->see('with Animosity and Lethargy');
        $this->see('December 13th');
        $this->see('32.50');
        $this->see('The Mosh Pit');
        $this->see('123 Example Lane');
        $this->see('Laraville');
        $this->see('ON');
        $this->see('17916');
        $this->see('For tickets, call (555) 555-5555.');

        /**
         * You could do the above using the newer Laravel 5.4 HTTP testing functionality
         * using the "get" and "assertSee" methods (see below), but for this course, lets stick
         * with the syntax afforded by the browser-testing-kit package we added for
         * backwards compatiblity with older Laravel feature tests.
         * @link https://laravel.com/docs/5.4/http-tests
         */

        /*
        $response = $this->get('/concerts/' . $concert->id);

        // Assert
        // See the concert details
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('December 13th');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville');
        $response->assertSee('ON');
        $response->assertSee('17916');
        $response->assertSee('For tickets, call (555) 555-5555.');
        */
    }

    /** @test */
    public function user_cannot_view_unpublished_concert_listing()
    {
        /**
         * Use Direct Model Access design for tests - no UI required, directly access the models in our domain logic
         */

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->states('unpublished')->create();

        // Act
        // View the concert listing
        $this->get('/concerts/' . $concert->id);

        // Assert
        $this->assertResponseStatus(404);


    }
}
