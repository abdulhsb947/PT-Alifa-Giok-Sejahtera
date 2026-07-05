<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineAgreement extends Model
{
    use HasFactory;

    protected $table = 'offline_agreements';

    protected $fillable = [

        // RELATION
        'offline_order_id',

        // REQUIREMENT
        'requirement_type',
        'requirement_file',

        // AGREEMENT
        'agreement_file',

        // PAYMENT
        'payment_type',
        'payment_amount',
        'remaining_payment',
        'tax',
        'admin_fee',
        'shipping_fee',
        'other_fee',

        // PAYMENT FILE
        'payment_proof',
        'final_payment_proof',

        // NOTES
        'notes'
    ];

    // ==========================
    // RELATION
    // ==========================

    public function order()
    {
        return $this->belongsTo(
            OfflineOrder::class,
            'offline_order_id'
        );
    }
}
