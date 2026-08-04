<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TransactionStats extends BaseWidget
{
    protected function getStats(): array
    {
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        // 1. Saldo Atual (Receitas pagas - Despesas pagas)
        $receitasPagas = Transaction::where('type', 'receita')->where('status', 'pago')->sum('amount');
        $despesasPagas = Transaction::where('type', 'despesa')->where('status', 'pago')->sum('amount');
        $saldoAtual = $receitasPagas - $despesasPagas;

        // 2. Receitas a Receber (Mês)
        $receitasReceberMes = Transaction::where('type', 'receita')
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->sum('amount');

        // 3. Despesas a Pagar (Mês)
        $despesasPagarMes = Transaction::where('type', 'despesa')
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->sum('amount');

        // 4. Inadimplência / Atrasos
        $totalAtrasado = Transaction::where('status', 'atrasado')->sum('amount');

        return [
            Stat::make('Saldo Atual', 'R$ ' . number_format($saldoAtual / 100, 2, ',', '.'))
                ->description('Em caixa hoje')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($saldoAtual >= 0 ? 'success' : 'danger'),
                
            Stat::make('Receitas a Receber', 'R$ ' . number_format($receitasReceberMes / 100, 2, ',', '.'))
                ->description('Previsto para este mês')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Despesas a Pagar', 'R$ ' . number_format($despesasPagarMes / 100, 2, ',', '.'))
                ->description('Previsto para este mês')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
                
            Stat::make('Atrasos', 'R$ ' . number_format($totalAtrasado / 100, 2, ',', '.'))
                ->description('Total vencido e não pago')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
