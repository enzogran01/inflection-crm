<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class DueSoonTransactionsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->where('status', 'pendente')
                    ->whereBetween('due_date', [
                        Carbon::today(),
                        Carbon::tomorrow()->endOfDay(),
                    ])
                    ->orderBy('due_date')
            )
            ->heading('Contas Vencendo Hoje e Amanhã')
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', 100),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->color(fn ($record) => Carbon::parse($record->due_date)->isToday() ? 'danger' : 'warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Marcar como Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Transaction $record) {
                        $record->update([
                            'status' => 'pago',
                            'paid_at' => Carbon::today(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Pagamento')
                    ->modalDescription('Tem certeza que deseja marcar esta conta como paga?'),
            ]);
    }
}
