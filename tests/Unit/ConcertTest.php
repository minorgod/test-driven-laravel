<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
//use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{

    /**
     * Unit tests for Concert model.
     */

    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date(){
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('December 1st, 2016 8:00pm'),
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('December 1st, 2016', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_start_time(){
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars(){
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'ticket_price' => 2000,
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('20.00', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published(){
        //create concert with a known date using our model factory
        $publishedConcertA = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $publishedConcertB = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $unpublishedConcert = factory(Concert::class)->create([
            'published_at' => null,
        ]);

        //verify the date is formatted as expected
        $publishedConcerts = Concert::published()->get();
        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

}
