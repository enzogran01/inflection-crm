<div>
    <x-filament::modal id="notifications-modal" width="lg">
        <x-slot name="trigger">
            <div class="relative inline-flex mx-4 mt-1">
                <x-filament::icon-button
                    icon="heroicon-o-inbox"
                    color="gray"
                    tooltip="Notificações"
                />
                @if($this->notifications->count() > 0)
                    <span class="absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 flex items-center justify-center min-w-[1.25rem] h-5 rounded-full bg-red-600 text-[10px] font-bold text-white px-1 border-2 border-white dark:border-gray-900 shadow-sm">
                        {{ $this->notifications->count() }}
                    </span>
                @endif
            </div>
        </x-slot>
        
        <x-slot name="heading">
            <div class="flex items-center gap-6">
                <span>Notificações ({{ $this->notifications->count() }})</span>
                @if($this->notifications->count() > 0)
                    <x-filament::link size="sm" color="primary" wire:click="limparTodas" tag="button" class="text-sm font-normal">
                        Limpar todas
                    </x-filament::link>
                @endif
            </div>
        </x-slot>

        <div class="py-2 flex gap-4 flex-col">
            @forelse($this->notifications as $notification)
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm text-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col">
                        <p class="font-medium text-gray-900 dark:text-white mb-1">
                            @if(isset($notification->data['tipo']) && $notification->data['tipo'] === 'reuniao')
                                Você foi convidado para a Reunião
                            @elseif(isset($notification->data['tipo']) && $notification->data['tipo'] === 'tarefa')
                                Você foi atribuído à Tarefa
                            @elseif($notification->type === 'App\Notifications\ReuniaoAtribuidaNotification')
                                Você foi convidado para a Reunião
                            @elseif($notification->type === 'App\Notifications\TarefaAtribuidaNotification')
                                Você foi atribuído à Tarefa
                            @else
                                Nova Notificação
                            @endif
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 mb-3">
                            @php
                                $mensagem = $notification->data['mensagem'] ?? '';
                                $mensagem = str_replace('Você foi convidado para a reunião ', '', $mensagem);
                                $mensagem = str_replace('Você foi atribuído à tarefa: ', '', $mensagem);
                            @endphp
                            {{ $mensagem }}
                        </p>
                    </div>    
                    <x-filament::button size="sm" color="primary" wire:click="estouCiente('{{ $notification->id }}')">
                        Ok
                    </x-filament::button>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    Nenhuma notificação no momento.
                </div>
            @endforelse
        </div>
    </x-filament::modal>
</div>
