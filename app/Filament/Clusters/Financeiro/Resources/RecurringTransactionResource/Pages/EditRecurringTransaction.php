<?php

namespace App\Filament\Clusters\Financeiro\Resources\RecurringTransactionResource\Pages;

use App\Filament\Clusters\Financeiro\Resources\RecurringTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurringTransaction extends EditRecord
{
    protected static string $resource = RecurringTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
