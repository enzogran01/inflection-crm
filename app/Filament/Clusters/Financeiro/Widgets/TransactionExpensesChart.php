<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionExpensesChart extends ChartWidget
{
    protected static ?string $heading = 'Despesas por Categoria (Mês Atual)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $despesasPorCategoria = Transaction::select('category', DB::raw('SUM(amount) as total'))
            ->where('type', 'despesa')
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [
            '#ef4444', // red
            '#f97316', // orange
            '#f59e0b', // amber
            '#eab308', // yellow
            '#84cc16', // lime
            '#22c55e', // green
            '#10b981', // emerald
            '#14b8a6', // teal
            '#06b6d4', // cyan
            '#0ea5e9', // sky
        ];

        $colors = [];
        $i = 0;

        foreach ($despesasPorCategoria as $despesa) {
            $labels[] = $despesa->category;
            $data[] = $despesa->total / 100;
            $colors[] = $backgroundColors[$i % count($backgroundColors)];
            $i++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Despesas',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
