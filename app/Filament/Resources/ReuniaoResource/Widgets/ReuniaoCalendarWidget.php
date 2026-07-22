<?php

namespace App\Filament\Resources\ReuniaoResource\Widgets;

use App\Models\Reuniao;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use Illuminate\Support\Facades\Auth;

class ReuniaoCalendarWidget extends FullCalendarWidget
{
    public string | null | \Illuminate\Database\Eloquent\Model $model = Reuniao::class;
    
    public \Illuminate\Database\Eloquent\Model | int | string | null $record = null;

    public function fetchEvents(array $fetchInfo): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Só busca as reuniões que o usuário tem acesso se não for para ver tudo
        $query = Reuniao::query()
            ->where('inicio', '>=', $fetchInfo['start'])
            ->where('fim', '<=', $fetchInfo['end']);

        // Se o usuário não tem a permissão view_any global, filtra pelas dele
        if (!$user->can('view_any_reuniao')) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('participantes', function ($sub) use ($user) {
                    $sub->where('users.id', $user->id);
                })
                ->orWhereHas('cargos', function ($sub) use ($user) {
                    $sub->whereIn('roles.id', $user->roles()->pluck('id'));
                });
            });
        }

        return $query->get()
            ->map(
                fn (Reuniao $reuniao) => array_merge(
                    EventData::make()
                        ->id($reuniao->id)
                        ->title($reuniao->titulo)
                        ->start($reuniao->inicio)
                        ->end($reuniao->fim)
                        ->backgroundColor(match ($reuniao->status) {
                            'agendada' => '#f59e0b', // warning / yellow
                            'concluida' => '#10b981', // success / green
                            'cancelada' => '#ef4444', // danger / red
                            default => '#3b82f6', // primary / blue
                        })
                        ->toArray(),
                    ['classNames' => ['cursor-pointer']]
                )
            )
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Grid::make(2)
                ->schema(
                    \App\Filament\Resources\ReuniaoResource::form(
                        \Filament\Forms\Form::make($this)
                    )->getComponents()
                )
        ];
    }

    public function resolveEventRecord(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Using the Model class to resolve
        return Reuniao::find($data['id']);
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $record = $this->resolveEventRecord($event);

        if (! auth()->user()->can('update_reuniao', $record)) {
            return true; // revert
        }

        $record->update([
            'inicio' => $event['start'],
            'fim' => $event['end'] ?? $event['start'],
        ]);

        return false;
    }

    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        $record = $this->resolveEventRecord($event);

        if (! auth()->user()->can('update_reuniao', $record)) {
            return true; // revert
        }

        $record->update([
            'inicio' => $event['start'],
            'fim' => $event['end'] ?? $event['start'],
        ]);

        return false;
    }

    protected function headerActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\CreateAction::make()
                ->modalHeading('Criar Reunião')
                ->model(Reuniao::class)
                ->mountUsing(function (\Filament\Forms\Form $form, array $arguments) {
                    $inicio = isset($arguments['start']) ? \Carbon\Carbon::parse($arguments['start']) : null;
                    $fim = isset($arguments['end']) ? \Carbon\Carbon::parse($arguments['end']) : null;

                    if ($inicio && $fim && ($arguments['allDay'] ?? false)) {
                        $inicio->startOfDay(); // Define 00:00:00
                        $fim->startOfDay()->addHour(); // Define 01:00:00 no dia de fim
                    }

                    $form->fill([
                        'inicio' => $inicio ? $inicio->format('Y-m-d H:i:s') : null,
                        'fim' => $fim ? $fim->format('Y-m-d H:i:s') : null,
                    ]);
                })
                ->visible(fn () => auth()->user()->can('create_reuniao')),
        ];
    }

    protected function modalActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\EditAction::make()
                ->modalHeading('Editar Reunião')
                ->visible(fn (\Illuminate\Database\Eloquent\Model $record) => auth()->user()->can('update_reuniao', $record)),
            \Saade\FilamentFullCalendar\Actions\DeleteAction::make()
                ->modalHeading('Excluir Reunião')
                ->visible(fn (\Illuminate\Database\Eloquent\Model $record) => auth()->user()->can('delete_reuniao', $record)),
        ];
    }

    protected function viewAction(): \Filament\Actions\Action
    {
        return \Saade\FilamentFullCalendar\Actions\ViewAction::make()
            ->modalHeading('Visualizar Reunião')
            ->extraModalFooterActions(fn (\Illuminate\Database\Eloquent\Model $record): array => [
                \Saade\FilamentFullCalendar\Actions\EditAction::make()
                    ->modalHeading('Editar Reunião')
                    ->cancelParentActions()
                    ->visible(fn () => auth()->user()->can('update_reuniao', $record)),
                \Saade\FilamentFullCalendar\Actions\DeleteAction::make()
                    ->modalHeading('Excluir Reunião')
                    ->cancelParentActions()
                    ->visible(fn () => auth()->user()->can('delete_reuniao', $record)),
            ]);
    }

    public function config(): array
    {
        return [
            'height' => 550, // reduz o tamanho/altura padrão do calendário
            'initialView' => 'dayGridMonth',
            'editable' => auth()->user()->can('create_reuniao'),
            'selectable' => true,
        ];
    }
}
