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
        $now = Carbon::now();

        if ($activeFilter === 'year') {
            $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            $labels = $months;
            
            $receitas = Transaction::where('type', 'receita')
                ->where('status', 'pago')
                ->whereYear('paid_at', $now->year)
                ->get()
                ->groupBy(fn($t) => Carbon::parse($t->paid_at)->month);

            $despesas = Transaction::where('type', 'despesa')
                ->where('status', 'pago')
                ->whereYear('paid_at', $now->year)
                ->get()
                ->groupBy(fn($t) => Carbon::parse($t->paid_at)->month);

            for ($i = 1; $i <= 12; $i++) {
                $receitasData[] = ($receitas->get($i)?->sum('amount') ?? 0) / 100;
                $despesasData[] = ($despesas->get($i)?->sum('amount') ?? 0) / 100;
            }
        } else {
            $daysInMonth = $now->daysInMonth;

            $receitas = Transaction::where('type', 'receita')
                ->where('status', 'pago')
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->get()
                ->groupBy(fn($t) => Carbon::parse($t->paid_at)->day);

            $despesas = Transaction::where('type', 'despesa')
                ->where('status', 'pago')
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->get()
                ->groupBy(fn($t) => Carbon::parse($t->paid_at)->day);

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = $i;
                $receitasData[] = ($receitas->get($i)?->sum('amount') ?? 0) / 100;
                $despesasData[] = ($despesas->get($i)?->sum('amount') ?? 0) / 100;
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
