<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Tarefas extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    
    protected static ?string $navigationGroup = 'Produtividade';

    protected static \Filament\Pages\SubNavigationPosition $subNavigationPosition = \Filament\Pages\SubNavigationPosition::Top;
}
