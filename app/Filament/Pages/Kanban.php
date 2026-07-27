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

    public ?string $filterSearch = null;
    public ?string $filterAreaAtuacao = null;
    public ?string $filterPrioridade = null;
    public ?string $filterResponsavel = null;

    public function getSubject(): Builder
    {
        $query = \App\Models\Tarefa::query()->with(['responsaveis', 'areaAtuacao']);

        if (!empty($this->filterSearch)) {
            $query->where('titulo', 'like', "%{$this->filterSearch}%");
        }

        if (!empty($this->filterAreaAtuacao)) {
            $query->where('area_atuacao_id', $this->filterAreaAtuacao);
        }

        if (!empty($this->filterPrioridade)) {
            $query->where('prioridade', $this->filterPrioridade);
        }

        if (!empty($this->filterResponsavel)) {
            $query->whereHas('responsaveis', function ($q) {
                $q->where('users.id', $this->filterResponsavel);
            });
        }

        $user = auth()->user();

        // Check if user is not an admin or gestor
        if (! $user->can('view_others_tarefa')) {
            $query->where(function ($q) use ($user) {
                if ($user->can('view_own_tarefa')) {
                    $q->whereHas('responsaveis', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
                } else {
                    $q->where('id', '<', 0); // impossible condition
                }
                
                if ($user->can('view_unassigned_tarefa')) {
                    $q->orDoesntHave('responsaveis');
                }
            });
        }

        return $query;
    }

    public function boot(): void
    {
        $this
            ->titleField('titulo')
            ->orderField('position')
            ->columnField('status')
            ->priorityField('prioridade')
            ->cardAttributes([
                'prazo' => 'Prazo',
                'nome_responsavel' => 'Responsável',
                'area_atuacao_nome' => 'area_atuacao_nome',
                'area_atuacao_cor' => 'area_atuacao_cor',
                'area_atuacao_icone' => 'area_atuacao_icone',
            ])
            ->cardAttributeIcons([
                'prazo' => 'heroicon-o-calendar',
                'nome_responsavel' => 'heroicon-o-user',
            ])
            ->cardAttributeColors([
                'prazo' => 'gray',
                'nome_responsavel' => 'default',
            ])
            ->columns([
                'a_fazer' => 'A Fazer',
                'em_andamento' => 'Em Andamento',
                'em_revisao' => 'Em Revisão',
                'concluido' => 'Concluído',
            ])
            ->columnColors([
                'a_fazer' => 'danger',
                'em_andamento' => 'primary',
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
                ->preload()
                ->saveRelationshipsUsing(function (\Filament\Forms\Components\Select $component, $state, $record) {
                    $oldResponsaveis = $record->responsaveis()->pluck('users.id')->toArray();
                    $component->getRelationship()->sync($state ?? []);
                    
                    // Fetch fresh state after sync
                    $newResponsaveis = $record->fresh()->responsaveis()->pluck('users.id')->toArray();
                    
                    $added = array_diff($newResponsaveis, $oldResponsaveis);
                    
                    foreach ($added as $userId) {
                        \App\Models\User::find($userId)?->notify(new \App\Notifications\TarefaAtribuidaNotification($record));
                    }
                }),
            Select::make('meta_id')
                ->label('Meta')
                ->relationship('meta', 'titulo')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('area_atuacao_id')
                ->label('Área de Atuação')
                ->relationship('areaAtuacao', 'nome')
                ->searchable()
                ->preload()
                ->required(),
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
            ->form($this->getFormSchema())
            ->action(function (array $arguments, array $data, \Filament\Forms\Form $form) {
                $record = new \App\Models\Tarefa();
                $record->fill([
                    ...$data,
                    'status' => $arguments['column'],
                ]);
                $record->save();
                
                $form->model($record)->saveRelationships();
            });
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('Editar Tarefa')
            ->visible(fn () => auth()->user()->can('update_tarefa'))
            ->form($this->getFormSchema())
            ->action(function (array $arguments, array $data, \Filament\Forms\Form $form) {
                $record = \App\Models\Tarefa::find($arguments['record']);
                if ($record) {
                    $record->fill($data);
                    $record->save();
                    
                    $form->model($record)->saveRelationships();

                    \Filament\Notifications\Notification::make()
                        ->title('Tarefa atualizada com sucesso')
                        ->success()
                        ->send();
                }
            })
            ->extraModalFooterActions([
                \Filament\Actions\Action::make('delete_tarefa')
                    ->label('Excluir')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Excluir Tarefa')
                    ->modalDescription('Tem certeza que deseja excluir esta tarefa? Esta ação não pode ser desfeita.')
                    ->modalSubmitActionLabel('Sim, excluir')
                    ->action(function (\App\Models\Tarefa $record) {
                        $record->delete();
                        redirect(request()->header('Referer') ?? '/admin/tarefas');
                    })
            ]);
    }
}
