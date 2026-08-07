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
                RecurringTransaction::pendingForCurrentMonth()
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
                        $record->generateTransactionForCurrentMonth();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Transação do Mês')
                    ->modalDescription('Uma nova transação será gerada no mês atual com status Pendente.'),
            ]);
    }
}
