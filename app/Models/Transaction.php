<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'description',
        'type',
        'category',
        'amount',
        'due_date',
        'paid_at',
        'status',
    ];
}
