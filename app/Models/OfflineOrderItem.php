<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineOrderItem extends Model
{
    protected $fillable = [

        'offline_order_id',
        'product_id',

        'quantity',
        'price',

        'returned_qty',
        'damaged_qty',
        'lost_qty',
        'repair_cost',
        'lost_cost',
        'return_notes'
    ];

    protected $casts = [

        'quantity' => 'integer',
        'price' => 'decimal:2',

        'returned_qty' => 'integer',
        'damaged_qty' => 'integer',
        'lost_qty' => 'integer',
        'repair_cost' => 'decimal:2',
        'lost_cost' => 'decimal:2',
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

    public function order()
    {
        return $this->belongsTo(OfflineOrder::class);
    }
}
