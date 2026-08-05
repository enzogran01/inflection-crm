<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'description',
        'type',
        'category',
        'amount',
        'periodicity',
        'payment_method',
        'due_day',
        'due_month',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}