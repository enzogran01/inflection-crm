<?php

namespace App\Filament\Clusters\Financeiro\Resources\RecurringTransactionResource\Pages;

use App\Filament\Clusters\Financeiro\Resources\RecurringTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringTransaction extends CreateRecord
{
    protected static string $resource = RecurringTransactionResource::class;
}
