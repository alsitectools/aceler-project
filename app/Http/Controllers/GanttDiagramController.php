<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GanttDiagramController extends Controller
{
    /**
     * #1 - Filter by assigned user
     * #2 - Date range filter
     * #12 - Persist filters in URL (handled from frontend, backend receives params)
     */
    public function index()
    {
        $user = Auth::user();
        $currentWorkspace = Workspace::find($user->currant_workspace);

        $projects = Project::where('workspace', $currentWorkspace->id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Users for assign_to filter (#1)
        $users = User::join('user_workspaces', 'users.id', '=', 'user_workspaces.user_id')
            ->where('user_workspaces.workspace_id', $currentWorkspace->id)
            ->orderBy('users.name')
            ->select('users.id', 'users.name')
            ->distinct()
            ->get();

        return view('projects.gantt_diagram', compact('currentWorkspace', 'projects', 'users'));
    }

    /**
     * AJAX endpoint returning hierarchical Gantt JSON.
     * Improvements implemented:
     * #1 - Filter by assigned user
     * #2 - Filter by date range
     * #4 - Delay indicator (overdue class)
     * #5 - Real task progress (logged vs estimated hours)
     * #7 - Cache data per workspace+filters
     * #8 - Total hours per milestone in popup
     */
    public function getData(Request $request)
    {
        $user = Auth::user();
        $currentWorkspace = Workspace::find($user->currant_workspace);

        // #7 - Build cache key from all filter params
        $cacheKey = 'gantt_' . $currentWorkspace->id . '_' . md5($request->fullUrl());

        return Cache::remember($cacheKey, 90, function () use ($request, $currentWorkspace) {
            return $this->buildGanttData($request, $currentWorkspace);
        });
    }

    private function buildGanttData(Request $request, Workspace $currentWorkspace)
    {
        $query = Project::where('workspace', $currentWorkspace->id)
            ->where('is_active', 1);

        // Filter by project(s) — supports comma-separated IDs
        if ($request->filled('project_id')) {
            $projectIds = array_filter(explode(',', $request->project_id));
            if (count($projectIds) === 1) {
                $query->where('id', $projectIds[0]);
            } else {
                $query->whereIn('id', $projectIds);
            }
        }

        // Filter by project status — supports comma-separated values
        if ($request->filled('status')) {
            $statuses = array_filter(explode(',', $request->status));
            if (count($statuses) === 1) {
                $query->where('status', $statuses[0]);
            } else {
                $query->whereIn('status', $statuses);
            }
        }

        // #2 - Filter by date range
        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->where('start_date', '>=', $request->date_from)
                    ->orWhere('end_date', '>=', $request->date_from)
                    ->orWhereNull('start_date');
            });
        }
        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->where('start_date', '<=', $request->date_to)
                    ->orWhereNull('start_date');
            });
        }

        $milestoneConstraints = function ($q) use ($request) {
            $q->orderBy('order');
            // #1 - Filter milestones by assigned user(s) — supports comma-separated IDs
            if ($request->filled('assigned_to')) {
                $userIds = array_filter(explode(',', $request->assigned_to));
                $q->where(function ($mq) use ($userIds) {
                    $mq->whereIn('assign_to', $userIds)
                        ->orWhereIn('milestone_assigned_to_user', $userIds);
                });
            }
        };

        $projects = $query->with([
            'milestones' => $milestoneConstraints,
            'milestones.tasks' => function ($q) {
                $q->select('tasks.*')
                    ->addSelect(DB::raw('(SELECT SUM(TIME_TO_SEC(t.time)) FROM timesheets t WHERE t.task_id = tasks.id) as total_logged_seconds'))
                    ->addSelect(DB::raw('(SELECT MIN(t.date) FROM timesheets t WHERE t.task_id = tasks.id) as first_timesheet_date'));
            },
            'milestones.tasks.type',
        ])->orderBy('name')->get();

        $ganttItems = [];
        $showTasks = $request->input('show_tasks', false);
        $today = now()->format('Y-m-d');

        foreach ($projects as $project) {
            $projectStart = $project->start_date ?? $project->created_at?->format('Y-m-d');
            $projectEnd = $project->end_date;

            // Compute real span from milestones so the bar matches visible children
            if ($project->milestones->isNotEmpty()) {
                $computedStart = $this->computeProjectStartFromMilestones($project->milestones);
                $computedEnd = $this->computeProjectEndFromMilestones($project->milestones);

                // Use the earliest between project start_date and milestone start
                if ($computedStart) {
                    $projectStart = $projectStart
                        ? min($projectStart, $computedStart)
                        : $computedStart;
                }
                // Project end = milestone span only (ignore DB end_date that extends beyond visible milestones)
                if ($computedEnd) {
                    $projectEnd = $computedEnd;
                }
            }

            if (!$projectStart) {
                $projectStart = $today;
            }
            if (!$projectEnd) {
                $projectEnd = Carbon::parse($projectStart)->addDays(30)->format('Y-m-d');
            }

            // Guard: end must never be before start (causes negative SVG width)
            if (Carbon::parse($projectEnd)->lt(Carbon::parse($projectStart))) {
                $projectEnd = $projectStart;
            }

            $totalMilestones = $project->milestones->count();
            $doneMilestones = $project->milestones->where('status', '4')->count();
            $projectProgress = $totalMilestones > 0 ? round(($doneMilestones / $totalMilestones) * 100) : 0;

            // #8 - Aggregate hours for the entire project
            $projectLoggedSeconds = 0;
            $projectEstimatedSeconds = 0;
            foreach ($project->milestones as $ms) {
                foreach ($ms->tasks as $t) {
                    $projectLoggedSeconds += (int) ($t->total_logged_seconds ?? 0);
                    if ($t->estimated_date && $t->start_date) {
                        $days = max(1, Carbon::parse($t->start_date)->diffInWeekdays(Carbon::parse($t->estimated_date)));
                        $projectEstimatedSeconds += $days * 8 * 3600;
                    }
                }
            }

            // Project status-based CSS class
            $projectClass = match ($project->status) {
                'Finished' => 'gantt-project gantt-project-finished',
                'OnHold'   => 'gantt-project gantt-project-onhold',
                default    => 'gantt-project',
            };

            $ganttItems[] = [
                'id' => 'project_' . $project->id,
                'name' => $project->name,
                'start' => $projectStart,
                'end' => $projectEnd,
                'progress' => $projectProgress,
                'custom_class' => $projectClass,
                'type' => 'project',
                'dependencies' => '',
                'extra' => [
                    'type' => 'project',
                    'status' => $project->status,
                    'milestones_count' => $totalMilestones,
                    'milestones_done' => $doneMilestones,
                    'slug' => $currentWorkspace->slug,
                    'project_id' => $project->id,
                    'logged_hours' => $this->formatSeconds($projectLoggedSeconds),
                ],
            ];

            foreach ($project->milestones as $milestone) {
                $msStart = $milestone->task_start_date
                    ?? $milestone->start_date
                    ?? $milestone->created_at?->format('Y-m-d');
                $msEnd = $milestone->finalization_date
                    ?? $milestone->planned_end_date
                    ?? $milestone->end_date;

                if (!$msStart) {
                    $msStart = $projectStart;
                }
                if (!$msEnd) {
                    $msEnd = Carbon::parse($msStart)->addDays(14)->format('Y-m-d');
                }

                if (Carbon::parse($msEnd)->lt(Carbon::parse($msStart))) {
                    $msEnd = $msStart;
                }

                $msProgress = $this->milestoneProgress($milestone);

                // #4 - Overdue detection
                $isOverdue = false;
                $deadlineDate = $milestone->planned_end_date ?? $milestone->end_date;
                if ($deadlineDate && (string) $milestone->status !== '4' && Carbon::parse($deadlineDate)->lt(Carbon::parse($today))) {
                    $isOverdue = true;
                }

                $msStatusClass = $isOverdue
                    ? 'gantt-ms-overdue'
                    : $this->milestoneStatusClass($milestone);

                // #8 - Aggregate hours for milestone
                $msLoggedSeconds = 0;
                $msEstimatedSeconds = 0;
                foreach ($milestone->tasks as $t) {
                    $msLoggedSeconds += (int) ($t->total_logged_seconds ?? 0);
                    if ($t->estimated_date && $t->start_date) {
                        $days = max(1, Carbon::parse($t->start_date)->diffInWeekdays(Carbon::parse($t->estimated_date)));
                        $msEstimatedSeconds += $days * 8 * 3600;
                    }
                }

                // #16 - Priority data for visual badge
                $ganttItems[] = [
                    'id' => 'milestone_' . $milestone->id,
                    'name' => '   ' . $milestone->title,
                    'start' => $msStart,
                    'end' => $msEnd,
                    'progress' => $msProgress,
                    'custom_class' => $msStatusClass,
                    'type' => 'milestone',
                    'dependencies' => 'project_' . $project->id,
                    'extra' => [
                        'type' => 'milestone',
                        'status' => $milestone->status,
                        'priority' => $milestone->priority,
                        'is_waiting' => (bool) $milestone->is_waiting,
                        'is_overdue' => $isOverdue,
                        'desired_date' => $milestone->end_date,
                        'planned_date' => $milestone->planned_end_date,
                        'task_start_date' => $milestone->task_start_date,
                        'finalization_date' => $milestone->finalization_date,
                        'logged_hours' => $this->formatSeconds($msLoggedSeconds),
                        'estimated_hours' => $this->formatSeconds($msEstimatedSeconds),
                        'slug' => $currentWorkspace->slug,
                        'project_id' => $milestone->project_id,
                    ],
                ];

                if ($showTasks) {
                    foreach ($milestone->tasks as $task) {
                        $taskStart = $task->start_date
                            ? Carbon::parse($task->start_date)->format('Y-m-d')
                            : ($task->first_timesheet_date ?? $msStart);
                        $taskEnd = $task->end_date
                            ? Carbon::parse($task->end_date)->format('Y-m-d')
                            : ($task->estimated_date
                                ? Carbon::parse($task->estimated_date)->format('Y-m-d')
                                : Carbon::parse($taskStart)->addDays(7)->format('Y-m-d'));

                        if (Carbon::parse($taskEnd)->lt(Carbon::parse($taskStart))) {
                            $taskEnd = $taskStart;
                        }

                        // #5 - Real task progress based on logged vs estimated
                        $taskProgress = $this->computeTaskProgress($task);
                        $taskName = $task->type ? $task->type->name : __('Task');

                        $ganttItems[] = [
                            'id' => 'task_' . $task->id,
                            'name' => '      ' . $taskName,
                            'start' => $taskStart,
                            'end' => $taskEnd,
                            'progress' => $taskProgress,
                            'custom_class' => 'gantt-task',
                            'type' => 'task',
                            'dependencies' => 'milestone_' . $milestone->id,
                            'extra' => [
                                'type' => 'task',
                                'logged_hours' => $task->total_logged_seconds ? $this->formatSeconds((int) $task->total_logged_seconds) : '00:00',
                                'task_id' => $task->id,
                                'slug' => $currentWorkspace->slug,
                                'project_id' => $task->project_id,
                            ],
                        ];
                    }
                }
            }
        }

        return response()->json($ganttItems);
    }

    private function computeProjectStartFromMilestones($milestones): ?string
    {
        $earliestDate = null;

        foreach ($milestones as $ms) {
            // Same coalesce as milestone bar display
            $msStart = $ms->task_start_date
                ?? $ms->start_date
                ?? $ms->created_at?->format('Y-m-d');

            if ($msStart && (!$earliestDate || Carbon::parse($msStart)->lt(Carbon::parse($earliestDate)))) {
                $earliestDate = $msStart;
            }
        }

        return $earliestDate;
    }

    private function computeProjectEndFromMilestones($milestones): ?string
    {
        $latestDate = null;

        foreach ($milestones as $ms) {
            // Same coalesce as milestone bar display: first non-null wins
            $msEnd = $ms->finalization_date
                ?? $ms->planned_end_date
                ?? $ms->end_date;

            // If no end date, use same fallback as display: start + 14 days
            if (!$msEnd) {
                $msStart = $ms->task_start_date
                    ?? $ms->start_date
                    ?? $ms->created_at?->format('Y-m-d');
                if ($msStart) {
                    $msEnd = Carbon::parse($msStart)->addDays(14)->format('Y-m-d');
                }
            }

            if ($msEnd && (!$latestDate || Carbon::parse($msEnd)->gt(Carbon::parse($latestDate)))) {
                $latestDate = $msEnd;
            }
        }

        return $latestDate;
    }

    private function milestoneProgress(Milestone $milestone): int
    {
        return match ((string) $milestone->status) {
            '1' => 0,
            '2' => 25,
            '3' => 50,
            '4' => 100,
            default => 0,
        };
    }

    private function milestoneStatusClass(Milestone $milestone): string
    {
        return match ((string) $milestone->status) {
            '1' => 'gantt-ms-created',
            '2' => 'gantt-ms-active',
            '3' => 'gantt-ms-progress',
            '4' => 'gantt-ms-done',
            default => 'gantt-ms-created',
        };
    }

    /**
     * #5 - Compute real progress for a task based on logged hours vs estimated time span.
     */
    private function computeTaskProgress($task): int
    {
        if ($task->end_date) {
            return 100;
        }

        $logged = (int) ($task->total_logged_seconds ?? 0);
        if ($logged === 0) {
            return 0;
        }

        // Estimate based on weekdays between start and estimated date (8h/day)
        if ($task->estimated_date && $task->start_date) {
            $days = max(1, Carbon::parse($task->start_date)->diffInWeekdays(Carbon::parse($task->estimated_date)));
            $estimatedSeconds = $days * 8 * 3600;
            return min(100, (int) round(($logged / $estimatedSeconds) * 100));
        }

        // If no estimated date, just show that work has started
        return min(50, (int) round($logged / 3600));
    }

    private function formatSeconds(int $totalSeconds): string
    {
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
