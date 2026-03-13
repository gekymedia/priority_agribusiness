<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Manual income records for Account & Finance.
 * external_transaction_id links to Priority Bank for 2-way reconciliation.
 */
class Income extends Model
{
    protected $fillable = [
        'category',
        'amount',
        'received_on',
        'description',
        'reference',
        'external_transaction_id',
    ];

    protected $casts = [
        'received_on' => 'date',
        'amount' => 'decimal:2',
    ];
}
