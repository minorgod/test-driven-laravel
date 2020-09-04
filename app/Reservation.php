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
}
