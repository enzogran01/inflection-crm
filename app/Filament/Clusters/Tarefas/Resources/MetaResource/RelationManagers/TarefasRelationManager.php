<?php

namespace App\Filament\Clusters\Tarefas\Resources\MetaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TarefasRelationManager extends RelationManager
{
    protected static string $relationship = 'tarefas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'a_fazer' => 'A Fazer',
                        'em_andamento' => 'Em Andamento',
                        'em_revisao' => 'Em Revisão',
                        'concluido' => 'Concluído',
                    ])
                    ->default('a_fazer')
                    ->required(),
                Forms\Components\Select::make('prioridade')
                    ->label('Prioridade')
                    ->options([
                        'baixa' => 'Baixa',
                        'media' => 'Média',
                        'alta' => 'Alta',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('prazo')
                    ->label('Prazo')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('responsaveis')
                    ->label('Responsáveis')
                    ->multiple()
                    ->relationship('responsaveis', 'name')
                    ->searchable()
                    ->preload()
                    ->saveRelationshipsUsing(function (\Filament\Forms\Components\Select $component, $state, $record) {
                        $oldResponsaveis = $record->responsaveis()->pluck('users.id')->toArray();
                        $component->getRelationship()->sync($state ?? []);
                        
                        $newResponsaveis = $record->fresh()->responsaveis()->pluck('users.id')->toArray();
                        
                        $added = array_diff($newResponsaveis, $oldResponsaveis);
                        
                        foreach ($added as $userId) {
                            \App\Models\User::find($userId)?->notify(new \App\Notifications\TarefaAtribuidaNotification($record));
                        }
                    }),
                Forms\Components\Select::make('cargos')
                    ->label('Cargos')
                    ->multiple()
                    ->relationship('cargos', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'a_fazer' => 'A Fazer',
                        'em_andamento' => 'Em Andamento',
                        'em_revisao' => 'Em Revisão',
                        'concluido' => 'Concluído',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'a_fazer' => 'gray',
                        'em_andamento' => 'primary',
                        'em_revisao' => 'warning',
                        'concluido' => 'success',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('prazo')
                    ->label('Prazo')
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
