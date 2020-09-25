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
     */
    public function __construct($tickets)
    {
        $this->tickets = $tickets;
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
}
