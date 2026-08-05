<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionCashflowChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'month';

    public function getHeading(): string
    {
        return $this->filter === 'year' ? 'Fluxo de Caixa Mensal (Ano Atual)' : 'Fluxo de Caixa Diário (Mês Atual)';
    }

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Este Mês',
            'year' => 'Este Ano',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $labels = [];
        $receitasData = [];
        $despesasData = [];

        if ($activeFilter === 'year') {
            $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            $labels = $months;
            
            for ($i = 1; $i <= 12; $i++) {
                $receitas = Transaction::where('type', 'receita')
                    ->where('status', 'pago')
                    ->whereYear('paid_at', Carbon::now()->year)
                    ->whereMonth('paid_at', $i)
                    ->sum('amount');

                $despesas = Transaction::where('type', 'despesa')
                    ->where('status', 'pago')
                    ->whereYear('paid_at', Carbon::now()->year)
                    ->whereMonth('paid_at', $i)
                    ->sum('amount');

                $receitasData[] = $receitas / 100;
                $despesasData[] = $despesas / 100;
            }
        } else {
            $daysInMonth = Carbon::now()->daysInMonth;

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
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entradas',
                    'data' => $receitasData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Saídas',
                    'data' => $despesasData,
                    'borderColor' => '#ef4444',
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
