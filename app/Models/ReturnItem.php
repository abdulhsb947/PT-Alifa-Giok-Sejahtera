<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'rental_id',
        'product_id',
        'damaged_qty',
        'lost_qty',
        'repair_cost',
        'lost_cost',
        'notes'
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
