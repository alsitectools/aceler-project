<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Timesheet;

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
        'ref_delegation'
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
        return $this->milestones()->where('status', 2);
    }

    public function updateProjectStatus()
    {
        $this->loadMissing(['milestones:id,project_id,status']);

        // 1) Sin encargos => OnHold
        if ($this->milestones->isEmpty()) {
            $this->status = 'OnHold';
            return $this->save();
        }

        // 2) Todos los encargos en Done(4) => Finished
        $allDone = $this->milestones->every(fn($m) => (int)$m->status === 4);
        if ($allDone) {
            $this->status = 'Finished';
            return $this->save();
        }

        // 3) Cualquier encargo en status 2, 3 o 4 (no todos 4) => Ongoing
        $hasAdvanced = $this->milestones->contains(fn($m) => in_array((int)$m->status, [2, 3, 4], true));
        $this->status = $hasAdvanced ? 'Ongoing' : 'OnHold';

        return $this->save();
    }




    public function milestonesCount()
    {
        return $this->milestones()->count();
    }

    public function user_tasks($user_id)
    {
        return Task::where('project_id', $this->id)->whereRaw('FIND_IN_SET(?, assign_to)', [$user_id])->get();
    }
    public function delegation()
    {
        return $this->belongsTo(Delegation::class, 'ref_delegation', 'id');
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

    // App\Models\Project.php

    public function scopeWhereUserIsParticipant($query, $userId)
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId)
                ->where('user_projects.is_active', 1);
        });
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

                    // Obtener el nombre correcto especialmente si es custom

                    $taskName = $task->type ? $task->type->name : '';
                    if ($task->type->name === 'custom') {
                        $customTask = CustomTasks::where('id_task', $task->id)->first();
                        $taskName = $customTask ? $customTask->name : $taskName;
                    }
                    // \Log::debug('Task name' .$taskName);
                    // \Log::debug('Evaluando tarea', [
                    //     'task_id' => $task->id,
                    //     'task_name' => $task->type ? $task->type->name : '',
                    //     'task_start' => $taskStart->toDateString(),
                    //     'task_end' => $taskEnd ? $taskEnd->toDateString() : null,
                    //     'first_day' => $first_day->toDateString(),
                    //     'seventh_day' => $seventh_day->toDateString(),
                    // ]);
                    // Permitir mostrar tareas hasta 5 años atrás
                    $milestoneStatus = $milestone->status;

                    if (
                        $taskStart->gte(Carbon::now()->subYears(5)) &&
                        (
                            !$taskEnd ||
                            $taskEnd->gte($first_day) ||
                            $milestoneStatus == 3
                        )
                    ) {
                        $taskData = self::processTaskTimesheets($task, $days, $currentWorkspace, $project->id, $userId);
                        $taskArray[] = $taskData;
                        $totalTaskTimes[] = $taskData['totaltime'];
                        $hasTasks = true;
                    }
                }
                if (!empty($taskArray)) {
                    $milestoneArray[] = [
                        'milestone_name' => $milestone->title ?? 'unknown',
                        'milestone_id' => $milestone->id ?? 'unknown',
                        'taskArray' => $taskArray,
                    ];
                }
            }
            if ($hasTasks) {
                $totalrecords = count($totalTaskTimes);
                $timesheetArray[] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'ref_delegation' => $project->ref_delegation,
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

                $milestoneStatus = $milestone->status;

                if (
                    $taskStart->gte(Carbon::now()->subYears(5)) &&
                    (
                        !$taskEnd ||
                        $taskEnd->gte($first_day) ||
                        $milestoneStatus == 3
                    )
                ) {
                    $userID = $task->assign_to;
                    $taskData = self::processTaskTimesheets($task, $days, $currentWorkspace, $project->id, $userID);

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
                \Log::info('Project ref_delegation debug', [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'ref_delegation' => $project->ref_delegation,
                ]);
                $timesheetArray[] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'ref_delegation' => $project->ref_delegation,
                    'milestone_name' => $milestone->title ?? 'unknown',
                    'milestone_id' => $milestone->id ?? 'unknown',
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
        $taskTimesheets = Timesheet::where('task_id', $task->id)->get();
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
                'user_id' => $task->assign_to,
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
        $timesheetsQuery = Timesheet::select('timesheets.*')
            ->join('projects', 'projects.id', '=', 'timesheets.project_id')
            ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
            ->where('projects.workspace', $currentWorkspace->id);

        foreach ($days['datePeriod'] as $date) {
            $dateFormatted = $date->format('Y-m-d');

            if ($allProjects) {
                $timesheetsQuery->where('timesheets.created_by', Auth::user()->id);
            } else {
                $timesheetsQuery->where('projects.id', $project_id);
            }

            $dailyTimesheets = $timesheetsQuery->get();
            $totalDateTimes[$dateFormatted] = Utility::calculateTimesheetHours($dailyTimesheets->where('date', $dateFormatted)->pluck('time')->toArray());
        }

        return $totalDateTimes;
    }

    public static function calculateGlobalDateTimes($days, $userId)
    {
        $totalsByDate = [];

        // ✅ CONVERTIR Carbon -> Y-m-d
        foreach ($days['datePeriod'] as $date) {
            $dateKey = Carbon::parse($date)->toDateString();
            $totalsByDate[$dateKey] = 0;
        }

        $timesheets = Timesheet::where('created_by', $userId)
            ->whereBetween('date', [
                Carbon::parse($days['first_day'])->toDateString(),
                Carbon::parse($days['seventh_day'])->toDateString()
            ])
            ->get();

        foreach ($timesheets as $timesheet) {

            $dateKey = Carbon::parse($timesheet->date)->toDateString();

            if (!array_key_exists($dateKey, $totalsByDate)) {
                continue;
            }

            [$h, $m, $s] = explode(':', $timesheet->time);
            $totalsByDate[$dateKey] += ($h * 60) + $m;
        }

        // ✅ Mantener el orden de los días
        $result = [];
        foreach ($totalsByDate as $minutes) {
            $result[] = sprintf(
                '%02d:%02d',
                floor($minutes / 60),
                $minutes % 60
            );
        }

        return $result;
    }

    private static function buildPopupTasksByDate($days, $userId)
    {
        $firstDay = Carbon::parse($days['first_day'])->toDateString();
        $seventhDay = Carbon::parse($days['seventh_day'])->toDateString();

        $rows = Timesheet::where('timesheets.created_by', $userId)
            ->whereBetween('timesheets.date', [$firstDay, $seventhDay])
            ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
            ->join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
            ->join('projects', 'projects.id', '=', 'timesheets.project_id')
            ->leftJoin('task_types', 'task_types.id', '=', 'tasks.type_id')
            ->whereIn('milestones.status', [2, 3, 4])
            ->select([
                'timesheets.date',
                'timesheets.time',
                'timesheets.task_id',
                'task_types.name as task_type_name',
                'milestones.status as milestone_status',
                'milestones.title as milestone_name',
                'projects.name as project_name',
            ])
            ->get();

        $tasksByDate = [];
        foreach ($rows as $ts) {
            $date = Carbon::parse($ts->date)->toDateString();
            $time = Carbon::parse($ts->time)->format('H:i');

            if ($time === '00:00') {
                continue;
            }

            $taskName = trim($ts->task_type_name ?? '');
            if (strtolower($taskName) === 'custom') {
                $customTask = CustomTasks::where('id_task', $ts->task_id)->first();
                $taskName = $customTask && !empty($customTask->name) ? $customTask->name : __('Custom');
            } else {
                $taskName = !empty($taskName) ? __($taskName) : __('N/A');
            }

            $tasksByDate[$date][] = [
                'task_name' => $taskName,
                'hours' => $time,
                'milestone_status' => (int) $ts->milestone_status,
                'milestone_name' => $ts->milestone_name,
                'project_name' => $ts->project_name,
            ];
        }

        return $tasksByDate;
    }

    public static function getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets = [], $days = [], $project_id = null, $seeAsOwner = false, $showAllWorkspaces = false)
    {
        $userId = Auth::id();
        $allProjects = false;
        $project_id == '-1';
        $timesheetArray = [];
        $totalTaskTimes = [];
        $totalrecords = 0;

        if ($project_id == -1) {
            $allProjects = true;

            $query = Project::select(['id', 'name', 'ref_delegation'])
                ->whereHas('milestones.tasks');

            // Filtrar por workspace actual o todos los workspaces
            if (!$showAllWorkspaces) {
                $query->where('workspace', $currentWorkspace->id);
            }

            $projects = $query->with(['milestones' => function ($query) {
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
        //$totalDateTimes = self::calculateDateTimes($days, $currentWorkspace, $project_id, $allProjects);
        $totalDateTimes = self::calculateGlobalDateTimes(
            $days,
            Auth::id()
        );
        //get all timetable info of the user
        $userTimetable = UserTimetable::where('user_id', $userId)->first();

        //conver to array
        \Log::debug('PreArray', [
            'user_id' => $userId,
            'userTimetable' => $userTimetable,
        ]);
        $userTimetableArray = $userTimetable ? $userTimetable->toArray() : [];

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $workHoursWeek = []; // Array para almacenar los días laborables y horas

        // iterate the user timetable array and check if the key is on the daysOfWeek array
        foreach ($userTimetableArray as $key => $value) {

            if (in_array(strtolower($key), $daysOfWeek)) {
                $workHoursWeek[$key] = $value;
            }
        }

        $holidayDates = [];
        if (!empty($userTimetableArray['range_holidays'])) {
            $decodedHolidays = json_decode($userTimetableArray['range_holidays'], true);
            if (is_array($decodedHolidays)) {
                $holidayDates = array_values($decodedHolidays);
            }
        }

        $intensiveHoursByDate = [];
        if (!empty($userTimetableArray['range_intensive_workday'])) {
            $decodedIntensive = json_decode($userTimetableArray['range_intensive_workday'], true);

            if (is_array($decodedIntensive)) {
                foreach ($decodedIntensive as $hours => $dates) {
                    if (!is_array($dates)) {
                        continue;
                    }

                    foreach ($dates as $date) {
                        $intensiveHoursByDate[$date] = $hours;
                    }
                }
            }
        }

        $popupTasksByDate = self::buildPopupTasksByDate($days, $userId);

        $htmlContent = view('projects.timesheet-week', compact(
            'currentWorkspace',
            'timesheetArray',
            'totalDateTimes',
            'calculatedTotalTaskTime',
            'days',
            'seeAsOwner',
            'allProjects',
            'workHoursWeek',
            'holidayDates',
            'intensiveHoursByDate',
            'popupTasksByDate'
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
