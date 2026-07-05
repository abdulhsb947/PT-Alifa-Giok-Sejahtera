<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PenaltyPayment
 *
 * @property int $id
 * @property int $penalty_id
 * @property string|null $bukti_pembayaran
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment whereBuktiPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment wherePenaltyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PenaltyPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PenaltyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
    'penalty_id',
    'bukti_pembayaran',
    'status'
];
}
