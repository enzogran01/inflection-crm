<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'description',
        'type',
        'amount',
        'due_date',
        'paid_at',
        'status',
    ];
}
