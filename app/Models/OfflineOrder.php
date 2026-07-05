<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OfflineOrderItem;

class OfflineOrder extends Model
{
    protected $fillable = [

        'order_code',

        'customer_name',
        'phone',

        'project_name',
        'project_location',

        'start_date',

        'duration',
        'duration_unit',

        'notes',

        'total_price',
        'status',
        'return_date',
        'late_days',
        'penalty',

        'created_by'
    ];

    public function items()
    {
        return $this->hasMany(
            OfflineOrderItem::class,
            'offline_order_id'
        );
    }

    // ==========================
    // AGREEMENT
    // ==========================

    public function agreement()
    {
        return $this->hasOne(
            OfflineAgreement::class,
            'offline_order_id'
        );
    }
    
}