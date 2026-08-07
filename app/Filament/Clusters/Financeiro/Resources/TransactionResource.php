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

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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
                Forms\Components\Select::make('category')
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
                    ->required()
                    ->numeric()
                    ->prefix('R$')
                    ->step('0.01')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2, '.', '') : null)
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) round((float) $state * 100) : null),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\DatePicker::make('paid_at'),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'credito' => 'Crédito',
                        'debito' => 'Débito',
                        'pix' => 'PIX',
                        'dinheiro' => 'Dinheiro',
                        'boleto' => 'Boleto',
                    ])
                    ->label('Método de Pagamento')
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receita' => 'Receita',
                        'despesa' => 'Despesa',
                    ])
                    ->label('Tipo'),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Infraestrutura' => 'Infraestrutura',
                        'Folha de Pagamento' => 'Folha de Pagamento',
                        'Marketing' => 'Marketing',
                        'Impostos' => 'Impostos',
                        'Serviços' => 'Serviços',
                        'Vendas' => 'Vendas',
                        'Outros' => 'Outros',
                    ])
                    ->label('Categoria')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'credito' => 'Crédito',
                        'debito' => 'Débito',
                        'pix' => 'PIX',
                        'dinheiro' => 'Dinheiro',
                        'boleto' => 'Boleto',
                    ])
                    ->label('Método de Pagamento'),
                Tables\Filters\TernaryFilter::make('is_standard')
                    ->label('É recorrente?')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('recurring_transaction_id'),
                        false: fn (Builder $query) => $query->whereNull('recurring_transaction_id'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markAsPaid')
                        ->label('Marcar como Pago')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Transaction $record) {
                            $record->update([
                                'status' => 'pago',
                                'paid_at' => now(),
                            ]);
                        })
                        ->requiresConfirmation()
                        ->visible(fn (Transaction $record): bool => $record->status !== 'pago'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Marcar como Pago')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, Tables\Actions\BulkAction $action) {
                            if ($records->contains('status', 'pago')) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Ação cancelada')
                                    ->body('Uma ou mais transações selecionadas já estão pagas.')
                                    ->send();
                                
                                $action->halt();
                            }

                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'pago',
                                    'paid_at' => now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
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
