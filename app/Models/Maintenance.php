<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Maintenance
 *
 * @property int $id
 * @property int $product_id
 * @property int $qty
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $price
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Maintenance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Maintenance extends Model
{
    use HasFactory;

protected $fillable = [
    'product_id',
    'qty',
    'price',
    'status',
    'notes'
];

public function product()
{
    return $this->belongsTo(Product::class)
        ->withTrashed()
        ->withDefault([
            'name' => 'Produk telah dihapus',
            'available_stock' => 0,
            'rented_stock' => 0,
            'maintenance_stock' => 0,
        ]);
}
}
