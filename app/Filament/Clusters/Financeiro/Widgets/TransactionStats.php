<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionStats extends Widget
{
    protected static string $view = 'filament.clusters.financeiro.widgets.transaction-stats';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $hoje = Carbon::today();
        
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();
        
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $fimMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        $cacheKey = 'transaction_stats_' . date('Y-m-d');
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($hoje, $inicioMes, $fimMes, $inicioMesAnterior, $fimMesAnterior) {
            // ---- SALDO ATUAL ----
            $receitasPagas = Transaction::where('type', 'receita')->where('status', 'pago')->sum('amount');
            $despesasPagas = Transaction::where('type', 'despesa')->where('status', 'pago')->sum('amount');
            $saldoAtual = $receitasPagas - $despesasPagas;

            // Variação Mês Anterior (Saldo até fim do mês anterior)
            $receitasPagasMesAnterior = Transaction::where('type', 'receita')
                ->where('status', 'pago')
                ->where('due_date', '<=', $fimMesAnterior)
                ->sum('amount');
                
            $despesasPagasMesAnterior = Transaction::where('type', 'despesa')
                ->where('status', 'pago')
                ->where('due_date', '<=', $fimMesAnterior)
                ->sum('amount');
                
            $saldoMesAnterior = $receitasPagasMesAnterior - $despesasPagasMesAnterior;
            $variacaoSaldo = $saldoMesAnterior != 0 ? (($saldoAtual - $saldoMesAnterior) / abs($saldoMesAnterior)) * 100 : 0;

            // ---- RECEITAS ----
            $receitasReceberMes = Transaction::where('type', 'receita')
                ->where('status', 'pendente')
                ->whereBetween('due_date', [$inicioMes, $fimMes])
                ->sum('amount');
                
            $receitasPagasMes = Transaction::where('type', 'receita')
                ->where('status', 'pago')
                ->whereBetween('due_date', [$inicioMes, $fimMes])
                ->sum('amount');
                
            $receitasTotaisMes = $receitasReceberMes + $receitasPagasMes;
            $pctReceitaRealizada = $receitasTotaisMes > 0 ? ($receitasPagasMes / $receitasTotaisMes) * 100 : 0;

            // Crescimento MoM (Mês sobre Mês)
            $receitasTotaisMesAnterior = Transaction::where('type', 'receita')
                ->whereBetween('due_date', [$inicioMesAnterior, $fimMesAnterior])
                ->sum('amount');
            $crescimentoReceita = $receitasTotaisMesAnterior > 0 ? (($receitasTotaisMes - $receitasTotaisMesAnterior) / $receitasTotaisMesAnterior) * 100 : 0;

            // ---- DESPESAS ----
            $despesasPagarMes = Transaction::where('type', 'despesa')
                ->where('status', 'pendente')
                ->whereBetween('due_date', [$inicioMes, $fimMes])
                ->sum('amount');
                
            $despesasPagasMes = Transaction::where('type', 'despesa')
                ->where('status', 'pago')
                ->whereBetween('due_date', [$inicioMes, $fimMes])
                ->sum('amount');
                
            $despesasTotaisMes = $despesasPagasMes + $despesasPagarMes;
            $pctDespesaPaga = $despesasTotaisMes > 0 ? ($despesasPagasMes / $despesasTotaisMes) * 100 : 0;
            
            $impactoFaturamento = $receitasTotaisMes > 0 ? ($despesasTotaisMes / $receitasTotaisMes) * 100 : 0;

            // ---- ATRASOS ----
            $atrasosReceita = Transaction::where('type', 'receita')->where('status', 'atrasado')->sum('amount');
            $atrasosDespesa = Transaction::where('type', 'despesa')->where('status', 'atrasado')->sum('amount');
            $totalAtrasado = $atrasosReceita + $atrasosDespesa;
            
            $pctAtrasoReceita = $totalAtrasado > 0 ? ($atrasosReceita / $totalAtrasado) * 100 : 0;
            $pctAtrasoDespesa = $totalAtrasado > 0 ? ($atrasosDespesa / $totalAtrasado) * 100 : 0;

            $tempoMedioAtraso = Transaction::where('status', 'atrasado')
                ->selectRaw('AVG(DATEDIFF(CURRENT_DATE, due_date)) as dias_atraso')
                ->value('dias_atraso') ?? 0;

            $saldoSemAtrasos = $saldoAtual + $atrasosReceita - $atrasosDespesa;

            // Projeção Fim de Mês (Saldo Atual + Receitas Pendentes - Despesas Pendentes)
            $projecaoFimMes = $saldoAtual + $receitasReceberMes - $despesasPagarMes;

            return [
                'saldo' => [
                    'atual' => $saldoAtual,
                    'projecao_fim_mes' => $projecaoFimMes,
                    'variacao_mom' => $variacaoSaldo,
                ],
                'receitas' => [
                    'total_mes' => $receitasTotaisMes,
                    'receber_mes' => $receitasReceberMes,
                    'pagas_mes' => $receitasPagasMes,
                    'pct_realizada' => $pctReceitaRealizada,
                    'crescimento_mom' => $crescimentoReceita,
                ],
                'despesas' => [
                    'total_mes' => $despesasTotaisMes,
                    'pagar_mes' => $despesasPagarMes,
                    'pagas_mes' => $despesasPagasMes,
                    'pct_paga' => $pctDespesaPaga,
                    'impacto_faturamento' => $impactoFaturamento,
                ],
                'atrasos' => [
                    'total' => $totalAtrasado,
                    'receitas' => $atrasosReceita,
                    'despesas' => $atrasosDespesa,
                    'pct_receitas' => $pctAtrasoReceita,
                    'pct_despesas' => $pctAtrasoDespesa,
                    'tempo_medio' => round((float) $tempoMedioAtraso),
                    'saldo_projetado' => $saldoSemAtrasos,
                ]
            ];
        });
    }
}
