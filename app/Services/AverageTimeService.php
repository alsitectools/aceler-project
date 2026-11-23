<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AverageTimeService
{
    public function getAllAverageTimes(int $workspaceId): array
    {
        // v3: año/mes por fecha de finalización del encargo y retraso contra la entrega prevista;
        // clave nueva para no servir la caché calculada con el criterio anterior
        return Cache::remember("average_times_v3_{$workspaceId}", 600, function () use ($workspaceId) {
            return $this->computeAverageTimes($workspaceId);
        });
    }

    private function computeAverageTimes(int $workspaceId): array
    {
        Carbon::setLocale(App::getLocale());

        // Solo encargos finalizados del espacio de trabajo
        $milestones = DB::table('milestones')
            ->join('projects', 'projects.id', '=', 'milestones.project_id')
            ->where('projects.workspace', '=', $workspaceId)
            ->whereNotNull('milestones.finalization_date')
            ->where('milestones.finalization_date', '!=', '0000-00-00')
            ->select(
                'milestones.start_date',
                'milestones.end_date',
                'milestones.planned_end_date',
                'milestones.task_start_date',
                'milestones.finalization_date'
            )
            ->get();

        $grouped = [];
        $validDate = fn($d) => !empty($d) && $d !== '0000-00-00' && !str_starts_with((string) $d, '0000-00-00');

        foreach ($milestones as $milestone) {
            if (!$validDate($milestone->finalization_date) || !$validDate($milestone->start_date)) {
                continue;
            }

            $creationDate = Carbon::parse($milestone->start_date)->startOfDay();
            $taskStartDate = $validDate($milestone->task_start_date)
                ? Carbon::parse($milestone->task_start_date)->startOfDay()
                : $creationDate;
            // Fecha objetivo: la entrega prevista y, si no hay, la deseada (igual que la columna
            // «Completado» de la tabla de Encargos). Sin ninguna, la de creación.
            $estimatedDate = $validDate($milestone->planned_end_date)
                ? Carbon::parse($milestone->planned_end_date)->startOfDay()
                : ($validDate($milestone->end_date) ? Carbon::parse($milestone->end_date)->startOfDay() : $creationDate);
            $finalizationDate = Carbon::parse($milestone->finalization_date)->startOfDay();

            $deliveryTime = $creationDate->diffInDays($finalizationDate);
            $startUpTime = $taskStartDate->lt($creationDate)
                ? 0
                : min($creationDate->diffInDays($taskStartDate), $deliveryTime);
            $delayTime = max(0, $estimatedDate->diffInDays($finalizationDate, false));
            $workingTime = max(0, $deliveryTime - $startUpTime - $delayTime);

            // Año, mes y trimestre: los de la finalización del encargo («encargos finalizados en marzo»).
            // Antes el año salía del inicio del proyecto y el mes de la creación del encargo.
            $year = $finalizationDate->format('Y');
            if (!isset($grouped[$year])) {
                $grouped[$year] = [
                    'months' => [],
                    'quarters' => [],
                    'yearly' => $this->newPeriod(),
                ];
            }

            $month = $finalizationDate->translatedFormat('F');
            $quarter = 'Q' . ceil((int) $finalizationDate->format('n') / 3);

            if (!isset($grouped[$year]['months'][$month])) {
                $grouped[$year]['months'][$month] = $this->newPeriod();
            }
            $this->accumulate($grouped[$year]['months'][$month], $startUpTime, $workingTime, $delayTime);

            if (!isset($grouped[$year]['quarters'][$quarter])) {
                $grouped[$year]['quarters'][$quarter] = $this->newPeriod();
            }
            $this->accumulate($grouped[$year]['quarters'][$quarter], $startUpTime, $workingTime, $delayTime);

            $this->accumulate($grouped[$year]['yearly'], $startUpTime, $workingTime, $delayTime);
        }

        foreach ($grouped as &$yearData) {
            $yearData['yearly'] = $this->finalizePeriod($yearData['yearly']);

            foreach ($yearData['months'] as &$monthData) {
                $monthData = $this->finalizePeriod($monthData);
            }
            unset($monthData);

            foreach ($yearData['quarters'] as &$quarterData) {
                $quarterData = $this->finalizePeriod($quarterData);
            }
            unset($quarterData);
        }
        unset($yearData);

        return $grouped;
    }

    private function newPeriod(): array
    {
        return [
            'count' => 0,
            'sumStartUp' => 0,
            'sumWorking' => 0,
            'sumDelay' => 0,
        ];
    }

    private function accumulate(array &$period, int $startUp, int $working, int $delay): void
    {
        $period['count']++;
        $period['sumStartUp'] += $startUp;
        $period['sumWorking'] += $working;
        $period['sumDelay'] += $delay;
    }

    private function finalizePeriod(array $period): array
    {
        if ($period['count'] > 0) {
            $period['averageStartUp'] = round($period['sumStartUp'] / $period['count']);
            $period['averageWorking'] = round($period['sumWorking'] / $period['count']);
            $period['averageDelay'] = round($period['sumDelay'] / $period['count']);
        } else {
            $period['averageStartUp'] = 0;
            $period['averageWorking'] = 0;
            $period['averageDelay'] = 0;
        }

        // Se conserva «count»: la vista distingue «sin finalizados» de «finalizados con tiempo medio 0 días»
        unset($period['sumStartUp'], $period['sumWorking'], $period['sumDelay']);

        return $period;
    }
}