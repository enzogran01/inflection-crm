<?php

namespace App\Filament\Clusters\Oportunidades\Resources;

use App\Filament\Clusters\Oportunidades;
use App\Filament\Clusters\Oportunidades\Resources\ClienteResource\Pages;
use App\Filament\Clusters\Oportunidades\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource implements \BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

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
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('descricao')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('inicio_contato'),
                Forms\Components\TextInput::make('contato')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inicio_contato')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\Filter::make('inicio_contato')
                    ->form([
                        Forms\Components\DatePicker::make('inicio_contato_de')->label('Início Contato (De)'),
                        Forms\Components\DatePicker::make('inicio_contato_ate')->label('Início Contato (Até)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['inicio_contato_de'],
                                fn (Builder $query, $date): Builder => $query->whereDate('inicio_contato', '>=', $date),
                            )
                            ->when(
                                $data['inicio_contato_ate'],
                                fn (Builder $query, $date): Builder => $query->whereDate('inicio_contato', '<=', $date),
                            );
                    })
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
        ];
    }
}
