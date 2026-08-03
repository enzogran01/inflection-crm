<?php

namespace App\Filament\Clusters\Financeiro\Resources;

use App\Filament\Clusters\Financeiro;
use App\Filament\Clusters\Financeiro\Resources\TransactionResource\Pages;
use App\Filament\Clusters\Financeiro\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $modelLabel = 'Transação';

    protected static ?string $pluralModelLabel = 'Transações';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Financeiro::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'receita' => 'Receita',
                        'despesa' => 'Despesa',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('R$'),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\DatePicker::make('paid_at'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'atrasado' => 'Atrasado',
                    ])
                    ->required()
                    ->default('pendente'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receita' => 'success',
                        'despesa' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BRL', 100)
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('BRL', 100)),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'pago' => 'success',
                        'atrasado' => 'danger',
                        default => 'primary',
                    }),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
