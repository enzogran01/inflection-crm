@php
    $scoreData = $this->getHealthScore();
    $score = $scoreData['score'];
    $nivel = $scoreData['nivel'];
    $detalhes = $scoreData['detalhes'];
    
    $hexColors = [
        'verde' => '#10b981',
        'amarelo' => '#f59e0b',
        'vermelho' => '#ef4444',
    ];

    $descricoes = [
        'verde' => 'Saudável',
        'amarelo' => 'Atenção',
        'vermelho' => 'Crítico',
    ];
    
    $style = [
        'color' => $hexColors[$nivel] ?? $hexColors['verde'],
        'desc' => $descricoes[$nivel] ?? $descricoes['verde'],
    ];
    
    $getScoreColor = function ($scoreValue) use ($hexColors) {
        if ($scoreValue >= 75) return $hexColors['verde'];
        if ($scoreValue >= 40) return $hexColors['amarelo'];
        return $hexColors['vermelho'];
    };
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Score de Saúde Financeira">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            
            {{-- Gauge visual --}}
            <div class="relative flex items-center justify-center w-48 h-48">
                {{-- Círculo base --}}
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-200 dark:text-gray-700" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current" style="color: {{ $style['color'] }}" stroke-width="3" stroke-dasharray="100, 100" stroke-dashoffset="{{ 100 - $score }}" stroke-linecap="round"></circle>
                </svg>
                
                {{-- Texto central --}}
                <div class="absolute flex flex-col items-start justify-center text-left">
                    <span class="text-3xl font-bold" style="color: {{ $style['color'] }}">{{ $score }}%</span>
                    <span class="text-sm font-medium" style="color: {{ $style['color'] }}">{{ $style['desc'] }}</span>
                </div>
            </div>

            {{-- Detalhes (Breakdown) --}}
            <div class="flex-1 w-full space-y-4">
                @foreach([
                    ['label' => 'Inadimplência', 'key' => 'inadimplencia'],
                    ['label' => 'Margem Operacional', 'key' => 'margem'],
                    ['label' => 'Cobertura de Caixa', 'key' => 'cobertura'],
                ] as $item)
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                            <span class="text-gray-500">{{ $detalhes[$item['key']]['score'] }} / 100 pts <span class="text-xs ml-1">(peso {{ $detalhes[$item['key']]['peso'] * 100 }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="h-2 rounded-full" style="background-color: {{ $getScoreColor($detalhes[$item['key']]['score']) }}; width: {{ $detalhes[$item['key']]['score'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
