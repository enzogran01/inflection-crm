<?php

namespace App\Filament\Clusters\Financeiro\Pages;

use App\Filament\Clusters\Financeiro;
use App\Filament\Clusters\Financeiro\Widgets\SaudeFinanceiraWidget;
use App\Filament\Clusters\Financeiro\Widgets\AcompanhamentoMetasWidget;
use Filament\Pages\Page;

class MetasSaude extends Page
{
    protected static ?string $cluster = Financeiro::class;

    protected static ?string $title = 'Metas e Saúde';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.clusters.financeiro.pages.metas-saude';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Clusters\Financeiro\Widgets\SaudeFinanceiraWidget::class,
            \App\Filament\Clusters\Financeiro\Widgets\HealthScoreWidget::class,
            \App\Filament\Clusters\Financeiro\Widgets\AcompanhamentoMetasWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }
}
