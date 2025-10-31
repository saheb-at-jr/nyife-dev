<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'method',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array', // store Razorpay notes as array
    ];
}
