<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ConcertTest
 * @package Tests\Unit
 */
class ConcertTest extends TestCase
{

    /**
     * Unit tests for Concert model.
     */

    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('December 1st, 2016 8:00pm'),
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('December 1st, 2016', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_start_time()
    {
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        //create concert with a known date using our model factory
        $concert = factory(Concert::class)->make([
            'ticket_price' => 2000,
        ]);

        //verify the date is formatted as expected
        $this->assertEquals('20.00', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published()
    {
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

    /** @test */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
    }

    /** @test */
    public function can_add_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function tickets_remaining_does_not_include_tickets_associated_with_an_orders()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);
        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    public function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        try {
            $concert = factory(Concert::class)->create()
                ->addTickets(10);
            $order = $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            //make sure no order was created
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }


    /** @test */
    public function cannot_order_tickets_that_have_already_been_purchased()
    {
        try {
            $concert = factory(Concert::class)->create()->addTickets(10);
            $order = $concert->orderTickets('jane@example.com', 8);

            $order = $concert->orderTickets('john@example.com', 3);

        } catch (NotEnoughTicketsException $e) {
            //make sure no order was created
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }
}
