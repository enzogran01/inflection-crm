@props([
    'atual',
    'anterior',
    'invertido' => false,
    'sufixo' => '%'
])

@php
    $variacao = \App\Services\FinanceiroService::calcularVariacaoPercentual($atual, $anterior);
    $isPositive = $variacao >= 0;
    
    // Se invertido for true (ex: inadimplência, onde maior é ruim), a cor verde é para valores negativos
    $isGood = $invertido ? !$isPositive : $isPositive;
    
    $colorClass = $isGood ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
    $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    
    if ($variacao == 0) {
        $colorClass = 'text-gray-500 dark:text-gray-400';
        $icon = 'heroicon-m-minus';
    }
@endphp

<div class="flex items-center gap-x-1 text-sm font-medium {{ $colorClass }}">
    <x-filament::icon :icon="$icon" class="h-4 w-4" />
    <span>{{ $variacao > 0 ? '+' : '' }}{{ number_format($variacao, 1, ',', '.') }}{{ $sufixo }}</span>
    <span class="text-xs text-gray-500 font-normal ml-1">vs mês ant.</span>
</div>
