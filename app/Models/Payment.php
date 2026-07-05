<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $order_id
 * @property string $proof
 * @property string $amount
 * @property string $payment_type
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    protected $fillable = [
    'order_id',
    'proof',
    'amount',
    'payment_type',
    'status',
    'notes',
    'biaya_sewa',
    'biaya_pemasangan',
    'biaya_pembongkaran',
    'biaya_pengiriman',
    'biaya_lainnya',
    'total_tagihan',
    'sisa_pembayaran',
    'due_date'
];

    public function order()
{
    return $this->belongsTo(\App\Models\Order::class);
}
}
