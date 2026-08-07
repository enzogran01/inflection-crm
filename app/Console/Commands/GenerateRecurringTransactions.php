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

        $recurringTransactions = \App\Models\RecurringTransaction::pendingForCurrentMonth()->get();

        $count = 0;

        foreach ($recurringTransactions as $record) {
            $record->generateTransactionForCurrentMonth();
            $count++;
        }

        $this->info("Foram geradas {$count} transações com sucesso.");
    }
}
