<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Penalty
 *
 * @property int $id
 * @property int $rental_id
 * @property int $late_days
 * @property int $late_fee
 * @property int $damage_fee
 * @property int $total_fee
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PenaltyPayment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereDamageFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereLateDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereLateFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereRentalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereTotalFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Penalty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Penalty extends Model
{
    use HasFactory;
    protected $fillable = [
    'rental_id',
    'late_days',
    'late_fee',
    'damage_fee',
    'total_fee',
    'notes'
];

public function payment()
{
    return $this->hasOne(PenaltyPayment::class);
}
public function rental()
{
    return $this->belongsTo(Rental::class);
}
}
