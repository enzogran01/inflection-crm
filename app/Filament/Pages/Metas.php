<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Metas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static string $view = 'filament.pages.metas';
    
    protected static ?string $cluster = \App\Filament\Clusters\Tarefas::class;
}
