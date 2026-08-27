<?php

namespace App\Services;

class FinanceiroService
{
    /**
     * @param int $atrasadas Quantidade de receitas atrasadas
     * @param int $totalReceitas Quantidade total de receitas
     * @return float Porcentagem de inadimplência (0 a 100)
     */
    public static function calcularInadimplencia(int $atrasadas, int $totalReceitas): float
    {
        return $totalReceitas > 0 ? round(($atrasadas / $totalReceitas) * 100, 1) : 0.0;
    }

    /**
     * @param float $receitas Total de receitas no período
     * @param float $despesas Total de despesas no período
     * @return float Resultado operacional em R$
     */
    public static function calcularResultadoOperacional(float $receitas, float $despesas): float
    {
        return $receitas - $despesas;
    }

    /**
     * @param float $receitas Total de receitas no período
     * @param float $despesas Total de despesas no período
     * @return float Margem operacional em %
     */
    public static function calcularMargemOperacional(float $receitas, float $despesas): float
    {
        return $receitas > 0 ? round((($receitas - $despesas) / $receitas) * 100, 1) : 0.0;
    }

    /**
     * @param float $saldo Saldo atual (caixa)
     * @param float $despesaMediaDiaria Despesa média por dia no período
     * @return int Quantos dias o saldo atual cobre as despesas
     */
    public static function calcularCoberturaCaixa(float $saldo, float $despesaMediaDiaria): int
    {
        return $despesaMediaDiaria > 0 ? (int) round($saldo / $despesaMediaDiaria, 0) : 0;
    }

    /**
     * @param float $atual Valor no período atual
     * @param float $anterior Valor no período anterior
     * @return float Variação percentual entre os períodos
     */
    public static function calcularVariacaoPercentual(float $atual, float $anterior): float
    {
        if ($anterior == 0) {
            return $atual > 0 ? 100.0 : ($atual < 0 ? -100.0 : 0.0);
        }
        return round((($atual - $anterior) / abs($anterior)) * 100, 1);
    }

    /**
     * Calcula o score de saúde financeira (0 a 100) baseado em um array de indicadores.
     * @param array $indicadores ['inadimplencia' => %, 'margem' => %, 'cobertura' => dias]
     * @param array|null $config Configuração de pesos e limites
     * @return array ['score' => int, 'nivel' => 'verde'|'amarelo'|'vermelho', 'detalhes' => [...]]
     */
    public static function calcularHealthScore(array $indicadores, ?array $config = null): array
    {
        $config = $config ?? [
            'pesos' => [
                'inadimplencia' => 0.35,
                'margem' => 0.35,
                'cobertura' => 0.30
            ],
            'limites' => [
                // Inadimplência: menor é melhor. <= 5% é 100pts, >= 15% é 0pts.
                'inadimplencia' => ['bom' => 5, 'alerta' => 15],
                // Margem: maior é melhor. >= 20% é 100pts, <= 5% é 0pts.
                'margem' => ['bom' => 20, 'alerta' => 5],
                // Cobertura: maior é melhor. >= 30 dias é 100pts, <= 15 dias é 0pts.
                'cobertura' => ['bom' => 30, 'alerta' => 15],
            ],
        ];

        $scores = [];
        $detalhes = [];

        // 1. Score Inadimplência
        $inad = $indicadores['inadimplencia'] ?? 0;
        $inadBom = $config['limites']['inadimplencia']['bom'];
        $inadAlerta = $config['limites']['inadimplencia']['alerta'];
        
        if ($inad <= $inadBom) {
            $scoreInad = 100;
        } elseif ($inad >= $inadAlerta) {
            $scoreInad = 0;
        } else {
            // Regra de três inversa entre bom(5) e alerta(15).
            // Ex: se for 10, fica no meio (50).
            $scoreInad = 100 - (($inad - $inadBom) / ($inadAlerta - $inadBom) * 100);
        }
        $scores['inadimplencia'] = max(0, min(100, $scoreInad));
        $detalhes['inadimplencia'] = ['valor' => $inad, 'score' => $scores['inadimplencia'], 'peso' => $config['pesos']['inadimplencia']];

        // 2. Score Margem
        $marg = $indicadores['margem'] ?? 0;
        $margBom = $config['limites']['margem']['bom'];
        $margAlerta = $config['limites']['margem']['alerta'];

        if ($marg >= $margBom) {
            $scoreMarg = 100;
        } elseif ($marg <= $margAlerta) {
            $scoreMarg = 0;
        } else {
            $scoreMarg = (($marg - $margAlerta) / ($margBom - $margAlerta) * 100);
        }
        $scores['margem'] = max(0, min(100, $scoreMarg));
        $detalhes['margem'] = ['valor' => $marg, 'score' => $scores['margem'], 'peso' => $config['pesos']['margem']];

        // 3. Score Cobertura
        $cob = $indicadores['cobertura'] ?? 0;
        $cobBom = $config['limites']['cobertura']['bom'];
        $cobAlerta = $config['limites']['cobertura']['alerta'];

        if ($cob >= $cobBom) {
            $scoreCob = 100;
        } elseif ($cob <= $cobAlerta) {
            $scoreCob = 0;
        } else {
            $scoreCob = (($cob - $cobAlerta) / ($cobBom - $cobAlerta) * 100);
        }
        $scores['cobertura'] = max(0, min(100, $scoreCob));
        $detalhes['cobertura'] = ['valor' => $cob, 'score' => $scores['cobertura'], 'peso' => $config['pesos']['cobertura']];

        // Calcular Score Total Ponderado
        $scoreFinal = 
            ($scores['inadimplencia'] * $config['pesos']['inadimplencia']) +
            ($scores['margem'] * $config['pesos']['margem']) +
            ($scores['cobertura'] * $config['pesos']['cobertura']);

        $scoreFinal = (int) round($scoreFinal);

        // Nível
        if ($scoreFinal >= 75) {
            $nivel = 'verde';
        } elseif ($scoreFinal >= 40) {
            $nivel = 'amarelo';
        } else {
            $nivel = 'vermelho';
        }

        return [
            'score' => $scoreFinal,
            'nivel' => $nivel,
            'detalhes' => $detalhes
        ];
    }
}
