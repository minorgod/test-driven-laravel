<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
use App\Ticket;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

/**
 * Class ReservationTest
 * @package Tests\Unit
 */
class ReservationTest extends TestCase
{

    /**
     * Unit tests for Reservation model.
     */

    /** @test */
    public function calculating_total_cost()
    {
        // Make a simple mock of a tickets collection
        $tickets = collect(
            [
                (object)['price' => 1200],
                (object)['price' => 1200],
                (object)['price' => 1200],
            ]
        );

        $reservation = new Reservation($tickets);
        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function retrieving_the_reserverations_tickets()
    {
        // Make a simple mock of a tickets collection
        $tickets = collect(
            [
                (object)['price' => 1200],
                (object)['price' => 1200],
                (object)['price' => 1200],
            ]
        );

        $reservation = new Reservation($tickets);
        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {

        // Make a simple mock of a tickets collection using a spies to let us make assertions later
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets);

        $reservation->cancel();

        foreach($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }


    }



}
