<?php

namespace App\Filament\Clusters\Financeiro\Resources\TransactionResource\Pages;

use App\Filament\Clusters\Financeiro\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => \Filament\Resources\Components\Tab::make('Todos'),
            'pendentes' => \Filament\Resources\Components\Tab::make('Pendentes')
                ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'pendente')),
            'atrasados' => \Filament\Resources\Components\Tab::make('Atrasados')
                ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'atrasado'))
                ->badgeColor('danger'),
            'pagos' => \Filament\Resources\Components\Tab::make('Pagos')
                ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'pago')),
        ];
    }
}
