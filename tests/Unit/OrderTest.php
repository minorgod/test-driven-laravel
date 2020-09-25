<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class OrderTest
 * @package Tests\Unit
 */
class OrderTest extends TestCase
{

    /**
     * Unit tests for Order model.
     */

    use DatabaseMigrations;


    /** @test */
    public function creating_an_order_from_tickets_and_email_and_amount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());

       /* $result = $order->toArray();

        $this->assertEquals(
            [
                'id' => $order->id,
                'concert_id' => $order->concert_id,
                'email' => $order->email,
                'ticket_quantity' => $order->ticketQuantity(),
                'amount' => 6000,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
            $result
        );*/
    }

    /** @test */
    public function converting_to_an_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(10);
        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals(
            [
                'id' => $order->id,
                'concert_id' => $order->concert_id,
                'email' => $order->email,
                'ticket_quantity' => $order->ticketQuantity(),
                'amount' => 6000,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
            $result
        );
    }

}
