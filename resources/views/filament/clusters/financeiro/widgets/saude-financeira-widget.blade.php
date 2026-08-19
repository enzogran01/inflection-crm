@php
    $data = $this->getViewData();
    
    $formatMoney = function ($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    };

    $getInadColor = function ($value) {
        if ($value < 10) return '#10b981'; // success
        if ($value <= 25) return '#f59e0b'; // warning
        return '#ef4444'; // danger
    };

    $getResOpColor = function ($value) {
        if ($value > 0) return '#10b981';
        if ($value == 0) return '#f59e0b';
        return '#ef4444';
    };

    $getMargemColor = function ($value) {
        if ($value >= 20) return '#10b981';
        if ($value >= 5) return '#f59e0b';
        return '#ef4444';
    };

    $getCoberturaColor = function ($value) {
        if ($value >= 90) return '#10b981';
        if ($value >= 30) return '#f59e0b';
        return '#ef4444';
    };
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div class="fi-wi-stats-overview-stats-ctn grid gap-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- 1. Inadimplência --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col gap-y-2">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5" style="color: {{ $getInadColor($data['inadimplencia']['atual']) }}" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Inadimplência</span>
            </div>
            <div class="text-3xl font-semibold tracking-tight" style="color: {{ $getInadColor($data['inadimplencia']['atual']) }}">
                {{ number_format($data['inadimplencia']['atual'], 1, ',', '.') }}%
            </div>
            <x-trend-indicator :atual="$data['inadimplencia']['atual']" :anterior="$data['inadimplencia']['anterior']" :invertido="true" />
            <div class="text-xs text-gray-400 mt-1">{{ $data['inadimplencia']['atrasadas'] }} de {{ $data['inadimplencia']['total_receitas'] }} receitas</div>
        </div>

        {{-- 2. Resultado Operacional --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col gap-y-2">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-m-banknotes" class="h-5 w-5" style="color: {{ $getResOpColor($data['resultado_operacional']['atual']) }}" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Resultado Operacional</span>
            </div>
            <div class="text-3xl font-semibold tracking-tight" style="color: {{ $getResOpColor($data['resultado_operacional']['atual']) }}">
                {{ $formatMoney($data['resultado_operacional']['atual']) }}
            </div>
            <x-trend-indicator :atual="$data['resultado_operacional']['atual']" :anterior="$data['resultado_operacional']['anterior']" />
            <div class="text-xs text-gray-400 mt-1">Receitas - Despesas</div>
        </div>

        {{-- 3. Margem Operacional --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col gap-y-2">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-m-chart-pie" class="h-5 w-5" style="color: {{ $getMargemColor($data['margem_operacional']['atual']) }}" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Margem Operacional</span>
            </div>
            <div class="text-3xl font-semibold tracking-tight" style="color: {{ $getMargemColor($data['margem_operacional']['atual']) }}">
                {{ number_format($data['margem_operacional']['atual'], 1, ',', '.') }}%
            </div>
            <x-trend-indicator :atual="$data['margem_operacional']['atual']" :anterior="$data['margem_operacional']['anterior']" />
            <div class="text-xs text-gray-400 mt-1">(Receita - Despesa) / Receita</div>
        </div>

        {{-- 4. Cobertura de Caixa --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col gap-y-2">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-m-shield-check" class="h-5 w-5" style="color: {{ $getCoberturaColor($data['cobertura_caixa']['atual']) }}" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cobertura de Caixa</span>
            </div>
            <div class="text-3xl font-semibold tracking-tight" style="color: {{ $getCoberturaColor($data['cobertura_caixa']['atual']) }}">
                {{ $data['cobertura_caixa']['atual'] }} dias
            </div>
            <x-trend-indicator :atual="$data['cobertura_caixa']['atual']" :anterior="$data['cobertura_caixa']['anterior']" sufixo="d" />
            <div class="text-xs text-gray-400 mt-1">Dias cobertos pelo saldo atual</div>
        </div>

    </div>
</x-filament-widgets::widget>
