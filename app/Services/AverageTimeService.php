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
        return Cache::remember("average_times_{$workspaceId}", 600, function () use ($workspaceId) {
            return $this->computeAverageTimes($workspaceId);
        });
    }

    private function computeAverageTimes(int $workspaceId): array
    {
        Carbon::setLocale(App::getLocale());

        $milestones = DB::table('milestones')
            ->join('projects', 'projects.id', '=', 'milestones.project_id')
            ->where('projects.workspace', '=', $workspaceId)
            ->whereNotNull('projects.start_date')
            ->select(
                'projects.start_date as project_start_date',
                'milestones.start_date',
                'milestones.end_date',
                'milestones.task_start_date',
                'milestones.finalization_date'
            )
            ->get();

        $grouped = [];

        foreach ($milestones as $milestone) {
            $year = date('Y', strtotime($milestone->project_start_date));

            if (!isset($grouped[$year])) {
                $grouped[$year] = [
                    'months' => [],
                    'quarters' => [],
                    'yearly' => $this->newPeriod(),
                ];
            }

            if (!$milestone->finalization_date || $milestone->finalization_date === '0000-00-00') {
                continue;
            }

            $creationDate = Carbon::parse($milestone->start_date);
            $taskStartDate = $milestone->task_start_date && $milestone->task_start_date !== '0000-00-00'
                ? Carbon::parse($milestone->task_start_date)
                : $creationDate;
            $estimatedDate = $milestone->end_date && $milestone->end_date !== '0000-00-00'
                ? Carbon::parse($milestone->end_date)
                : $creationDate;
            $finalizationDate = Carbon::parse($milestone->finalization_date);

            $deliveryTime = $creationDate->diffInDays($finalizationDate);
            $startUpTime = $taskStartDate->lt($creationDate)
                ? 0
                : min($creationDate->diffInDays($taskStartDate), $deliveryTime);
            $delayTime = max(0, $estimatedDate->diffInDays($finalizationDate, false));
            $workingTime = max(0, $deliveryTime - $startUpTime - $delayTime);

            $month = Carbon::parse($milestone->start_date)->translatedFormat('F');
            $quarter = 'Q' . ceil(date('n', strtotime($milestone->start_date)) / 3);

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

        unset($period['count'], $period['sumStartUp'], $period['sumWorking'], $period['sumDelay']);

        return $period;
    }
}