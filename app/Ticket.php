<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{

    // Whitelist everything as fillable
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /* public function order()
     {
         return $this->belongsTo(Order::class);
     }*/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    /**
     * @return void
     */
    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }
}
