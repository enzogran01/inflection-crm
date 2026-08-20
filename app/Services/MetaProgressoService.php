<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\Transaction;
use Carbon\Carbon;

class MetaProgressoService
{
    /**
     * Calcula o progresso de uma meta baseado em seu tipo e período.
     * @param Meta $meta
     * @param Carbon $inicio
     * @param Carbon $fim
     * @return array ['valor_atual' => float, 'percentual' => float, 'descricao' => string]
     */
    public static function calcular(Meta $meta, Carbon $inicio, Carbon $fim): array
    {
        if (!$meta->valor_alvo || $meta->valor_alvo <= 0) {
            return ['valor_atual' => 0, 'percentual' => 0, 'descricao' => 'Meta sem valor alvo'];
        }

        $tipo = $meta->tipo_meta_financeira ?? 'receita'; // Fallback
        
        // Mapeia tipos antigos para os novos correspondentes, se existirem na base
        $mapeamentoTipos = [
            'receita' => 'faturamento',
            'lucro' => 'economia',
            'despesa' => 'reducao_despesa',
        ];
        
        $tipoResolvido = $mapeamentoTipos[$tipo] ?? $tipo;

        $resultado = match ($tipoResolvido) {
            'faturamento' => self::calcularFaturamento($meta->valor_alvo, $inicio, $fim),
            'reducao_despesa' => self::calcularReducaoDespesa($meta->valor_alvo, $inicio, $fim),
            'reducao_inadimplencia' => self::calcularReducaoInadimplencia($meta->valor_alvo, $inicio, $fim),
            'margem_operacional' => self::calcularMargemOperacionalMeta($meta->valor_alvo, $inicio, $fim),
            'economia' => self::calcularEconomia($meta->valor_alvo, $inicio, $fim),
            default => ['valor_atual' => 0, 'percentual' => 0, 'descricao' => "Tipo de meta desconhecido: {$tipo}"],
        };

        if ($resultado['percentual'] >= 100 && $meta->status !== 'concluida') {
            $meta->update(['status' => 'concluida']);
        } elseif ($resultado['percentual'] > 0 && $resultado['percentual'] < 100 && $meta->status === 'pendente') {
            $meta->update(['status' => 'em_andamento']);
        } elseif ($resultado['percentual'] < 100 && $meta->status === 'concluida') {
            $meta->update(['status' => 'em_andamento']);
        }

        return $resultado;
    }

    private static function calcularFaturamento(float $alvo, Carbon $inicio, Carbon $fim): array
    {
        $realizado = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount'); // Em centavos, ajustado abaixo ou na query dependendo do BD
        
        $realizadoFormatoDecimal = $realizado / 100; // Assumindo banco armazena centavos
        $percentual = min(($realizadoFormatoDecimal / $alvo) * 100, 100);
        
        return [
            'valor_atual' => $realizadoFormatoDecimal,
            'percentual' => $percentual,
            'descricao' => 'Faturamento acumulado'
        ];
    }

    private static function calcularReducaoDespesa(float $alvo, Carbon $inicio, Carbon $fim): array
    {
        $realizado = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount');
            
        $realizadoFormatoDecimal = $realizado / 100;
        
        // Se a despesa já superou o alvo (teto de gastos), progresso é 0.
        // Se a despesa for 0, progresso é 100%
        $percentualGasto = ($realizadoFormatoDecimal / $alvo) * 100;
        $percentual = max(0, min(100 - $percentualGasto, 100));
        
        // Se ainda não gastou nada ou gastou menos que o alvo, inverte para mostrar quão longe do "limite" estamos
        // Ex: limite 1000, gastou 200 (20%). O progresso da "meta" de manter a despesa <= 1000 é 80% (restante livre)
        // Isso é uma heurística comum para teto de gastos.
        
        return [
            'valor_atual' => $realizadoFormatoDecimal,
            'percentual' => $percentual,
            'descricao' => 'Gasto em relação ao teto'
        ];
    }

    private static function calcularReducaoInadimplencia(float $alvoPercentual, Carbon $inicio, Carbon $fim): array
    {
        $totalReceitas = Transaction::where('type', 'receita')
            ->whereBetween('due_date', [$inicio, $fim])
            ->count();
            
        $atrasadas = Transaction::where('type', 'receita')
            ->where('status', 'atrasado')
            ->whereBetween('due_date', [$inicio, $fim])
            ->count();
            
        $inadAtual = FinanceiroService::calcularInadimplencia($atrasadas, $totalReceitas);
        
        // Se inad Atual <= alvo, atingiu a meta (100%)
        // Se inadAtual = 100% e alvo = 5%, o progresso é quase 0.
        // Fórmula de progresso em redução: quão próximo do alvo está?
        // Se inadiplencia for 20% e o alvo é 5%, está 15pp longe.
        // Vamos usar (Alvo / Atual) * 100, max 100
        $percentual = 0;
        if ($inadAtual <= $alvoPercentual) {
            $percentual = 100;
        } elseif ($inadAtual > 0) {
            $percentual = min(($alvoPercentual / $inadAtual) * 100, 100);
        } else {
            $percentual = 100; // se inad = 0, atingiu meta qualquer q seja o alvo
        }

        return [
            'valor_atual' => $inadAtual,
            'percentual' => $percentual,
            'descricao' => 'Inadimplência atual vs Alvo'
        ];
    }

    private static function calcularMargemOperacionalMeta(float $alvo, Carbon $inicio, Carbon $fim): array
    {
        $receitas = Transaction::where('type', 'receita')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount');
            
        $despesas = Transaction::where('type', 'despesa')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount');
            
        $margemAtual = FinanceiroService::calcularMargemOperacional($receitas, $despesas);
        
        $percentual = 0;
        if ($margemAtual > 0 && $alvo > 0) {
            $percentual = min(($margemAtual / $alvo) * 100, 100);
        } elseif ($margemAtual >= $alvo) {
            $percentual = 100; // Ex: alvo era 0 e a margem é 5
        }

        return [
            'valor_atual' => $margemAtual,
            'percentual' => $percentual,
            'descricao' => 'Margem atual vs Alvo'
        ];
    }

    private static function calcularEconomia(float $alvo, Carbon $inicio, Carbon $fim): array
    {
        $receitas = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount');
            
        $despesas = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('due_date', [$inicio, $fim])
            ->sum('amount');
            
        $economia = ($receitas - $despesas) / 100;
        
        $percentual = 0;
        if ($economia > 0 && $alvo > 0) {
            $percentual = min(($economia / $alvo) * 100, 100);
        } elseif ($economia >= $alvo) {
            $percentual = 100;
        }

        return [
            'valor_atual' => $economia,
            'percentual' => $percentual,
            'descricao' => 'Saldo positivo no período'
        ];
    }
}
