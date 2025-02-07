<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Project extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name',
        'ref_mo',
        'type',
        'status',
        'clipo',
        'start_date',
        'end_date',
        'budget',
        'workspace',
        'created_by',
        'is_active',
    ];



    public function creater()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function workspaceData()
    {
        return $this->hasOne('App\Models\Workspace', 'id', 'workspace');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_projects', 'project_id', 'user_id')
            ->withPivot('is_active')
            ->orderBy('users.id', 'ASC');
    }
    public function technicians()
    {
        return $this->belongsToMany('App\Models\User', 'user_projects', 'project_id', 'user_id')
            ->where('users.type', 'user')
            ->withPivot('is_active')
            ->orderBy('users.id', 'ASC');
    }
    public function typeRel()
    {
        return $this->belongsTo(ProjectType::class, 'type', 'id');
    }

    public function typeName()
    {
        return $this->type ? $this->typeRel->name : 'unknown';
    }

    public function salesManager()
    {
        return $this->belongsToMany('App\Models\User', 'user_projects', 'project_id', 'user_id')
            ->where('users.type', 'client')
            ->withPivot('is_active')
            ->orderBy('users.id', 'ASC');
    }

    public function task()
    {
        return $this->hasMany('App\Models\Task', 'project_id', 'id');
    }

    public function clientids()
    {
        return $this->belongsToMany('App\Models\User', 'user_projects', 'project_id', 'user_id')
            ->where('users.type', 'client')
            ->withPivot('is_active')
            ->pluck('client_id')
            ->orderBy('users.id', 'ASC');
    }

    public function countTask()
    {
        return Task::where('project_id', '=', $this->id)->count();
    }

    public function tasks()
    {
        return Task::where('project_id', '=', $this->id)->get();
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
    public function activeMilestones()
    {
        return $this->hasMany(Milestone::class)->where('status', 2);
    }

    public function updateProjectStatus()
    {
        if ($this->milestones()->where('status', 3)->exists() || $this->milestones()->exists()) {
            $this->status = 'Ongoing';
        }

        if (!$this->milestones()->where('status', '!=', 4)->exists()) {
            $this->status = 'Finished';
        }

        $this->save();
    }

    public function milestonesCount()
    {
        return $this->milestones()->count();
    }

    public function user_tasks($user_id)
    {
        return Task::where('project_id', $this->id)->whereRaw('FIND_IN_SET(?, assign_to)', [$user_id])->get();
    }


    public function user_done_tasks($user_id)
    {
        return Task::where('project_id', '=', $this->id)->where('assign_to', '=', $user_id)->get();
    }

    public function timesheet()
    {
        return Timesheet::where('project_id', '=', $this->id)->get();
    }

    public function countTaskComments()
    {
        return Task::join('comments', 'comments.task_id', '=', 'tasks.id')->where('project_id', '=', $this->id)->count();
    }

    public function getProgress()
    {
        // La logica que se debe aplicar con los milestone para que un proyecto este acabado es cuando ya tiene todos los encargos acabados

        $total     = Task::where('project_id', '=', $this->id)->count();
        $totalDone = Task::where('project_id', '=', $this->id)->where('status', '=', 'done')->count();
        if ($totalDone == 0) {
            return 0;
        }

        return round(($totalDone * 100) / $total);
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile', 'project_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\ActivityLog', 'project_id', 'id')->orderBy('id', 'desc');
    }
    private static function processAllProjectsTimesheets($projects, $timesheets, $days, $currentWorkspace, $userId, &$totalTaskTimes)
    {
        $timesheetArray = [];
        $projectIndex = 0;
        $totalrecords = 0;
        $user_id = Auth::user()->id;

        $first_day = Carbon::parse($days['first_day']);
        $seventh_day = Carbon::parse($days['seventh_day']);

        foreach ($projects as $project) {
            $milestoneArray = [];
            $hasTasks = false;

            foreach ($project->activeMilestones as $milestone) {

                $taskArray = [];
                foreach ($milestone->userTasks($user_id)->get() as $task) {
                    $taskStart = Carbon::parse($task->start_date);
                    $taskEnd = $task->end_date ? Carbon::parse($task->end_date) : null;

                    if ($taskStart->lte($seventh_day) && (!$taskEnd || $taskEnd->gte($first_day))) {
                        $taskData = self::processTaskTimesheets($task, $days, $currentWorkspace, $project->id, $userId);
                        $taskArray[] = $taskData;
                        $totalTaskTimes[] = $taskData['totaltime'];
                        $hasTasks = true;
                    }
                }
                if (!empty($taskArray)) {
                    $milestoneArray[] = [
                        'milestone_name' => $milestone->title ?? 'unknown',
                        'taskArray' => $taskArray,
                    ];
                }
            }
            if ($hasTasks) {
                $totalrecords = count($totalTaskTimes);
                $timesheetArray[] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'milestoneArray' => $milestoneArray
                ];
            }
        }

        return [
            'timesheetArray' => $timesheetArray,
            'totalrecords' => $totalrecords,
        ];
    }
    private static function processSingleProjectTimesheets($project, $timesheets, $days, $currentWorkspace, $userId, &$totalTaskTimes)
    {
        $timesheetArray = [];
        $totalrecords = 0;

        $first_day = Carbon::parse($days['first_day']);
        $seventh_day = Carbon::parse($days['seventh_day']);

        $milestonesWithTasks = $project->milestones()
            ->whereHas('tasks')
            ->with('tasks')
            ->get();

        foreach ($milestonesWithTasks as $milestone) {
            $userArray = [];

            foreach ($milestone->tasks as $task) {
                $taskStart = Carbon::parse($task->start_date);
                $taskEnd = $task->end_date ? Carbon::parse($task->end_date) : null;

                if ($taskStart->lte($seventh_day) && (!$taskEnd || $taskEnd->gte($first_day))) {
                    $taskData = self::processTaskTimesheets($task, $days, $currentWorkspace, $project->id, $userId);
                    $totalTaskTimes[] = $taskData['totaltime'];

                    $user = User::find($task->assign_to);
                    if ($user) {
                        if (!isset($userArray[$user->id])) {
                            $userArray[$user->id] = [
                                'user_id' => $user->id,
                                'user_name' => $user->name ?? 'unknown name',
                                'taskArray' => []
                            ];
                        }
                        $userArray[$user->id]['taskArray'][] = $taskData;
                    }
                }
            }

            if (!empty($userArray)) {
                $timesheetArray[] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'milestone_name' => $milestone->title ?? 'unknown',
                    'usersArray' => array_values($userArray),
                ];
                $totalrecords += array_sum(array_map(function ($user) {
                    return count($user['taskArray']);
                }, $userArray));
            }
        }

        return [
            'timesheetArray' => $timesheetArray,
            'totalrecords' => $totalrecords,
        ];
    }

    private static function processTaskTimesheets($task, $days, $currentWorkspace, $projectId, $userId)
    {
        $taskTimesheets = $task->timesheets ?? [];
        $times = [];
        $dateArray = [
            'week' => [],
        ];

        for ($j = 0; $j < 7; $j++) {
            if (!isset($days['datePeriod']) || count($days['datePeriod']) < 7) {
                throw new Exception("El rango de fechas no está correctamente definido.");
            }

            $date = $days['datePeriod'][$j]->format('Y-m-d');

            $filteredTimesheet = collect($taskTimesheets)->first(function ($val) use ($date, $task, $userId) {
                return isset($val->created_by, $val->date, $val->task_id) &&
                    $val->created_by == $userId &&
                    $val->date == $date &&
                    $val->task_id == $task->id;
            });

            if (!$filteredTimesheet) {
                $time = '00:00';
                $type = 'create';
                $url = route('project.timesheet.create', [
                    'slug' => $currentWorkspace->slug,
                    'project_id' => $projectId,
                ]);
            } else {
                $time = Carbon::parse($filteredTimesheet->time)->format('H:i');
                $type = 'edit';
                $url = route('project.timesheet.edit', [
                    'slug' => $currentWorkspace->slug,
                    'timesheet_id' => $filteredTimesheet->id,
                    'project_id' => $projectId,
                ]);
            }

            $dateArray['week'][$j] = [
                'project_id' => $projectId,
                'user_id' => $userId,
                'task_id' => $task->id,
                'milestone_id' => $task->milestone_id,
                'date' => $date,
                'time' => $time,
                'type' => $type,
                'url'  => $url,
            ];

            $times[] = $time;
        }

        $calculatedtasktime = Utility::calculateTimesheetHours($times);

        $taskType = TaskType::select('id', 'name')
            ->where('id', $task->type_id)
            ->first();

        return [
            'task_id' => $task->id,
            'task_name' => $taskType ? $taskType->name : 'unknown',
            'dateArray' => $dateArray,
            'totaltime' => $calculatedtasktime,
        ];
    }
    private static function calculateDateTimes($days, $currentWorkspace, $project_id, $allProjects)
    {
        $totalDateTimes = [];

        foreach ($days['datePeriod'] as $date) {
            $dateFormatted = $date->format('Y-m-d');
            $timesheetsQuery = Timesheet::select('timesheets.*')
                ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->where('projects.workspace', $currentWorkspace->id);

            if (!$allProjects) {
                $timesheetsQuery->where('projects.id', $project_id);
            }

            $dailyTimesheets = $timesheetsQuery->get();
            $totalDateTimes[$dateFormatted] = Utility::calculateTimesheetHours($dailyTimesheets->where('date', $dateFormatted)->pluck('time')->toArray());
        }

        return $totalDateTimes;
    }
    public static function getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets = [], $days = [], $project_id = null, $seeAsOwner = false)
    {
        $userId = Auth::id();
        $allProjects = false;
        $project_id == '-1';
        $timesheetArray = [];
        $totalTaskTimes = [];
        $totalrecords = 0;

        if ($project_id == -1) {
            $allProjects = true;

            $projects = Project::select(['id', 'name'])
                ->whereHas('milestones.tasks')
                ->with(['milestones' => function ($query) {
                    $query->select(['id', 'title', 'project_id'])
                        ->whereHas('tasks');
                }])
                ->get();

            $results = self::processAllProjectsTimesheets($projects, $timesheets, $days, $currentWorkspace, $userId, $totalTaskTimes);

            $totalrecords = $results['totalrecords'];
            $timesheetArray = $results['timesheetArray'];
        } else {
            $project = Project::find($project_id);

            if ($project) {
                $results = self::processSingleProjectTimesheets($project, $timesheets, $days, $currentWorkspace, $userId, $totalTaskTimes);
                $totalrecords = $results['totalrecords'];
                $timesheetArray = $results['timesheetArray'];
            }
        }

        $calculatedTotalTaskTime = Utility::calculateTimesheetHours($totalTaskTimes);
        $totalDateTimes = self::calculateDateTimes($days, $currentWorkspace, $project_id, $allProjects);

        $htmlContent = view('projects.timesheet-week', compact(
            'currentWorkspace',
            'timesheetArray',
            'totalDateTimes',
            'calculatedTotalTaskTime',
            'days',
            'seeAsOwner',
            'allProjects'
        ))->render();

        return compact('htmlContent', 'totalrecords');
    }

    public function project_progress()
    {
        $total_task     = Task::where('project_id', '=', $this->id)->count();
        $completed_task =  Task::where('project_id', '=', $this->id)->where('status', '=', 4)->count();

        if ($total_task > 0) {
            $percentage = intval(($completed_task / $total_task) * 100);


            return [

                'percentage' => $percentage . '%',
            ];
        } else {
            return [

                'percentage' => 0,
            ];
        }
    }



    public function project_milestone_progress()
    {
        $total_milestone     = Milestone::where('project_id', '=', $this->id)->count();
        $total_progress_sum  = Milestone::where('project_id', '=', $this->id)->sum('progress');

        if ($total_milestone > 0) {
            $percentage = intval(($total_progress_sum / $total_milestone));


            return [

                'percentage' => $percentage . '%',
            ];
        } else {
            return [

                'percentage' => 0,
            ];
        }
    }
}
