<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property int $id
 * @property int $concert_id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConcertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Order extends Model
{
    protected $guarded = [];

    //protected $fillable = ['email'];

    public static function forTickets($tickets, $email, $amount=null)
    {
        $order = self::create([
            'email' => $email,
            'amount' => $amount === null ? $tickets->sum('price') : $amount
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;

    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return int
     */
    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
        $this->delete();
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'concert_id' => $this->concert_id,
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
