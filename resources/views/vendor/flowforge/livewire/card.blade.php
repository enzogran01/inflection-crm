@props(['config', 'columnId', 'record'])

@php
    $priority = strtolower($record['priority'] ?? '');
    
    $priorityLabel = match($priority) {
        'baixa' => 'Baixa',
        'media', 'média' => 'Média',
        'alta' => 'Alta',
        default => null,
    };

    $priorityColorClass = match($priority) {
        'baixa' => 'priority-badge--baixa',
        'media', 'média' => 'priority-badge--media',
        'alta' => 'priority-badge--alta',
        default => '',
    };

    // Extract responsavel from attributes
    $responsavelData = $record['attributes']['nome_responsavel'] ?? null;
    $responsavelName = $responsavelData['value'] ?? null;

    // Get remaining attributes (excluding nome_responsavel)
    $otherAttributes = collect($record['attributes'] ?? [])
        ->filter(fn($attr, $key) => $key !== 'nome_responsavel' && !empty($attr['value']));
@endphp

<div
    @class([
        'ff-card kanban-card',
        'ff-card--interactive' => $this->editAction() &&  ($this->editAction)(['record' => $record['id']])->isVisible(),
        'ff-card--non-interactive' => !$this->editAction(),
    ])
    x-sortable-handle
    x-sortable-item="{{ $record['id'] }}"
    @if($this->editAction() &&  ($this->editAction)(['record' => $record['id']])->isVisible())
        wire:click="mountAction('edit', {record: '{{ $record['id'] }}'})"
    @endif
>
    <div class="ff-card__content">
        {{-- Title row with priority badge --}}
        <div class="ff-card__title-row">
            <h4 class="ff-card__title">{{ $record['title'] }}</h4>
            @if($priorityLabel)
                <span class="priority-badge {{ $priorityColorClass }}">
                    {{ $priorityLabel }}
                </span>
            @endif
        </div>

        @if(!empty($record['description']))
            <p class="ff-card__description">{{ $record['description'] }}</p>
        @endif

        {{-- Other attributes (like prazo) --}}
        @if($otherAttributes->isNotEmpty())
            <div class="ff-card__attributes">
                @foreach($otherAttributes as $attribute => $data)
                    @php
                        $displayValue = $data['value'];
                        if (is_string($displayValue) && preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $displayValue)) {
                            $displayValue = \Carbon\Carbon::parse($displayValue)->format('d/m/Y');
                        }
                    @endphp
                    <x-flowforge::card-badge
                        :label="$data['label']"
                        :value="$displayValue"
                        :color="$data['color'] ?? 'default'"
                        :icon="$data['icon'] ?? null"
                        :type="$data['type'] ?? null"
                        :badge="$data['badge'] ?? null"
                        :rounded="$data['rounded'] ?? 'md'"
                        :size="$data['size'] ?? 'md'"
                    />
                @endforeach
            </div>
        @endif

        {{-- Separator + Responsavel --}}
        @if($responsavelName)
            <div class="ff-card__separator"></div>
            <div class="ff-card__responsavel">
                <x-dynamic-component component="heroicon-o-user" class="ff-card__responsavel-icon" />
                <span class="ff-card__responsavel-name">{{ $responsavelName }}</span>
            </div>
        @endif
    </div>
</div>
