<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TransactionCashflowChart extends ChartWidget
{
    protected static ?string $heading = 'Fluxo de Caixa Diário (Mês Atual)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $daysInMonth = Carbon::now()->daysInMonth;
        $labels = [];
        $receitasData = [];
        $despesasData = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::now()->setDay($i)->format('Y-m-d');
            $labels[] = $i;

            $receitas = Transaction::where('type', 'receita')
                ->where('status', 'pago')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $despesas = Transaction::where('type', 'despesa')
                ->where('status', 'pago')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $receitasData[] = $receitas / 100;
            $despesasData[] = $despesas / 100;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entradas',
                    'data' => $receitasData,
                    'borderColor' => '#10b981', // green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Saídas',
                    'data' => $despesasData,
                    'borderColor' => '#ef4444', // red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
