<?php

namespace App\Filament\Clusters\Financeiro\Resources;

use App\Filament\Clusters\Financeiro;
use App\Filament\Clusters\Financeiro\Resources\RecurringTransactionResource\Pages;
use App\Models\RecurringTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecurringTransactionResource extends Resource
{
    protected static ?string $model = RecurringTransaction::class;

    protected static ?string $modelLabel = 'Transação Padrão';

    protected static ?string $pluralModelLabel = 'Transações Padrão';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $cluster = Financeiro::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'receita' => 'Receita',
                        'despesa' => 'Despesa',
                    ])
                    ->required(),
                Forms\Components\Select::make('category')
                    ->label('Categoria')
                    ->options([
                        'Infraestrutura' => 'Infraestrutura',
                        'Folha de Pagamento' => 'Folha de Pagamento',
                        'Marketing' => 'Marketing',
                        'Impostos' => 'Impostos',
                        'Serviços' => 'Serviços',
                        'Vendas' => 'Vendas',
                        'Outros' => 'Outros',
                    ])
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('amount')
                    ->label('Valor')
                    ->required()
                    ->numeric()
                    ->prefix('R$')
                    ->step('0.01')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2, '.', '') : null)
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) round((float) $state * 100) : null),
                Forms\Components\Select::make('periodicity')
                    ->label('Periodicidade')
                    ->options([
                        'mensal' => 'Mensal',
                        'anual' => 'Anual',
                    ])
                    ->required()
                    ->default('mensal'),
                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pagamento')
                    ->options([
                        'credito' => 'Crédito',
                        'debito' => 'Débito',
                        'pix' => 'PIX',
                        'dinheiro' => 'Dinheiro',
                        'boleto' => 'Boleto',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('due_day')
                    ->label('Dia de Vencimento')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->nullable(),
                Forms\Components\Select::make('due_month')
                    ->label('Mês de Vencimento (Anual)')
                    ->options([
                        1 => 'Janeiro',
                        2 => 'Fevereiro',
                        3 => 'Março',
                        4 => 'Abril',
                        5 => 'Maio',
                        6 => 'Junho',
                        7 => 'Julho',
                        8 => 'Agosto',
                        9 => 'Setembro',
                        10 => 'Outubro',
                        11 => 'Novembro',
                        12 => 'Dezembro',
                    ])
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receita' => 'success',
                        'despesa' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', 100)
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodicity')
                    ->label('Periodicidade')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pagamento')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucfirst($state) : null),
                Tables\Columns\TextColumn::make('due_day')
                    ->label('Dia do Vencimento'),
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
            'index' => Pages\ListRecurringTransactions::route('/'),
            'create' => Pages\CreateRecurringTransaction::route('/create'),
            'edit' => Pages\EditRecurringTransaction::route('/{record}/edit'),
        ];
    }
}
