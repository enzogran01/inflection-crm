<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use App\Services\FinanceiroService;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class SaudeFinanceiraWidget extends Widget
{
    protected static string $view = 'filament.clusters.financeiro.widgets.saude-financeira-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $inicioMesAtual = Carbon::now()->startOfMonth();
        $fimMesAtual = Carbon::now()->endOfMonth();
        
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $fimMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        // INADIMPLÊNCIA
        $totalReceitasAtual = Transaction::where('type', 'receita')
            ->whereBetween('due_date', [$inicioMesAtual, $fimMesAtual])
            ->count();
        $atrasadasAtual = Transaction::where('status', 'atrasado')
            ->where('type', 'receita')
            ->whereBetween('due_date', [$inicioMesAtual, $fimMesAtual])
            ->count();
            
        $totalReceitasAnterior = Transaction::where('type', 'receita')
            ->whereBetween('due_date', [$inicioMesAnterior, $fimMesAnterior])
            ->count();
        $atrasadasAnterior = Transaction::where('status', 'atrasado')
            ->where('type', 'receita')
            ->whereBetween('due_date', [$inicioMesAnterior, $fimMesAnterior])
            ->count();

        $inadimplenciaAtual = FinanceiroService::calcularInadimplencia($atrasadasAtual, $totalReceitasAtual);
        $inadimplenciaAnterior = FinanceiroService::calcularInadimplencia($atrasadasAnterior, $totalReceitasAnterior);

        // RESULTADO E MARGEM
        $receitasMesAtual = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicioMesAtual, $fimMesAtual])
            ->sum('amount') / 100;
        $despesasMesAtual = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicioMesAtual, $fimMesAtual])
            ->sum('amount') / 100;

        $receitasMesAnterior = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicioMesAnterior, $fimMesAnterior])
            ->sum('amount') / 100;
        $despesasMesAnterior = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicioMesAnterior, $fimMesAnterior])
            ->sum('amount') / 100;

        $resultadoAtual = FinanceiroService::calcularResultadoOperacional($receitasMesAtual, $despesasMesAtual);
        $resultadoAnterior = FinanceiroService::calcularResultadoOperacional($receitasMesAnterior, $despesasMesAnterior);

        $margemAtual = FinanceiroService::calcularMargemOperacional($receitasMesAtual, $despesasMesAtual);
        $margemAnterior = FinanceiroService::calcularMargemOperacional($receitasMesAnterior, $despesasMesAnterior);

        // COBERTURA
        $saldoAtual = (Transaction::where('type', 'receita')->where('status', 'pago')->sum('amount') 
                     - Transaction::where('type', 'despesa')->where('status', 'pago')->sum('amount')) / 100;
                     
        $saldoAnterior = (Transaction::where('type', 'receita')->where('status', 'pago')->where('paid_at', '<=', $fimMesAnterior)->sum('amount') 
                        - Transaction::where('type', 'despesa')->where('status', 'pago')->where('paid_at', '<=', $fimMesAnterior)->sum('amount')) / 100;

        $diasPassadosAtual = max(1, Carbon::now()->day);
        $despesaMediaDiariaAtual = $despesasMesAtual / $diasPassadosAtual;
        $coberturaAtual = FinanceiroService::calcularCoberturaCaixa($saldoAtual, $despesaMediaDiariaAtual);

        $diasMesAnterior = $fimMesAnterior->daysInMonth;
        $despesaMediaDiariaAnterior = $despesasMesAnterior / $diasMesAnterior;
        $coberturaAnterior = FinanceiroService::calcularCoberturaCaixa($saldoAnterior, $despesaMediaDiariaAnterior);

        return [
            'inadimplencia' => [
                'atual' => $inadimplenciaAtual,
                'anterior' => $inadimplenciaAnterior,
                'atrasadas' => $atrasadasAtual,
                'total_receitas' => $totalReceitasAtual,
            ],
            'resultado_operacional' => [
                'atual' => $resultadoAtual,
                'anterior' => $resultadoAnterior,
            ],
            'margem_operacional' => [
                'atual' => $margemAtual,
                'anterior' => $margemAnterior,
            ],
            'cobertura_caixa' => [
                'atual' => $coberturaAtual,
                'anterior' => $coberturaAnterior,
            ],
        ];
    }
}
