<x-filament::page>
    <!-- FILTERS -->
    <div class="mb-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
        <h1 class="font-bold text-gray-500 dark:text-gray-400 mb-4">FILTROS</h1>

        <div class="flex flex-row items-center gap-4 w-full">
            <!-- Buscar -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-400 dark:text-gray-300 mb-1">Buscar</label>
                <div class="relative">
                    <input wire:model.live.debounce.500ms="filterSearch" type="text" placeholder="Buscar por título..." style="padding-left: 1.5rem;" class="w-full pr-3 py-2 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-75" />
                </div>
            </div>

            <!-- Área de Atuação -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-400 dark:text-gray-300 mb-1">Área de Atuação</label>
                <div class="relative">
                    <select wire:model.live="filterAreaAtuacao" style="padding-left: 1.5rem;" class="w-full pl-3 pr-10 py-2 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none transition duration-75">
                        <option class="dark:bg-gray-900" value="">Todos</option>
                        @foreach(\App\Models\AreaAtuacao::all() as $area)
                        <option class="dark:bg-gray-900" value="{{ $area->id }}">{{ $area->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Prioridade -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-400 dark:text-gray-300 mb-1">Prioridade</label>
                <div class="relative">
                    <select wire:model.live="filterPrioridade" style="padding-left: 1.5rem;" class="w-full pl-3 pr-10 py-2 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none transition duration-75">
                        <option class="dark:bg-gray-900" value="">Todas</option>
                        <option class="dark:bg-gray-900" value="baixa">Baixa</option>
                        <option class="dark:bg-gray-900" value="media">Média</option>
                        <option class="dark:bg-gray-900" value="alta">Alta</option>
                    </select>
                </div>
            </div>

            <!-- Responsável -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-400 dark:text-gray-300 mb-1">Responsável</label>
                <div class="relative">
                    <select wire:model.live="filterResponsavel" style="padding-left: 1.5rem;" class="w-full pl-3 pr-10 py-2 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 appearance-none transition duration-75">
                        <option class="dark:bg-gray-900" value="">Todos</option>
                        @foreach(\App\Models\User::all() as $user)
                        <option class="dark:bg-gray-900" value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="h-[calc(100vh-16rem)]">
        @livewire('relaticle.flowforge.livewire.kanban-board', [
        'adapter' => $this->getAdapter(),
        'pageClass' => $this::class
        ], key('kanban-board-'.md5($filterSearch.$filterAreaAtuacao.$filterPrioridade.$filterResponsavel)))
    </div>
</x-filament::page>