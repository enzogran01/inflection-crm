<?php

namespace App\Filament\Clusters\Tarefas\Resources;

use App\Filament\Clusters\Tarefas;
use App\Filament\Clusters\Tarefas\Resources\MetaResource\Pages;
use App\Filament\Clusters\Tarefas\Resources\MetaResource\RelationManagers;
use App\Models\Meta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MetaResource extends Resource
{
    protected static ?string $model = Meta::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $modelLabel = 'Meta';
    protected static ?string $pluralModelLabel = 'Metas';

    protected static ?string $cluster = Tarefas::class;

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('prazo')
                    ->label('Prazo')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em Andamento',
                        'concluida' => 'Concluída',
                    ])
                    ->default('pendente')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('titulo')
                        ->weight('bold')
                        ->size('lg')
                        ->limit(50)
                        ->wrap()
                        ->extraAttributes(['class' => 'break-all min-w-0'])
                        ->searchable(),
                    Tables\Columns\TextColumn::make('descricao')
                        ->formatStateUsing(fn (string $state): string => \Illuminate\Support\Str::limit(strip_tags($state), 150))
                        ->wrap()
                        ->extraAttributes(['class' => 'break-all min-w-0'])
                        ->color('gray'),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pendente' => 'Pendente',
                                'em_andamento' => 'Em andamento',
                                'concluida' => 'Concluída',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pendente' => 'danger',
                                'em_andamento' => 'warning',
                                'concluida' => 'success',
                                default => 'primary',
                            })
                            ->grow(false),
                        Tables\Columns\TextColumn::make('tarefas_count')
                            ->counts('tarefas')
                            ->icon('heroicon-m-check-circle')
                            ->formatStateUsing(fn ($state) => $state . ' Tarefa' . ($state !== 1 ? 's' : ''))
                            ->color('danger')
                            ->extraAttributes(['class' => 'whitespace-nowrap'])
                            ->grow(false),
                        Tables\Columns\TextColumn::make('prazo')
                            ->icon('heroicon-m-calendar')
                            ->date('d/m/Y')
                            ->color('gray')
                            ->alignEnd()
                            ->sortable(),
                    ]),
                ])->space(3),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em Andamento',
                        'concluida' => 'Concluída',
                    ]),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TarefasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMetas::route('/'),
            'create' => Pages\CreateMeta::route('/create'),
            'edit' => Pages\EditMeta::route('/{record}/edit'),
        ];
    }
}
