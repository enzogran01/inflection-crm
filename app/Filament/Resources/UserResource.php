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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';

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
                    ->tel()
                    ->maxLength(20),
                    
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Senha'),
                    
                // Aqui entra o Dropdown de Cargos
                Select::make('roles')
                    ->relationship(
                        name: 'roles', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereIn('name', ['Administrador', 'Gestor', 'Colaborador'])
                    )
                    ->multiple() // Necessário para a relação do Spatie
                    ->maxItems(1) // Limita a apenas 1 escolha, funcionando como um dropdown único
                    ->preload()
                    ->searchable()
                    ->label('Cargo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => filament()->getUserAvatarUrl($record)),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nome'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('E-mail'),
                    
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable()
                    ->label('Telefone'),
                    
                // Coluna de Cargos com as cores
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Cargo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Administrador' => 'danger',   // Vermelho
                        'Gestor'        => 'warning',  // Amarelo/Laranja
                        'Colaborador'   => 'success',  // Verde
                        'super_admin'   => 'primary',  // Azul (para o seu usuário principal)
                        default         => 'gray',
                    })
                    ->searchable(),
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
