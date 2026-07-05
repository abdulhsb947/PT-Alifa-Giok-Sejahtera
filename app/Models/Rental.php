<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rental
 *
 * @property int $id
 * @property int $order_id
 * @property string $tanggal_kirim
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Penalty|null $penalty
 * @method static \Illuminate\Database\Eloquent\Builder|Rental newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereTanggalKirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tanggal_kirim',
        'status'
    ];

    public function order()
{
    return $this->belongsTo(Order::class);
}

public function penalty()
{
    return $this->hasOne(Penalty::class);
}

public function returnItems()
{
    return $this->hasMany(ReturnItem::class);
}
}