<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class TicketTest
 * @package Tests\Unit
 */
class TicketTest extends TestCase
{

    /**
     * Unit tests for Order model.
     */

    use DatabaseMigrations;

    /** @test */
    function a_ticket_can_be_released() {

        $concert = factory(Concert::class)->create();
        $concert->addTickets(1);
        $order = $concert->orderTickets('jane@example.com', 1);

        $ticket = $order->tickets()->first();
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        //get a fresh ticket
        $this->assertNull($ticket->fresh()->order_id);

    }

}
