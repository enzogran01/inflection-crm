<?php

namespace App\Filament\Clusters\Financeiro\Pages;

use App\Filament\Clusters\Financeiro;
use Filament\Pages\Dashboard;

class FinanceiroDashboard extends Dashboard
{
    protected static ?string $cluster = Financeiro::class;

    protected static ?string $title = 'Dashboard Financeiro';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $routePath = 'dashboard';

    protected static ?int $navigationSort = -1; // Put it at the top of the cluster

    public function getWidgets(): array
    {
        return [
            \App\Filament\Clusters\Financeiro\Widgets\TransactionStats::class,
            \App\Filament\Clusters\Financeiro\Widgets\TransactionCashflowChart::class,
            \App\Filament\Clusters\Financeiro\Widgets\TransactionExpensesChart::class,
            \App\Filament\Clusters\Financeiro\Widgets\DueSoonTransactionsTable::class,
            \App\Filament\Clusters\Financeiro\Widgets\RecentPaidTransactionsTable::class,
            \App\Filament\Clusters\Financeiro\Widgets\PendingRecurringTransactionsWidget::class,
        ];
    }
}
