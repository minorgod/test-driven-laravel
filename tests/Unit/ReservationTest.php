<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
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
              (object) ['price' => 1200],
              (object) ['price' => 1200],
              (object) ['price' => 1200],
          ]
        );

        $reservation = new Reservation($tickets);
        $this->assertEquals(3600, $reservation->totalCost());
    }

}
