<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Oportunidades extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Produtividade';

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;
}
