<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Milestone; // Importa el modelo Milestone
use App\Models\Timesheet;

class CalenderController extends Controller
{
    public function index($slug, $project_id = NULL)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        // Obtén las tareas según el tipo de usuario
        if ($objUser->getGuard() == 'client') {
            $tasks = Task::select('tasks.*')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
                ->where('client_projects.client_id', '=', $objUser->id)
                ->where('client_projects.permission', 'LIKE', '%show task%')
                ->where('projects.workspace', '=', $currentWorkspace->id);

            $projects = Project::select('projects.*')
                ->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
                ->where('client_projects.client_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        } elseif ($currentWorkspace && $currentWorkspace->permission == 'Owner') {
            $tasks = Task::select('tasks.*')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->where('projects.workspace', '=', $currentWorkspace->id);

            $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        } else {
            $tasks = Task::select('tasks.*')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->whereRaw("find_in_set('" . Auth::user()->id . "',tasks.assign_to)");

            $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        }

        if ($project_id) {
            $tasks->where('tasks.project_id', '=', $project_id);
        }
        $tasks = $tasks->get();

        // Formatear las tareas para FullCalendar
        $arrayJson = [];
        foreach ($tasks as $task) {
            $arrayJson[] = [
                "title" => $task->id, // Cambié esto para mostrar el ID como título (puedes cambiarlo a lo que desees)
                "start" => $task->start_date,
                "end" => $task->estimated_date,
                "url" => $objUser->getGuard() != 'client' ? route(
                    'tasks.show',
                    [$currentWorkspace->slug, $task->project_id, $task->id]
                ) : route(
                    'client.tasks.show',
                    [$currentWorkspace->slug, $task->project_id, $task->id]
                ),
                "className" => 'event-info border-info',
                "allDay" => true,
            ];
        }

        return view('calendar.index', compact('currentWorkspace', 'arrayJson', 'projects', 'project_id'));
    }
    public function calendar(Request $request, $slug, $project_id = NULL)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
    
        $projects = [];
    
        if ($objUser->getGuard() == 'client') {
            $tasks = Task::select('tasks.*', 'task_types.name as type_name')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
                ->leftJoin('task_types', 'tasks.type_id', '=', 'task_types.id')
                ->where('client_projects.client_id', '=', $objUser->id)
                ->where('client_projects.permission', 'LIKE', '%show task%')
                ->where('projects.workspace', '=', $currentWorkspace->id);
    
            $projects = Project::select('projects.*')
                ->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
                ->where('client_projects.client_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        } elseif ($currentWorkspace && $currentWorkspace->permission == 'Owner') {
            $tasks = Task::select('tasks.*', 'task_types.name as type_name')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->leftJoin('task_types', 'tasks.type_id', '=', 'task_types.id')
                ->where('projects.workspace', '=', $currentWorkspace->id);
    
            $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        } else {
            $tasks = Task::select('tasks.*', 'task_types.name as type_name')
                ->join('projects', 'projects.id', '=', 'tasks.project_id')
                ->leftJoin('task_types', 'tasks.type_id', '=', 'task_types.id')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->whereRaw("find_in_set('" . $objUser->id . "',tasks.assign_to)");
    
            $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();
        }
    
        if ($request->has('project_id') && $request->project_id != '') {
            $tasks->where('tasks.project_id', '=', $request->project_id);
        }
    
        $tasks = $tasks->get();
    
        $taskHours = [];
        $totalMinutes = 0;
    
        foreach ($tasks as $task) {
            $timesheets = Timesheet::where('task_id', $task->id)->get();
            $taskMinutes = 0;
    
            foreach ($timesheets as $timesheet) {
                list($hours, $minutes, $seconds) = explode(':', $timesheet->time);
                $taskMinutes += ($hours * 60) + $minutes;
            }
    
            $totalMinutes += $taskMinutes;
    
            $hours = floor($taskMinutes / 60);
            $minutes = $taskMinutes % 60;
            $taskHours[$task->id] = sprintf('%02d:%02d', $hours, $minutes);
        }
    
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $formattedTotalHours = sprintf('%02d:%02d', $hours, $minutes);
    
        $milestoneIds = $tasks->pluck('milestone_id')->filter()->unique();
        $milestones = Milestone::whereIn('id', $milestoneIds)->get()->keyBy('id');
    
        $arrayJson = [];
    
        foreach ($tasks as $task) {
            $milestoneTitle = isset($milestones[$task->milestone_id]) ? $milestones[$task->milestone_id]->title : $task->title;
            $taskTitle = $task->type_name ?? 'No Type';
            $formattedTitle = "$milestoneTitle - $taskTitle";
    
            $arrayJson[] = [
                "title" => $formattedTitle,
                "start" => $task->start_date,
                "end" => $task->estimated_date,
                "url" => route('tasks.show', [$currentWorkspace->slug, $task->project_id, $task->id]),
                "className" => 'event-info border-info',
                "allDay" => true,
                "milestone_id" => $task->milestone_id,
            ];
        }
    
        if ($request->ajax()) {
            return response()->json([
                'events' => $arrayJson,
                'tasks' => $tasks->map(function ($task) use ($milestones, $taskHours) {
                    $milestoneTitle = isset($milestones[$task->milestone_id]) ? $milestones[$task->milestone_id]->title : $task->title;
                    $taskTitle = $task->type_name ?? 'No Type';
                    $taskTime = $taskHours[$task->id] ?? '00:00';
                    return [
                        'milestoneTitle' => $milestoneTitle,
                        'taskTitle' => $taskTitle,
                        'taskTime' => $taskTime,
                    ];
                }),
                'formattedTotalHours' => $formattedTotalHours
            ]);
        }
    
        return view('calendar.index', compact('currentWorkspace', 'arrayJson', 'projects', 'project_id', 'tasks', 'taskHours', 'formattedTotalHours', 'milestones'));
    }
    
}
