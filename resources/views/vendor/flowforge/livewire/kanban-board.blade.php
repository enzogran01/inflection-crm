@props(['columns', 'config'])

<div
    class="ff-board"
    x-load
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('flowforge', package: 'relaticle/flowforge'))]"
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('flowforge', package: 'relaticle/flowforge') }}"
    x-data="flowforge({
        state: {
            columns: @js($columns),
            titleField: '{{ $config->getTitleField() }}',
            descriptionField: '{{ $config->getDescriptionField() }}',
            columnField: '{{ $config->getColumnField() }}',
            cardLabel: '{{ $config->getSingularCardLabel() }}',
            pluralCardLabel: '{{ $config->getPluralCardLabel() }}'
        }
    })"
>
<style>
    /* Columns */
    .custom-kanban-column {
        background-color: #ffffff; /* White in light mode */
        border: 1px solid #e5e7eb; /* border-gray-200 */
        border-radius: 0.5rem;
        box-shadow: none;
    }
    :is(.dark .custom-kanban-column) {
        background-color: rgba(var(--primary-950), 0.2);
        border-color: rgba(var(--primary-900), 0.3);
    }

    /* Column Header */
    .ff-column__header {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }
    :is(.dark .ff-column__header) {
        border-bottom-color: rgba(var(--primary-900), 0.3);
    }
    .ff-column__count {
        background-color: #eff6ff !important; /* blue-50 */
        color: #1d4ed8 !important; /* blue-700 */
        border: 1px solid #bfdbfe; /* blue-200 */
    }
    :is(.dark .ff-column__count) {
        background-color: rgba(var(--primary-900), 0.5) !important;
        color: #93c5fd !important;
        border-color: rgba(var(--primary-800), 0.5);
    }

    /* Cards */
    .ff-card {
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }
    :is(.dark .ff-card) {
        background-color: #1f2937;
        border-color: #374151;
    }
    
    .custom-card-baixa {
        background-color: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    :is(.dark .custom-card-baixa) {
        background-color: rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.4);
    }

    .custom-card-media {
        background-color: rgba(234, 179, 8, 0.1);
        border: 1px solid rgba(234, 179, 8, 0.3);
    }
    :is(.dark .custom-card-media) {
        background-color: rgba(234, 179, 8, 0.15);
        border-color: rgba(234, 179, 8, 0.4);
    }

    .custom-card-alta {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    :is(.dark .custom-card-alta) {
        background-color: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.4);
    }

    /* Badges */
    .ff-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.125rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
    }
    .ff-badge__icon {
        width: 1rem;
        height: 1rem;
    }
    .kanban-color-danger {
        background-color: #fef2f2 !important; /* red-50 */
        color: #b91c1c !important; /* red-700 */
        border: 1px solid #fca5a5; /* red-300 */
    }
    :is(.dark .kanban-color-danger) {
        background-color: rgba(153, 27, 27, 0.2) !important;
        color: #fca5a5 !important;
        border-color: rgba(153, 27, 27, 0.5);
    }
    
    /* Fix empty column drag scroll */
    .ff-column__content {
        position: relative;
        min-height: 100px; /* Ensure there is a drop zone */
    }
    .ff-empty-column {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none; /* Prevent interference with drag and drop */
        opacity: 0.7;
    }
</style>

    <!-- Board Content -->
    <div class="ff-board__content">
        <div class="ff-board__columns kanban-board">
            @foreach($columns as $columnId => $column)
                <x-flowforge::column
                    :columnId="$columnId"
                    :column="$column"
                    :config="$config"
                    wire:key="column-{{ $columnId }}"
                />
            @endforeach
        </div>
    </div>

    <x-filament-actions::modals />
</div>
