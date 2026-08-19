@php
    $scoreData = $this->getHealthScore();
    $score = $scoreData['score'];
    $nivel = $scoreData['nivel'];
    $detalhes = $scoreData['detalhes'];
    
    $cores = [
        'verde' => ['bg' => 'bg-green-500', 'text' => 'text-green-500', 'ring' => 'ring-green-500', 'desc' => 'Saudável'],
        'amarelo' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-500', 'ring' => 'ring-yellow-500', 'desc' => 'Atenção'],
        'vermelho' => ['bg' => 'bg-red-500', 'text' => 'text-red-500', 'ring' => 'ring-red-500', 'desc' => 'Crítico'],
    ];
    
    $style = $cores[$nivel] ?? $cores['verde'];
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Score de Saúde Financeira">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 py-4">
            
            {{-- Gauge visual --}}
            <div class="relative flex items-center justify-center w-48 h-48">
                {{-- Círculo base --}}
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-200 dark:text-gray-700" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-current {{ $style['text'] }}" stroke-width="3" stroke-dasharray="100, 100" stroke-dashoffset="{{ 100 - $score }}" stroke-linecap="round"></circle>
                </svg>
                
                {{-- Texto central --}}
                <div class="absolute flex flex-col items-center justify-center text-center">
                    <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $score }}</span>
                    <span class="text-sm font-medium {{ $style['text'] }}">{{ $style['desc'] }}</span>
                </div>
            </div>

            {{-- Detalhes (Breakdown) --}}
            <div class="flex-1 w-full space-y-4">
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Inadimplência</span>
                        <span class="text-gray-500">{{ $detalhes['inadimplencia']['score'] }} / 100 pts <span class="text-xs ml-1">(peso {{ $detalhes['inadimplencia']['peso'] * 100 }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="h-2 rounded-full {{ $detalhes['inadimplencia']['score'] >= 70 ? 'bg-green-500' : ($detalhes['inadimplencia']['score'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $detalhes['inadimplencia']['score'] }}%"></div>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Margem Operacional</span>
                        <span class="text-gray-500">{{ $detalhes['margem']['score'] }} / 100 pts <span class="text-xs ml-1">(peso {{ $detalhes['margem']['peso'] * 100 }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="h-2 rounded-full {{ $detalhes['margem']['score'] >= 70 ? 'bg-green-500' : ($detalhes['margem']['score'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $detalhes['margem']['score'] }}%"></div>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Cobertura de Caixa</span>
                        <span class="text-gray-500">{{ $detalhes['cobertura']['score'] }} / 100 pts <span class="text-xs ml-1">(peso {{ $detalhes['cobertura']['peso'] * 100 }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="h-2 rounded-full {{ $detalhes['cobertura']['score'] >= 70 ? 'bg-green-500' : ($detalhes['cobertura']['score'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $detalhes['cobertura']['score'] }}%"></div>
                    </div>
                </div>
            </div>
            
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
