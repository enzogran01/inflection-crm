<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Kanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static string $view = 'filament.pages.kanban';
    
    protected static ?string $cluster = \App\Filament\Clusters\Tarefas::class;
}
