<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    // This is generally safe as long as you don't pass in user input into the constructor of your models
    protected $guarded = [];
    // Make our date field be auto-casted back to a carbon date
    protected $dates = ['date'];


    // magic method to retrieve the date in formatted form
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F jS, Y');
    }

    // magic method to retrieve the time in formatted form
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    // magic method to retrieve the date in formatted form
    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

}
