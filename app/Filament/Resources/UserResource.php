<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $navigationGroup = 'Administração';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->image()
                    ->avatar()
                    ->directory('avatars')
                    ->label('Foto de Perfil'),
                
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),
                    
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('E-mail'),
                    
                Forms\Components\TextInput::make('telefone')
                    ->label('Telefone')
                    ->mask('(99) 99999-9999')
                    ->stripCharacters(['(', ')', '-', ' '])
                    ->numeric() 
                    ->required(),
                    
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->formatStateUsing(fn () => null)
                    ->label('Senha'),
                    
                Select::make('roles')
                    ->relationship(
                        name: 'roles', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereIn('name', ['Administrador', 'Gestor', 'Colaborador'])
                    )
                    ->multiple()
                    ->maxItems(1)
                    ->preload()
                    ->searchable()
                    ->label('Cargo')
                    ->required(),
                
                Select::make('area_atuacao_id')
                    ->relationship('areaAtuacao', 'nome')
                    ->label('Área de Atuação')
                    ->preload()
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Cargo')
                    ->preload(),
                SelectFilter::make('area_atuacao_id')
                    ->relationship('areaAtuacao', 'nome')
                    ->label('Área de Atuação')
                    ->preload(),
                TernaryFilter::make('telefone')
                    ->label('Possui Telefone?')
                    ->placeholder('Todos')
                    ->trueLabel('Sim')
                    ->falseLabel('Não')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('telefone')->where('telefone', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('telefone')->orWhere('telefone', ''),
                    ),
                ])
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => filament()->getUserAvatarUrl($record)),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nome')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('E-mail')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable()
                    ->label('Telefone')
                    ->formatStateUsing(function (?string $state) {
                        if (! $state) {
                            return null;
                        }
                        $numero = preg_replace('/[^0-9]/', '', $state);
                        if (strlen($numero) === 11) {
                            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $numero);
                        }
                        if (strlen($numero) === 10) {
                            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $numero);
                        }
                        return $state;
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Cargo')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Administrador' => 'primary', 
                        'Gestor'        => 'warning',  
                        'Colaborador'   => 'success',  
                        'super_admin'   => 'danger',  
                        default         => 'gray',
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('areaAtuacao.nome')
                    ->label('Área de Atuação')
                    ->badge()
                    ->color(fn ($record) => $record->areaAtuacao ? \Filament\Support\Colors\Color::hex($record->areaAtuacao->cor) : 'gray')
                    ->icon(fn ($record) => $record->areaAtuacao?->icone)
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Impersonate::make()
                        ->label('Personificar')
                        ->grouped()
                        ->visible(fn () => auth()->user()->hasRole('Administrador')),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
