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
        'start_date',
        'end_date',
        'budget',
        'workspace',
        'created_by',
        'is_active',
    ];

    public function getCollaborators()
    {
        //mostrara a los que hayan participado en el proyecto user_project ->where project_id
    }
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
            // ->where('users.type', 'user') no muestra s los comerciales
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
        return $this->hasMany(Milestone::class, 'project_id');
    }

    public function milestonesCount()
    {
        return $this->milestones()->count();
    }

    public function user_tasks($user_id)
    {
        return Task::where('project_id', $this->id)->whereRaw('FIND_IN_SET(?, assign_to)', [$user_id])->get();
    }

    // public function user_tasks($user_id){
    //     return Task::where('project_id','=',$this->id)->where('assign_to','=',$user_id)->get();
    // }
    public function user_done_tasks($user_id)
    {
        return Task::where('project_id', '=', $this->id)->where('assign_to', '=', $user_id)->get();
        // return Task::join('stages', 'stages.id', '=', 'tasks.status')->where('project_id', '=', $this->id)->where('assign_to', '=', $user_id)->where('stages.complete', '=', '1')->get();
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

    // public function milestones()
    // {
    //     return $this->hasMany('App\Models\Milestone', 'project_id', 'id');
    // }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile', 'project_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\ActivityLog', 'project_id', 'id')->orderBy('id', 'desc');
    }

    // public static function getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets = [], $days = [], $project_id = null, $seeAsOwner = false)
    public static function getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets = [], $milestones = [], $days = [], $project_id = null, $seeAsOwner = false)
    {
        $objUser = Auth::user();
        $user_id = Auth::user()->id;
        $userId = Auth::user()->id;


        $i = $k = 0;
        $allProjects = false;
        $timesheetArray = $totaltaskdatetimes = [];

        if ($project_id == '-1') {
            $allProjects = true;

            if ($currentWorkspace->permission == 'Owner') {
                $project_timesheets = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                    ->where('projects.workspace', '=', $currentWorkspace->id);
            } else {
                //Cuando hace el bucle para recorrer todos los usuarios, es interesante que sea cuando hay varios usuarios
                $project_timesheets = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->where('tasks.assign_to', $user_id);
            }
            // Son tdos los proyectos
            // dd($timesheets);

            $k = 0; // Inicializamos el índice del proyecto fuera del bucle principal
            foreach ($timesheets as $project_id => $milestones) {
                $project = Project::find($project_id);

                if ($project) {

                    // Inicializamos las entradas del proyecto en el array
                    $timesheetArray[$k]['project_id'] = $project->id;
                    $timesheetArray[$k]['project_name'] = $project->name;

                    $count = 0;
                    $m = 0; // Inicializamos el índice del hito para cada proyecto
                    foreach ($milestones as $milestone_id => $tasksmilestones) {

                        $milestone = Milestone::find($milestone_id);
                        $timesheetArray[$k]['milestoneArray'][$m]['milestone_name'] = $milestone ? $milestone->title : 'unknow';

                        $i = 0; // Reiniciamos el índice de tareas para cada hito
                        foreach ($tasksmilestones as  $tasktimesheet) {
                            $task = Task::find($tasktimesheet[0]['task_id']);
                            $taskId = $task->id;
                            $timesheet = Timesheet::find($tasktimesheet[0]['id']);

                            if ($task) {
                                $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['task_id'] = $timesheet->task_id;

                                $typesName = TaskType::select('id', 'name')
                                    ->where('project_type', '=', $project->type)
                                    ->where('id', $task->type_id)
                                    ->first();
                                $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['task_name'] = $typesName ? $typesName->name : 'unknow';

                                $user = User::find($task->assign_to);
                                $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['user_id'] = $user ? $user->id : null;
                                $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['user_name'] = $user ? $user->name : 'unknow name';

                                $times = [];

                                for ($j = 0; $j < 7; $j++) {
                                    $date = $days['datePeriod'][$j]->format('Y-m-d');

                                    $filtered_array = array_filter($tasktimesheet, function ($val) use ($userId, $date, $taskId) {
                                        if (isset($val['created_by']) && isset($val['date'])) {
                                            return ($val['created_by'] == $userId && $val['date'] == $date && $val['task_id'] == $taskId);
                                        }
                                    });

                                    $key = array_keys($filtered_array);
                                    $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['date'] = $date;

                                    if (count($key) > 0) {
                                        $time = Carbon::parse($tasktimesheet[$key[0]]['time'])->format('H:i');

                                        $times[] = $time;

                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = $time;
                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'edit';
                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url'] = route('project.timesheet.edit', [
                                            'slug' => $currentWorkspace->slug,
                                            'timesheet_id' => $tasktimesheet[$key[0]]['id'],
                                            'project_id' => $project_id,
                                        ]);
                                    } else {
                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = '00:00';
                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'create';
                                        $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url'] = route('project.timesheet.create', [
                                            'slug' => $currentWorkspace->slug,
                                            'project_id' => $project_id,
                                        ]);
                                    }
                                }
                                $calculatedtasktime = Utility::calculateTimesheetHours($times);
                                $totaltaskdatetimes[] = $calculatedtasktime;
                                $timesheetArray[$k]['milestoneArray'][$m]['taskArray'][$i]['dateArray'][$count]['totaltime'] = $calculatedtasktime;
                                // }
                            }
                            $i++;
                        }
                        $m++; // Incrementamos el índice del hito aquí para cada hito procesado
                    }
                    $k++; // Incrementamos el índice del proyecto aquí para cada proyecto procesado 
                }
            }
            $calculatedtotaltaskdatetime = Utility::calculateTimesheetHours($totaltaskdatetimes);

            foreach ($days['datePeriod'] as $key => $date) {
                $dateperioddate = $date->format('Y-m-d');

                $new_projects_timesheet = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->where('tasks.assign_to', '=', Auth::user()->id)->get();

                $totalDateTimes[$dateperioddate] = Utility::calculateTimesheetHours($new_projects_timesheet->where('date', $dateperioddate)->pluck('time')->toArray());
            }
        } else {

            $userId = Auth::user()->id;
            $project_timesheets = Timesheet::select('timesheets.*')
                ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->where('projects.workspace', '=', $currentWorkspace->id);

            $project = Project::find($project_id);


            $k = 0; // Inicializamos el índice del milestone fuera del bucle principal
            foreach ($timesheets as $milestone_id => $users) {
                $milestone = Milestone::find($milestone_id);

                if ($milestone) {
                    // Inicializamos las entradas del milestone en el array
                    $timesheetArray[$k]['project_id'] = $project ? $project->id : null;
                    $timesheetArray[$k]['milestone_id'] = $milestone ? $milestone->id : null;
                    $timesheetArray[$k]['milestone_name'] = $milestone ? $milestone->title : 'unknow title';

                    $count = 0;
                    $m = 0; // Inicializamos el índice de los usuarios para cada milestone
                    foreach ($users as $user_id => $userTask) {
                        $user = User::find($user_id);
                        $timesheetArray[$k]['usersArray'][$m]['user_id'] = $user ? $user->id : null;
                        $timesheetArray[$k]['usersArray'][$m]['user_name'] = $user ? $user->name : 'unknow name';

                        $i = 0; // Reiniciamos el índice de tareas para cada usuario
                        foreach ($userTask as $tasktimesheet) {
                            $task = Task::find($tasktimesheet[0]['task_id']);
                            $taskId = $task->id;
                            $timesheet = Timesheet::find($tasktimesheet[0]['id']);
                            // $project = Project::find($task->project_id);

                            if ($task) {
                                $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['task_id'] = $timesheet->task_id;

                                $typesName = TaskType::select('id', 'name')
                                    ->where('project_type', '=', $project->type)
                                    ->where('id', $task->type_id)
                                    ->first();
                                $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['task_name'] = $typesName ? $typesName->name : 'unknow';

                                $times = [];

                                for ($j = 0; $j < 7; $j++) {
                                    $date = $days['datePeriod'][$j]->format('Y-m-d');

                                    $filtered_array = array_filter($tasktimesheet, function ($val) use ($userId, $date, $taskId) {
                                        if (isset($val['created_by']) && isset($val['date'])) {
                                            return $val['created_by'] == $userId && $val['date'] == $date && $val['task_id'] == $taskId;
                                        }
                                    });

                                    $key = array_keys($filtered_array);
                                    $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['date'] = $date;

                                    if (count($key) > 0) {
                                        $time = Carbon::parse($tasktimesheet[$key[0]]['time'])->format('H:i');

                                        $times[] = $time;

                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = $time;
                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'edit';
                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url'] = route('project.timesheet.edit', [
                                            'slug' => $currentWorkspace->slug,
                                            'timesheet_id' => $tasktimesheet[$key[0]]['id'],
                                            'project_id' => $project_id,
                                        ]);
                                    } else {
                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = '00:00';
                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'create';
                                        $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url'] = route('project.timesheet.create', [
                                            'slug' => $currentWorkspace->slug,
                                            'project_id' => $project_id,
                                        ]);
                                    }
                                }

                                $calculatedtasktime = Utility::calculateTimesheetHours($times);
                                $totaltaskdatetimes[] = $calculatedtasktime;
                                $timesheetArray[$k]['usersArray'][$m]['taskArray'][$i]['dateArray'][$count]['totaltime'] = $calculatedtasktime;
                            }

                            $i++; // Incrementamos el índice de la tarea
                        }
                        $m++; // Incrementamos el índice del encargo
                    }
                    $k++; // Incrementamos el índice del proyecto
                }
            }
            $calculatedtotaltaskdatetime = Utility::calculateTimesheetHours($totaltaskdatetimes);

            foreach ($days['datePeriod'] as $key => $date) {
                $dateperioddate = $date->format('Y-m-d');
                $new_projects_timesheet = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->where('projects.id', '=', $project_id)->get();

                $totalDateTimes[$dateperioddate] = Utility::calculateTimesheetHours($new_projects_timesheet->where('date', $dateperioddate)->pluck('time')->toArray());
            }
        }

        $returnHTML = view(
            'projects.timesheet-week',
            compact(
                'currentWorkspace',
                'timesheetArray',
                'totalDateTimes',
                'calculatedtotaltaskdatetime',
                'days',
                'seeAsOwner',
                'allProjects'
            )
        )->render();

        return $returnHTML;
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
