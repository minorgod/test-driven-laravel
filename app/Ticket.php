<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Ticket
 *
 * @property int $id
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Order $order
 * @property-read \App\Concert $concert
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUpdatedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket available()
 */
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNull('order_id');
    }

    /**
     * @return void
     */
    public function release()
    {
        //$this->order_id = null;
        $this->update(['order_id' => null]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
