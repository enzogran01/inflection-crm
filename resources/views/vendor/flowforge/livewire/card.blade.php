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

    // Extract area_atuacao from attributes
    $areaAtuacaoData = $record['attributes']['area_atuacao_nome'] ?? null;
    $areaAtuacaoNome = $areaAtuacaoData['value'] ?? null;
    
    $areaAtuacaoCorData = $record['attributes']['area_atuacao_cor'] ?? null;
    $areaAtuacaoCor = $areaAtuacaoCorData['value'] ?? '#6b7280';
    
    $areaAtuacaoIconeData = $record['attributes']['area_atuacao_icone'] ?? null;
    $areaAtuacaoIcone = $areaAtuacaoIconeData['value'] ?? null;

    // Get remaining attributes (excluding nome_responsavel and area_atuacao_*)
    $otherAttributes = collect($record['attributes'] ?? [])
        ->filter(fn($attr, $key) => !in_array($key, ['nome_responsavel', 'area_atuacao_nome', 'area_atuacao_cor', 'area_atuacao_icone']) && !empty($attr['value']));
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
        {{-- Badges row (Area Atuação + Priority) --}}
        <div class="ff-card__badges-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; gap: 0.5rem; flex-wrap: wrap;">
            @if($areaAtuacaoNome)
                <span class="ff-badge ff-badge--md ff-badge--rounded-md" style="background-color: {{ $areaAtuacaoCor }}20; color: {{ $areaAtuacaoCor }}; border: 1px solid {{ $areaAtuacaoCor }}30; display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px;">
                    @if($areaAtuacaoIcone)
                        <x-dynamic-component :component="$areaAtuacaoIcone" class="w-4 h-4" style="width: 1rem; height: 1rem;" />
                    @endif
                    <span class="ff-badge__value" style="font-size: 0.75rem; font-weight: 500;">{{ $areaAtuacaoNome }}</span>
                </span>
            @else
                <div></div>
            @endif

            @if($priorityLabel)
                <span class="priority-badge {{ $priorityColorClass }}">
                    {{ $priorityLabel }}
                </span>
            @endif
        </div>

        {{-- Title row --}}
        <div class="ff-card__title-row">
            <h4 class="ff-card__title" style="margin-top: 6px; margin-bottom: 6px; word-wrap: break-word; white-space: normal; font-size: 1rem">{{ $record['title'] }}</h4>
        </div>

        @if(!empty($record['description']))
            <p class="ff-card__description">{{ $record['description'] }}</p>
        @endif

        {{-- Separator + Responsavel --}}
        <div class="ff-card__separator"></div>
        @if($responsavelName)
            <div class="ff-card__responsavel">
                <x-dynamic-component component="heroicon-o-user" class="ff-card__responsavel-icon" />
                <span class="ff-card__responsavel-name">{{ $responsavelName }}</span>
            </div>
        @endif

        {{-- prazo --}}
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
    </div>
</div>
