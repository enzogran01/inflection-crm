<?php

namespace App\Filament\Clusters\Financeiro\Resources\TransactionResource\Pages;

use App\Filament\Clusters\Financeiro\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
