<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Administracao;
use App\Filament\Resources\AreaAtuacaoResource\Pages;
use App\Filament\Resources\AreaAtuacaoResource\RelationManagers;
use App\Models\AreaAtuacao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AreaAtuacaoResource extends Resource
{
    protected static ?string $model = AreaAtuacao::class;

    protected static ?string $modelLabel = 'Área de Atuação';
    protected static ?string $pluralModelLabel = 'Áreas de Atuação';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Administração';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('cor')
                    ->label('Cor')
                    ->required(),
                Forms\Components\TextInput::make('icone')
                    ->label('Ícone (Heroicon)')
                    ->helperText('Ex: heroicon-o-academic-cap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('descricao')
                    ->label('Descrição')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icone')
                    ->label('Ícone')
                    ->icon(fn (string $state): string => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('cor')
                    ->label('Cor')
                    ->badge()
                    ->color(fn (string $state): array => \Filament\Support\Colors\Color::hex($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Usuários')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAreaAtuacaos::route('/'),
            'create' => Pages\CreateAreaAtuacao::route('/create'),
            'edit' => Pages\EditAreaAtuacao::route('/{record}/edit'),
        ];
    }
}
