<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Agreement
 *
 * @property int $id
 * @property int $order_id
 * @property string $file
 * @property string|null $admin_signed_at
 * @property string|null $customer_signed_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $customer_file
 * @property string|null $admin_notes
 * @property string|null $customer_notes
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereAdminSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCustomerFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCustomerNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCustomerSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Agreement extends Model
{
    protected $fillable = [
        'order_id',
        'file',
        'final_file',
        'signature_file',
        'customer_file',
        'admin_signed_at',
        'customer_signed_at',
        'admin_notes',
        'customer_notes',
        'version',
        'status',
        'signature_page',
        'signature_x',
        'signature_y',
        'signature_width',
        'signature_height',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
