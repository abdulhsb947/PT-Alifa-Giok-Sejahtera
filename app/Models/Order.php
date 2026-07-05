<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Message;
use App\Models\Agreement;
use App\Models\Payment;


/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_code
 * @property string $customer_name
 * @property string $phone
 * @property string $project_name
 * @property string $project_location
 * @property string|null $status
 * @property string $start_date
 * @property int $duration
 * @property string $duration_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $notes
 * @property string|null $admin_notes
 * @property string|null $document
 * @property string|null $review_document
 * @property int $user_id
 * @property-read Agreement|null $agreement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDurationUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProjectLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'project_name',
        'project_location',
        'phone',
        'status',
        'start_date',
        'duration',
        'duration_unit',
        'notes',
        'admin_notes',
        'document',
        'review_document'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function agreement()
{
    return $this->hasOne(Agreement::class, 'order_id', 'id');
}

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rental()
{
    return $this->hasOne(Rental::class);
}
}
