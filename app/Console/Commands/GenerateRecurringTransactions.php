<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financeiro:generate-recurring-transactions';

    protected $description = 'Gera automaticamente as transações a partir dos moldes de transação padrão para o mês corrente';

    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $this->info("Iniciando geração de transações recorrentes para {$now->format('m/Y')}...");

        $recurringTransactions = \App\Models\RecurringTransaction::query()
            ->whereDoesntHave('transactions', function (\Illuminate\Database\Eloquent\Builder $query) use ($now) {
                $query->whereMonth('due_date', $now->month)
                      ->whereYear('due_date', $now->year);
            })
            ->where(function ($query) use ($now) {
                $query->where('periodicity', 'mensal')
                      ->orWhere(function ($query) use ($now) {
                          $query->where('periodicity', 'anual')
                                ->where('due_month', $now->month);
                      });
            })
            ->get();

        $count = 0;

        foreach ($recurringTransactions as $record) {
            $dueDate = $now->copy();
            if ($record->due_day) {
                $dueDate->setDay(min($record->due_day, $dueDate->daysInMonth));
            }

            \App\Models\Transaction::create([
                'description' => $record->description,
                'type' => $record->type,
                'category' => $record->category,
                'amount' => $record->amount,
                'due_date' => $dueDate,
                'status' => 'pendente',
                'payment_method' => $record->payment_method,
                'recurring_transaction_id' => $record->id,
            ]);

            $count++;
        }

        $this->info("Foram geradas {$count} transações com sucesso.");
    }
}
