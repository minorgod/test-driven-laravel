<?php

namespace App;

use App\Billing\PaymentGateway;
use Illuminate\Database\Eloquent\Model;
use App\Order;
/**
 * Class Reservation
 * @package App
 */
class Reservation
{

    public $tickets;

    /**
     * Reservation constructor.
     * @param $tickets
     * @param $email
     */
    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }


    /**
     * @return \App\Order|\Illuminate\Database\Eloquent\Model
     */
    public function complete(PaymentGateway $paymentGateway, $token)
    {
        $paymentGateway->charge($this->totalCost(), $token);
        return Order::forTickets($this->tickets, $this->email, $this->totalCost());
    }

    /**
     * @return void
     */
    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }


    /**
     * @return mixed
     */
    public function tickets()
    {
        return $this->tickets;
    }

    /**
     * @return mixed
     */
    public function email()
    {
        return $this->email;
    }
}
