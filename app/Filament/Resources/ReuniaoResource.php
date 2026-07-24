<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReuniaoResource\Pages;
use App\Filament\Resources\ReuniaoResource\RelationManagers;
use App\Models\Reuniao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReuniaoResource extends Resource
{
    protected static ?string $model = Reuniao::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $modelLabel = 'Reunião';
    protected static ?string $pluralModelLabel = 'Reuniões';
    protected static ?string $navigationGroup = 'Produtividade';
    protected static ?string $slug = 'reunioes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('descricao')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('inicio')
                    ->native(false)
                    ->seconds(false)
                    ->required(),
                Forms\Components\DateTimePicker::make('fim')
                    ->native(false)
                    ->seconds(false)
                    ->required()
                    ->after('inicio'),
                Forms\Components\Select::make('status')
                    ->options([
                        'agendada' => 'Agendada',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada',
                    ])
                    ->required()
                    ->default('agendada'),
                Forms\Components\Select::make('participantes')
                    ->multiple()
                    ->relationship('participantes', 'name')
                    ->preload(),
                Forms\Components\Select::make('cargos')
                    ->multiple()
                    ->relationship('cargos', 'name')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'agendada' => 'warning',
                        'concluida' => 'success',
                        'cancelada' => 'danger',
                        default => 'primary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'agendada' => 'Agendada',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada',
                    ]),
                Tables\Filters\SelectFilter::make('participantes')
                    ->relationship('participantes', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('cargos')
                    ->relationship('cargos', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->extraAttributes(['class' => 'hidden'])
                    ->extraModalFooterActions(fn (\Illuminate\Database\Eloquent\Model $record): array => [
                        Tables\Actions\EditAction::make()
                            ->cancelParentActions()
                            ->visible(fn () => auth()->user()->can('update_reuniao', $record)),
                        Tables\Actions\DeleteAction::make()
                            ->cancelParentActions()
                            ->visible(fn () => auth()->user()->can('delete_reuniao', $record)),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReuniaos::route('/'),
            'create' => Pages\CreateReuniao::route('/create'),
            'edit' => Pages\EditReuniao::route('/{record}/edit'),
        ];
    }
}
