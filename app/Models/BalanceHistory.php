<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'balance_after',
        'type',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
