<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Concert
 *
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property \Illuminate\Support\Carbon $date
 * @property int $ticket_price
 * @property string $venue
 * @property string $venue_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_information
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_start_time
 * @property-read mixed $ticket_price_in_dollars
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|Concert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Concert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Concert published()
 * @method static \Illuminate\Database\Eloquent\Builder|Concert query()
 * @method static \Illuminate\Database\Eloquent\Builder|Concert unpublished()
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereAdditionalInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereTicketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereVenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereVenueAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Concert whereZip($value)
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @property-read int|null $tickets_count
 */
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
        return $this->hasMany(Order::class);
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

        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException();
        }

        $order = $this->orders()->create(['email' => $email]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }
        return $order;
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
