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

    public function scopePendingForCurrentMonth($query)
    {
        $now = \Carbon\Carbon::now();
        return $query->whereDoesntHave('transactions', function ($q) use ($now) {
            $q->whereMonth('due_date', $now->month)
              ->whereYear('due_date', $now->year);
        })->where(function ($q) use ($now) {
            $q->where('periodicity', 'mensal')
              ->orWhere(function ($q) use ($now) {
                  $q->where('periodicity', 'anual')
                    ->where('due_month', $now->month);
              });
        });
    }

    public function generateTransactionForCurrentMonth()
    {
        $dueDate = \Carbon\Carbon::now();
        if ($this->due_day) {
            $dueDate->setDay(min($this->due_day, $dueDate->daysInMonth));
        }

        return Transaction::create([
            'description' => $this->description,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => $this->amount,
            'due_date' => $dueDate,
            'status' => 'pendente',
            'payment_method' => $this->payment_method,
            'recurring_transaction_id' => $this->id,
        ]);
    }
}