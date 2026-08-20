<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;

class UpdateOverdueTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financeiro:update-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status de transações pendentes que passaram da data de vencimento para atrasado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Transaction::where('status', 'pendente')
            ->whereDate('due_date', '<', Carbon::today())
            ->update(['status' => 'atrasado']);

        $this->info("{$count} transações foram atualizadas para atrasado.");
    }
}
