<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    // This is generally safe as long as you don't pass in user input into the constructor of your models
    protected $guarded = [];
    // Make our date field be auto-casted back to a carbon date
    protected $dates = ['date'];

    /**
     * @description magic method to retrieve the date in formatted form
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F jS, Y');
    }

    /**
     * @description magic method to retrieve the time in formatted form
     * @return string
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    /**
     * @description magic method to retrieve the ticket price in formatted form
     * @return string
     */
    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder $query
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder $query
     */
    public function scopeUnpublished($query)
    {
        return $query->whereNull('published_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @param $email
     * @param $ticketQuantity
     * @return Model
     */
    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity);
        return $this->createOrder($email, $tickets);
    }


    /**
     * @param $ticketQuantity
     * @return mixed
     */
    public function findTickets($ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException();
        }
        return $tickets;
    }


    /**
     * @param $quantity
     * @return \App\Reservation
     */
    public function reserveTickets($quantity)
    {
        $tickets = $this->findTickets($quantity)->each(function ($ticket) {
            $ticket->reserve();
        });
        return new Reservation($tickets);
    }


    /**
     * @param $email
     * @param $tickets
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOrder($email, $tickets)
    {
        return Order::forTickets($tickets, $email);
    }

    /**
     * @param $quantity
     * @return \App\Concert
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
        return $this;
    }


    /**
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }


    /**
     * @param $customerEmail
     * @return bool
     */
    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     * @param $customerEmail
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }
}
