<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;

class Kanban extends KanbanBoardPage
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $title = 'Kanban de Tarefas';
    
    protected static ?string $cluster = \App\Filament\Clusters\Tarefas::class;

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;

    public function getSubject(): Builder
    {
        $query = \App\Models\Tarefa::query();

        $user = auth()->user();

        // Check if user is not an admin or gestor
        if (! $user->hasAnyRole(['super_admin', 'admin', 'administrador', 'Administrador', 'gestor', 'Gestor'])) {
            $query->where(function ($q) use ($user) {
                // Tasks where user is responsible
                $q->whereHas('responsaveis', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                // OR tasks with no responsaveis
                ->orDoesntHave('responsaveis');
            });
        }

        return $query;
    }

    public function mount(): void
    {
        $this
            ->titleField('titulo')
            ->orderField('position')
            ->columnField('status')
            ->priorityField('prioridade')
            ->cardAttributes([
                'prazo' => 'Prazo',
            ])
            ->cardAttributeIcons([
                'prazo' => 'heroicon-o-calendar',
            ])
            ->cardAttributeColors([
                'prazo' => 'danger',
            ])
            ->columns([
                'a_fazer' => 'A Fazer',
                'em_andamento' => 'Em Andamento',
                'em_revisao' => 'Em Revisão',
                'concluido' => 'Concluído',
            ])
            ->columnColors([
                'a_fazer' => 'gray',
                'em_andamento' => 'blue',
                'em_revisao' => 'warning',
                'concluido' => 'success',
            ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('titulo')
                ->label('Título')
                ->required(),
            RichEditor::make('descricao')
                ->label('Descrição'),
            Select::make('prioridade')
                ->label('Prioridade')
                ->options([
                    'baixa' => 'Baixa',
                    'media' => 'Média',
                    'alta' => 'Alta',
                ])
                ->required(),
            DatePicker::make('prazo')
                ->label('Prazo')
                ->displayFormat('d/m/Y'),
            Select::make('responsaveis')
                ->label('Responsáveis')
                ->multiple()
                ->relationship('responsaveis', 'name')
                ->searchable()
                ->preload(),
            Select::make('meta_id')
                ->label('Meta')
                ->relationship('meta', 'titulo')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('cargos')
                ->label('Cargos')
                ->multiple()
                ->relationship('cargos', 'name')
                ->searchable()
                ->preload(),
        ];
    }

    public function createAction(Action $action): Action
    {
        return $action
            ->icon('heroicon-o-plus')
            ->iconButton()
            ->color('warning')
            ->modalHeading('Criar Tarefa')
            ->visible(fn () => auth()->user()->can('create_tarefa'))
            ->form($this->getFormSchema());
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('Editar Tarefa')
            ->visible(fn () => auth()->user()->can('update_tarefa'))
            ->form($this->getFormSchema());
    }
}
