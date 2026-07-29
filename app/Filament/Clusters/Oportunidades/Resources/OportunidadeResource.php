<?php

namespace App\Filament\Clusters\Oportunidades\Resources;

use App\Filament\Clusters\Oportunidades;
use App\Filament\Clusters\Oportunidades\Resources\OportunidadeResource\Pages;
use App\Filament\Clusters\Oportunidades\Resources\OportunidadeResource\RelationManagers;
use App\Models\Oportunidade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OportunidadeResource extends Resource implements \BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions
{
    protected static ?string $model = Oportunidade::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $modelLabel = 'Oportunidade';
    protected static ?string $pluralModelLabel = 'Oportunidades';

    protected static ?string $cluster = Oportunidades::class;

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('cliente_id')
                    ->relationship('cliente', 'nome')
                    ->searchable()
                    ->preload(),
                Forms\Components\RichEditor::make('descricao')
                    ->columnSpanFull(),
                Forms\Components\Select::make('categoria')
                    ->options([
                        'evento' => 'Evento',
                        'cliente' => 'Cliente',
                        'parceria' => 'Parceria',
                    ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'novo' => 'Novo',
                        'em negociação' => 'Em negociação',
                        'revisão' => 'Revisão',
                        'fechado' => 'Fechado',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('data_fechamento_esperada'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoria')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'novo' => 'gray',
                        'em negociação' => 'primary',
                        'revisão' => 'warning',
                        'fechado' => 'success',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_fechamento_esperada')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'evento' => 'Evento',
                        'cliente' => 'Cliente',
                        'parceria' => 'Parceria',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'novo' => 'Novo',
                        'em negociação' => 'Em negociação',
                        'revisão' => 'Revisão',
                        'fechado' => 'Fechado',
                    ]),
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->relationship('cliente', 'nome')
                    ->label('Cliente')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOportunidades::route('/'),
            'create' => Pages\CreateOportunidade::route('/create'),
        ];
    }
}
