<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PendingRecurringTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RecurringTransaction::query()
                    ->whereDoesntHave('transactions', function (Builder $query) {
                        $query->whereMonth('due_date', Carbon::now()->month)
                              ->whereYear('due_date', Carbon::now()->year);
                    })
                    ->where(function ($query) {
                        $query->where('periodicity', 'mensal')
                              ->orWhere(function ($query) {
                                  $query->where('periodicity', 'anual')
                                        ->where('due_month', Carbon::now()->month);
                              });
                    })
            )
            ->heading('Contas Recorrentes Disponíveis (' . Carbon::now()->translatedFormat('F/Y') . ')')
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receita' => 'success',
                        'despesa' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', 100),
                Tables\Columns\TextColumn::make('due_day')
                    ->label('Dia de Vencimento')
                    ->formatStateUsing(fn ($state) => $state ? $state : 'Não definido'),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_transaction')
                    ->label('Gerar')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->action(function (RecurringTransaction $record) {
                        $dueDate = Carbon::now();
                        if ($record->due_day) {
                            $dueDate->setDay(min($record->due_day, $dueDate->daysInMonth));
                        }
                        
                        Transaction::create([
                            'description' => $record->description,
                            'type' => $record->type,
                            'category' => $record->category,
                            'amount' => $record->amount,
                            'due_date' => $dueDate,
                            'status' => 'pendente',
                            'payment_method' => $record->payment_method,
                            'recurring_transaction_id' => $record->id,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Transação do Mês')
                    ->modalDescription('Uma nova transação será gerada no mês atual com status Pendente.'),
            ]);
    }
}
