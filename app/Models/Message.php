<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $order_id
 * @property string $sender
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_read
 * @property-read \App\Models\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
     protected $fillable = [
        'order_id',
        'sender',
        'message'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}