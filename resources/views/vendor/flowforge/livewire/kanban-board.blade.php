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
    })">
    <style>
        /* Columns */
        .ff-column {
            background-color: #f9fafb !important;
            /* gray-50 */
            border: 1px solid #e5e7eb !important;
            /* gray-200 */
            border-radius: 0.5rem;
            box-shadow: none !important;
        }

        :is(.dark .ff-column) {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            /* white/10 */
        }

        /* Column Header */
        .ff-column__header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        :is(.dark .ff-column__header) {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .ff-column__header {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        /* Header Danger (A Fazer) */
        .kanban-header-danger {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--danger-300), 1);
        }

        .kanban-header-danger .ff-column__title {
            color: #374151;
        }

        :is(.dark .kanban-header-danger) {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--danger-400), 0.8);
        }

        :is(.dark .kanban-header-danger .ff-column__title) {
            color: #d1d5db;
        }

        /* Header Primary (Em Andamento) */
        .kanban-header-primary {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--primary-300), 1);
        }

        .kanban-header-primary .ff-column__title {
            color: #374151;
        }

        :is(.dark .kanban-header-primary) {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--primary-400), 0.8);
        }

        :is(.dark .kanban-header-primary .ff-column__title) {
            color: #d1d5db;
        }

        /* Header Warning (Em Revisão) */
        .kanban-header-warning {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--warning-300), 1);
        }

        .kanban-header-warning .ff-column__title {
            color: #374151;
        }

        :is(.dark .kanban-header-warning) {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--warning-400), 0.8);
        }

        :is(.dark .kanban-header-warning .ff-column__title) {
            color: #d1d5db;
        }

        /* Header Success (Concluído) */
        .kanban-header-success {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--success-300), 1);
        }

        .kanban-header-success .ff-column__title {
            color: #374151;
        }

        :is(.dark .kanban-header-success) {
            background-color: transparent;
            border-bottom: 2px solid rgba(var(--success-400), 0.8);
        }

        :is(.dark .kanban-header-success .ff-column__title) {
            color: #d1d5db;
        }

        .ff-column__count {
            background-color: #eff6ff !important;
            /* blue-50 */
            color: #1d4ed8 !important;
            /* blue-700 */
            border: 1px solid #bfdbfe;
            /* blue-200 */
        }

        :is(.dark .ff-column__count) {
            background-color: rgba(var(--primary-900), 0.5) !important;
            color: #93c5fd !important;
            border-color: rgba(var(--primary-800), 0.5);
        }

        /* Cards */
        .ff-card {
            background-color: #f3f4f6;
            /* gray-100 */
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            /* gray-200 */
        }

        :is(.dark .ff-card) {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Title row: title + priority badge side by side */
        .ff-card__title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .ff-card__title-row .ff-card__title {
            flex: 1;
            min-width: 0;
        }

        /* Priority badges */
        .priority-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            font-size: 0.675rem;
            font-weight: 600;
            border-radius: 9999px;
            white-space: nowrap;
            flex-shrink: 0;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .priority-badge--baixa {
            background-color: rgba(59, 130, 246, 0.12);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        :is(.dark .priority-badge--baixa) {
            background-color: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border-color: rgba(59, 130, 246, 0.4);
        }

        .priority-badge--media {
            background-color: rgba(234, 179, 8, 0.12);
            color: #a16207;
            border: 1px solid rgba(234, 179, 8, 0.3);
        }

        :is(.dark .priority-badge--media) {
            background-color: rgba(234, 179, 8, 0.2);
            color: #fde047;
            border-color: rgba(234, 179, 8, 0.4);
        }

        .priority-badge--alta {
            background-color: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        :is(.dark .priority-badge--alta) {
            background-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* Separator line */
        .ff-card__separator {
            height: 1px;
            background-color: #e5e7eb;
            margin: 0.5rem 0;
        }

        :is(.dark .ff-card__separator) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Responsavel row */
        .ff-card__responsavel {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .ff-card__responsavel-icon {
            width: 1rem;
            height: 1rem;
            color: #6b7280;
            /* gray-500 */
            flex-shrink: 0;
        }

        :is(.dark .ff-card__responsavel-icon) {
            color: #9ca3af;
            /* gray-400 */
        }

        .ff-card__responsavel-name {
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            /* gray-700 */
        }

        :is(.dark .ff-card__responsavel-name) {
            color: #d1d5db;
            /* gray-300 */
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
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fca5a5;
        }

        :is(.dark .kanban-color-danger) {
            background-color: rgba(var(--danger-500), 0.2) !important;
            color: rgba(var(--danger-400), 1) !important;
            border-color: rgba(var(--danger-500), 0.5) !important;
        }

        .kanban-color-primary {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe;
        }

        :is(.dark .kanban-color-primary) {
            background-color: rgba(var(--primary-500), 0.2) !important;
            color: rgba(var(--primary-400), 1) !important;
            border-color: rgba(var(--primary-500), 0.5) !important;
        }

        .kanban-color-warning {
            background-color: #fefce8 !important;
            color: #a16207 !important;
            border: 1px solid #fde047;
        }

        :is(.dark .kanban-color-warning) {
            background-color: rgba(var(--warning-500), 0.2) !important;
            color: rgba(var(--warning-400), 1) !important;
            border-color: rgba(var(--warning-500), 0.5) !important;
        }

        .kanban-color-success {
            background-color: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #86efac;
        }

        :is(.dark .kanban-color-success) {
            background-color: rgba(var(--success-500), 0.2) !important;
            color: rgba(var(--success-400), 1) !important;
            border-color: rgba(var(--success-500), 0.5) !important;
        }

        .kanban-color-default {
            background-color: transparent !important;
            color: inherit !important;
            border: none;
        }

        /* Fix empty column drag scroll */
        .ff-column__content {
            position: relative;
            min-height: 100px;
        }

        .ff-empty-column {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
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
                wire:key="column-{{ $columnId }}" />
            @endforeach
        </div>
    </div>

    <x-filament-actions::modals />
</div>