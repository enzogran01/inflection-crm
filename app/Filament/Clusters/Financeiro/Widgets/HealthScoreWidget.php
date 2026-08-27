<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use App\Services\FinanceiroService;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class HealthScoreWidget extends Widget
{
    protected static string $view = 'filament.clusters.financeiro.widgets.health-score-widget';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 2;

    public function getHealthScore(): array
    {
        $inicio = Carbon::now()->startOfMonth();
        $fim = Carbon::now()->endOfMonth();

        // 1. Inadimplência
        $totalReceitas = Transaction::where('type', 'receita')
            ->whereBetween('due_date', [$inicio, $fim])
            ->count();
        $atrasadas = Transaction::where('status', 'atrasado')
            ->where('type', 'receita')
            ->whereBetween('due_date', [$inicio, $fim])
            ->count();
        $inadimplencia = FinanceiroService::calcularInadimplencia($atrasadas, $totalReceitas);

        // 2. Margem
        $receitasMes = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount') / 100;
        $despesasMes = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount') / 100;
        $margem = FinanceiroService::calcularMargemOperacional($receitasMes, $despesasMes);

        // 3. Cobertura
        $saldoAtual = (Transaction::where('type', 'receita')->where('status', 'pago')->sum('amount')
            - Transaction::where('type', 'despesa')->where('status', 'pago')->sum('amount')) / 100;
        $diasPassados = max(1, Carbon::now()->day);
        $despesaMediaDiaria = $despesasMes / $diasPassados;
        $cobertura = FinanceiroService::calcularCoberturaCaixa($saldoAtual, $despesaMediaDiaria);

        return FinanceiroService::calcularHealthScore([
            'inadimplencia' => $inadimplencia,
            'margem' => $margem,
            'cobertura' => $cobertura,
        ]);
    }
}
