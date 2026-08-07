<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class RecentPaidTransactionsTable extends BaseWidget
{
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->where('status', 'pago')
                    ->orderByDesc('paid_at')
                    ->orderByDesc('updated_at')
                    ->limit(5)
            )
            ->heading('Últimas Movimentações')
            ->description('Últimas 5 contas pagas/recebidas')
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
                    ->money('BRL', 100)
                    ->color(fn ($record) => $record->type === 'receita' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Data Pagto.')
                    ->date('d/m/Y'),
            ])
            ->paginated(false);
    }
}
