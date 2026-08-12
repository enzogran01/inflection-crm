<?php

namespace App\Filament\Clusters\Financeiro\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class TransactionCashflowChart extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.transaction-cashflow-chart';

    public ?string $date_range = null;

    public function mount(): void
    {
        $this->date_range = Carbon::now()->startOfMonth()->format('d/m/Y') . ' - ' . Carbon::now()->endOfMonth()->format('d/m/Y');
        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make('date_range')
                    ->label('')
                    ->placeholder('Selecione o período')
                    ->displayFormat('DD/MM/YYYY')
                    ->format('d/m/Y')
                    ->live()
                    ->extraAttributes(['class' => 'min-w-[250px]'])
            ]);
    }

    public function getHeading(): string
    {
        return 'Fluxo de Caixa';
    }

    protected function getData(): array
    {
        $labels = [];
        $receitasData = [];
        $despesasData = [];

        if (empty($this->date_range)) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } else {
            $dates = explode(' - ', $this->date_range);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
            } else {
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
            }
        }

        $receitas = Transaction::where('type', 'receita')
            ->where('status', 'pago')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->get();

        $despesas = Transaction::where('type', 'despesa')
            ->where('status', 'pago')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->get();

        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays > 60) {
            $receitas = $receitas->groupBy(fn($t) => Carbon::parse($t->paid_at)->format('Y-m'));
            $despesas = $despesas->groupBy(fn($t) => Carbon::parse($t->paid_at)->format('Y-m'));

            $currentDate = $startDate->copy()->startOfMonth();
            $endMonth = $endDate->copy()->endOfMonth();

            while ($currentDate->lte($endMonth)) {
                $monthKey = $currentDate->format('Y-m');
                $labels[] = ucfirst($currentDate->translatedFormat('M/Y'));
                $receitasData[] = ($receitas->get($monthKey)?->sum('amount') ?? 0) / 100;
                $despesasData[] = ($despesas->get($monthKey)?->sum('amount') ?? 0) / 100;
                $currentDate->addMonth();
            }
        } else {
            $receitas = $receitas->groupBy(fn($t) => Carbon::parse($t->paid_at)->format('Y-m-d'));
            $despesas = $despesas->groupBy(fn($t) => Carbon::parse($t->paid_at)->format('Y-m-d'));

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateKey = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('d/m');
                $receitasData[] = ($receitas->get($dateKey)?->sum('amount') ?? 0) / 100;
                $despesasData[] = ($despesas->get($dateKey)?->sum('amount') ?? 0) / 100;
                $currentDate->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entradas',
                    'data' => $receitasData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Saídas',
                    'data' => $despesasData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
