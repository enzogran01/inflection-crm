<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Financeiro extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Produtividade';

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;
}
