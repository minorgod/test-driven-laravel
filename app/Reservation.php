<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
