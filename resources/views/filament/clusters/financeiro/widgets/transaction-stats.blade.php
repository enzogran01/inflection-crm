@php
    $data = $this->getViewData();

    $formatMoney = function ($value) {
        return 'R$ ' . number_format($value / 100, 2, ',', '.');
    };
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div class="fi-wi-stats-overview-stats-ctn grid gap-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- 1. SALDO ATUAL --}}
        <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden cursor-default min-h-[140px]">
            {{-- Standard State --}}
            <div x-show="!hovered" x-transition.opacity.duration.300ms class="grid gap-y-2">
                <div class="flex items-center gap-x-2">
                    <x-filament::icon icon="heroicon-m-banknotes" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Atual</span>
                </div>
                <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    {{ $formatMoney($data['saldo']['atual']) }}
                </div>
                <div class="flex items-center gap-x-1">
                    <span class="text-sm" style="color: {{ $data['saldo']['atual'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        Em caixa hoje
                    </span>
                </div>
            </div>

            {{-- Hover State --}}
            <div x-show="hovered" x-cloak x-transition.opacity.duration.300ms class="absolute inset-0 p-4 bg-white dark:bg-gray-900 flex flex-col justify-center gap-y-3 overflow-y-auto">
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Projeção Fim de Mês</div>
                    <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ $formatMoney($data['saldo']['projecao_fim_mes']) }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Variação (Mês Anterior)</div>
                    <div class="text-sm font-medium flex items-center gap-1" style="color: {{ $data['saldo']['variacao_mom'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        @if($data['saldo']['variacao_mom'] >= 0)
                            <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-4 w-4" />
                            +{{ number_format($data['saldo']['variacao_mom'], 1, ',', '.') }}%
                        @else
                            <x-filament::icon icon="heroicon-m-arrow-trending-down" class="h-4 w-4" />
                            {{ number_format($data['saldo']['variacao_mom'], 1, ',', '.') }}%
                        @endif
                    </div>
                </div>
            </div>
        </div>


        {{-- 2. RECEITAS --}}
        <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden cursor-default min-h-[140px]">
            {{-- Standard State --}}
            <div x-show="!hovered" x-transition.opacity.duration.300ms class="grid gap-y-2">
                <div class="flex items-center gap-x-2">
                    <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Receitas (Mês)</span>
                </div>
                <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    {{ $formatMoney($data['receitas']['total_mes']) }}
                </div>
                <div class="flex items-center gap-x-1">
                    <span class="text-sm" style="color: #16a34a;">Total previsto para o mês</span>
                </div>
            </div>

            {{-- Hover State --}}
            <div x-show="hovered" x-cloak x-transition.opacity.duration.300ms class="absolute inset-0 p-4 bg-white dark:bg-gray-900 flex flex-col justify-center gap-y-3 overflow-y-auto">
                <div>
                    <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        <span>Realizado: {{ number_format($data['receitas']['pct_realizada'], 0) }}%</span>
                        <span>{{ $formatMoney($data['receitas']['pagas_mes']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full" style="background-color: #22c55e; width: {{ min(100, $data['receitas']['pct_realizada']) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Crescimento MoM</div>
                    <div class="text-sm font-medium flex items-center gap-1" style="color: {{ $data['receitas']['crescimento_mom'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        @if($data['receitas']['crescimento_mom'] >= 0)
                            <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-4 w-4" />
                            +{{ number_format($data['receitas']['crescimento_mom'], 1, ',', '.') }}%
                        @else
                            <x-filament::icon icon="heroicon-m-arrow-trending-down" class="h-4 w-4" />
                            {{ number_format($data['receitas']['crescimento_mom'], 1, ',', '.') }}%
                        @endif
                    </div>
                </div>
            </div>
        </div>


        {{-- 3. DESPESAS --}}
        <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden cursor-default min-h-[140px]">
            {{-- Standard State --}}
            <div x-show="!hovered" x-transition.opacity.duration.300ms class="grid gap-y-2">
                <div class="flex items-center gap-x-2">
                    <x-filament::icon icon="heroicon-m-arrow-trending-down" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Despesas (Mês)</span>
                </div>
                <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    {{ $formatMoney($data['despesas']['total_mes']) }}
                </div>
                <div class="flex items-center gap-x-1">
                    <span class="text-sm" style="color: #ca8a04;">Total previsto para o mês</span>
                </div>
            </div>

            {{-- Hover State --}}
            <div x-show="hovered" x-cloak x-transition.opacity.duration.300ms class="absolute inset-0 p-4 bg-white dark:bg-gray-900 flex flex-col justify-center gap-y-3 overflow-y-auto">
                <div>
                    <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        <span>Pagas: {{ number_format($data['despesas']['pct_paga'], 0) }}%</span>
                        <span>{{ $formatMoney($data['despesas']['pagas_mes']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full" style="background-color: #eab308; width: {{ min(100, $data['despesas']['pct_paga']) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        <span>Impacto no Faturamento</span>
                        <span>{{ number_format($data['despesas']['impacto_faturamento'], 1, ',', '.') }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full" style="background-color: #ef4444; width: {{ min(100, $data['despesas']['impacto_faturamento']) }}%"></div>
                    </div>
                </div>
            </div>
        </div>


        {{-- 4. ATRASOS --}}
        <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden cursor-default min-h-[140px]">
            {{-- Standard State --}}
            <div x-show="!hovered" x-transition.opacity.duration.300ms class="grid gap-y-2">
                <div class="flex items-center gap-x-2">
                    <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Atrasos</span>
                </div>
                <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    {{ $formatMoney($data['atrasos']['total']) }}
                </div>
                <div class="flex items-center gap-x-1">
                    <span class="text-sm" style="color: #dc2626;">Total vencido e não pago</span>
                </div>
            </div>

            {{-- Hover State --}}
            <div x-show="hovered" x-cloak x-transition.opacity.duration.300ms class="absolute inset-0 p-4 bg-white dark:bg-gray-900 flex flex-col justify-center gap-y-2 overflow-y-auto">
                <div>
                    <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        <span>Composição</span>
                    </div>
                    {{-- Stacked Progress Bar --}}
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 flex overflow-hidden">
                        <div class="h-2" style="background-color: #eab308; width: {{ min(100, $data['atrasos']['pct_despesas']) }}%" title="Despesas: {{ $formatMoney($data['atrasos']['despesas']) }}"></div>
                        <div class="h-2" style="background-color: #22c55e; width: {{ min(100, $data['atrasos']['pct_receitas']) }}%" title="Receitas: {{ $formatMoney($data['atrasos']['receitas']) }}"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                        <span>A Pagar: {{ number_format($data['atrasos']['pct_despesas'], 0) }}%</span>
                        <span>A Receber: {{ number_format($data['atrasos']['pct_receitas'], 0) }}%</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 mt-1">
                    <div>
                        <div class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Média Atraso</div>
                        <div class="text-sm font-semibold text-gray-950 dark:text-white">{{ $data['atrasos']['tempo_medio'] }} dias</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Saldo Projetado</div>
                        <div class="text-sm font-semibold text-gray-950 dark:text-white" style="color: {{ $data['atrasos']['saldo_projetado'] >= 0 ? '#16a34a' : '#dc2626' }};">{{ $formatMoney($data['atrasos']['saldo_projetado']) }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
