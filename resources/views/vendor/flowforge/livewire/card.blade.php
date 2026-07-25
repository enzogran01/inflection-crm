@props(['config', 'columnId', 'record'])

@php
    $priority = strtolower($record['priority'] ?? '');
    
    $bgClass = match($priority) {
        'baixa' => 'custom-card-baixa',
        'media', 'média' => 'custom-card-media',
        'alta' => 'custom-card-alta',
        default => '',
    };
@endphp

<div
    @class([
        'ff-card kanban-card',
        'ff-card--interactive' => $this->editAction() &&  ($this->editAction)(['record' => $record['id']])->isVisible(),
        'ff-card--non-interactive' => !$this->editAction(),
        $bgClass
    ])
    x-sortable-handle
    x-sortable-item="{{ $record['id'] }}"
    @if($this->editAction() &&  ($this->editAction)(['record' => $record['id']])->isVisible())
        wire:click="mountAction('edit', {record: '{{ $record['id'] }}'})"
    @endif
>
    <div class="ff-card__content">
        <h4 class="ff-card__title">{{ $record['title'] }}</h4>

        @if(!empty($record['description']))
            <p class="ff-card__description">{{ $record['description'] }}</p>
        @endif

        @if(collect($record['attributes'] ?? [])->filter(fn($attribute) => !empty($attribute['value']))->isNotEmpty())
            <div class="ff-card__attributes">
                @foreach($record['attributes'] as $attribute => $data)
                    @if(isset($data) && !empty($data['value']))
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
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
