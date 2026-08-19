<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Meta;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AcompanhamentoMetasWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Acompanhamento de Metas Financeiras';

    protected function periodo(Meta $registro): array
    {
        $ref = $registro->data_referencia
            ? Carbon::parse($registro->data_referencia)
            : Carbon::now();

        return match ($registro->periodicidade) {
            'diaria' => [
                $ref->copy()->startOfDay(),
                $ref->copy()->endOfDay(),
            ],
            'semanal' => [
                $ref->copy()->startOfWeek(),
                $ref->copy()->endOfWeek(),
            ],
            'trimestral' => [
                $ref->copy()->firstOfQuarter(),
                $ref->copy()->lastOfQuarter()->endOfDay(),
            ],
            'semestral' => [
                $ref->copy()->month <= 6
                    ? $ref->copy()->startOfYear()
                    : $ref->copy()->setMonth(7)->startOfMonth(),
                $ref->copy()->month <= 6
                    ? $ref->copy()->setMonth(6)->endOfMonth()->endOfDay()
                    : $ref->copy()->endOfYear()->endOfDay(),
            ],
            'anual' => [
                $ref->copy()->startOfYear(),
                $ref->copy()->endOfYear()->endOfDay(),
            ],
            default => [
                $ref->copy()->startOfMonth(),
                $ref->copy()->endOfMonth()->endOfDay(),
            ],
        };
    }

    protected function progresso(Meta $registro): array
    {
        [$inicio, $fim] = $this->periodo($registro);
        return \App\Services\MetaProgressoService::calcular($registro, $inicio, $fim);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Meta::query()->where('categoria', 'financeira')
            )
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tipo_meta_financeira')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'faturamento' => 'Faturamento',
                        'reducao_despesa' => 'Redução de Despesa',
                        'reducao_inadimplencia' => 'Redução Inadimplência',
                        'margem_operacional' => 'Margem Operacional',
                        'economia' => 'Economia',
                        'receita' => 'Receita',
                        'despesa' => 'Despesa',
                        'lucro' => 'Lucro',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('valor_alvo')
                    ->label('Alvo')
                    ->formatStateUsing(function (Meta $record) {
                        if (in_array($record->tipo_meta_financeira, ['margem_operacional', 'reducao_inadimplencia'])) {
                            return number_format($record->valor_alvo, 1, ',', '.') . '%';
                        }
                        return 'R$ ' . number_format($record->valor_alvo, 2, ',', '.');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_atual')
                    ->label('Atual')
                    ->state(fn (Meta $registro): float => $this->progresso($registro)['valor_atual'])
                    ->formatStateUsing(function ($state, Meta $record) {
                        if (in_array($record->tipo_meta_financeira, ['margem_operacional', 'reducao_inadimplencia'])) {
                            return number_format($state, 1, ',', '.') . '%';
                        }
                        return 'R$ ' . number_format($state, 2, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('periodicidade')
                    ->label('Período')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'diaria' => 'Diária',
                        'semanal' => 'Semanal',
                        'mensal' => 'Mensal',
                        'trimestral' => 'Trimestral',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                        default => 'Mensal',
                    })
                    ->color('info'),
                Tables\Columns\TextColumn::make('progresso')
                    ->label('Progresso')
                    ->state(fn (Meta $registro): int => (int) $this->progresso($registro)['percentual'])
                    ->formatStateUsing(fn (int $state): string => $state . '%')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(query: function ($query, string $direction) {
                        $query->orderByRaw(
                            'COALESCE(valor_alvo, 0) ' . $direction
                        );
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em andamento',
                        'concluida' => 'Concluída',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'danger',
                        'em_andamento' => 'warning',
                        'concluida' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_meta_financeira')
                    ->label('Tipo')
                    ->options([
                        'faturamento' => 'Faturamento',
                        'reducao_despesa' => 'Redução de Despesa',
                        'reducao_inadimplencia' => 'Redução de Inadimplência',
                        'margem_operacional' => 'Margem Operacional',
                        'economia' => 'Economia',
                        'receita' => 'Receita (Legado)',
                        'despesa' => 'Despesa (Legado)',
                        'lucro' => 'Lucro (Legado)',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em Andamento',
                        'concluida' => 'Concluída',
                    ]),
                Tables\Filters\SelectFilter::make('periodicidade')
                    ->label('Periodicidade')
                    ->options([
                        'diaria' => 'Diária',
                        'semanal' => 'Semanal',
                        'mensal' => 'Mensal',
                        'trimestral' => 'Trimestral',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->url(fn (Meta $registro): string =>
                            \App\Filament\Clusters\Tarefas\Resources\MetaResource::getUrl('edit', ['record' => $registro])
                        ),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([5]);
    }
}
