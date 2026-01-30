<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Milestone; // Importa el modelo Milestone
use App\Models\Timesheet;
use App\Models\UserTimetable;
use DB;
use Log;
use DateTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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


    public function getCalendarData()
    {
        $userId = Auth::id();
        $timetable = UserTimetable::where('user_id', $userId)->first();
        $timesheets = Timesheet::where('user_id', $userId)->get();

        if (!$timetable) {
            return response()->json(['error' => 'No timetable found'], 404);
        }

        $calendarData = $this->processCalendarData($timetable, $timesheets);

        return response()->json($calendarData);
    }

    // Function to get the day of the week
    private function getDayOfWeek($date)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format('l'); // 'l' (lowercase 'L') returns the full textual representation of the day of the week
    }

    // Function to get the week days of a specific week
    public function getWeekDaysOfMonth($specificDate = null)
    {
        // If no date is provided, use the current date
        $referenceDate = $specificDate ? Carbon::parse($specificDate) : Carbon::now();

        // Calculate the first and last day of the week based on the given date
        $first_day = $referenceDate->copy()->startOfWeek(); // Monday
        $seventh_day = $referenceDate->copy()->endOfWeek(); // Sunday

        $dateCollection = [
            'first_day' => $first_day->toDateString(),
            'seventh_day' => $seventh_day->toDateString(),
            'datePeriod' => []
        ];

        // Generate the range of dates for the week
        $period = CarbonPeriod::create($first_day, $seventh_day);

        foreach ($period as $key => $dateObj) {
            $dateCollection['datePeriod'][$key] = $dateObj->toDateString();
        }

        return $dateCollection;
    }

    public function getTimesheetColor(Request $request)
    {
        $userId = Auth::id();

        // Obtener todas las imputaciones (timesheets) del usuario
        $query = DB::table('timesheets')
            ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
            ->join('users', 'tasks.assign_to', '=', 'users.id')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->select('tasks.*', 'timesheets.*')
            ->where('users.id', '=', $userId);

        if ($request->has('workspace_id') && $request->get('all') != 'true') {
            $query->where('projects.workspace', '=', $request->workspace_id);
        }

        $timesheets = $query->get();

        // Obtener el horario del usuario
        $timetable = UserTimetable::where('user_id', $userId)->first();
        if (!$timetable) {
            return response()->json(['error' => 'No timetable found'], 404);
        }

        // Horas esperadas por día
        $expectedHours = [
            'monday' => $timetable->monday,
            'tuesday' => $timetable->tuesday,
            'wednesday' => $timetable->wednesday,
            'thursday' => $timetable->thursday,
            'friday' => $timetable->friday,
            'saturday' => $timetable->saturday,
            'sunday' => $timetable->sunday,
        ];

        // 1️⃣ Agrupar imputaciones por fecha sumando las horas
        $groupedByDate = [];
        $minDate = null;
        $maxDate = null;

        foreach ($timesheets as $timesheet) {
            $date = $timesheet->date;
            if (!isset($groupedByDate[$date])) {
                $groupedByDate[$date] = 0; // total minutos
            }
            list($h, $m, $s) = explode(':', $timesheet->time);
            $groupedByDate[$date] += ($h * 60) + $m; // sumar minutos

            // Rastrear la fecha mínima y máxima
            if ($minDate === null || $date < $minDate) {
                $minDate = $date;
            }
            if ($maxDate === null || $date > $maxDate) {
                $maxDate = $date;
            }
        }

        // 2️⃣ Generar calendarData con total horas por fecha
        $calendarData = [];
        foreach ($groupedByDate as $date => $totalMinutes) {
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $formatted = sprintf('%02d:%02d', $hours, $minutes);

            $calendarData[] = [
                'date' => $date,
                'dayOfWeek' => $this->getDayOfWeek($date),
                'hours' => $formatted,
            ];
        }

        $today = Carbon::now()->toDateString();

        // Determinar el período de semanas a generar
        // Si hay timesheets, empezar desde el año del primer timesheet
        // Si no hay timesheets, usar el año actual
        if ($minDate !== null) {
            $startOfPeriod = Carbon::parse($minDate)->startOfYear()->startOfWeek();
        } else {
            $startOfPeriod = Carbon::now()->startOfYear()->startOfWeek();
        }

        $endOfPeriod = Carbon::now()->endOfWeek();

        // Crear un índice de calendarData por fecha para búsqueda rápida
        $calendarDataByDate = [];
        foreach ($calendarData as $dataDay) {
            $calendarDataByDate[$dataDay['date']] = $dataDay;
        }

        $colorData = [];

        // Generar colorData solo para días con timesheets (optimizado para evitar memory exhausted)
        foreach ($groupedByDate as $currentDate => $totalMinutes) {
            // Excluir días futuros
            if ($currentDate > $today) {
                continue;
            }

            $dayOfWeek = strtolower($this->getDayOfWeek($currentDate));
            $expectedHour = $expectedHours[$dayOfWeek] ?? null;

            if ($expectedHour === null) {
                continue;
            }

            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $workedHours = sprintf('%02d:%02d', $hours, $minutes);

            // Determinar color
            if ($workedHours == '00:00') {
                $dayColor = '#e06c71'; // rojo
            } elseif ($workedHours < $expectedHour) {
                $dayColor = '#fcf75e'; // amarillo
            } elseif ($workedHours == $expectedHour) {
                $dayColor = '#89e186'; // verde
            } else {
                $dayColor = '#b2e2f2'; // azul
            }

            $colorData[] = [
                'dayOfWeek' => ucfirst($dayOfWeek),
                'date' => $currentDate,
                'hours' => $workedHours,
                'color' => $dayColor,
            ];
        }

        // Generar días sin imputaciones (rojos) para el año actual y el anterior
        $twoYearsAgoStart = Carbon::now()->subYear()->startOfYear()->startOfWeek();
        $period = CarbonPeriod::create($twoYearsAgoStart, '1 day', $endOfPeriod);

        foreach ($period as $dateObj) {
            $currentDate = $dateObj->toDateString();

            // Excluir días futuros
            if ($currentDate > $today) {
                continue;
            }

            // Si ya existe en groupedByDate, ya fue procesado arriba
            if (isset($groupedByDate[$currentDate])) {
                continue;
            }

            $dayOfWeek = strtolower($this->getDayOfWeek($currentDate));
            $expectedHour = $expectedHours[$dayOfWeek] ?? null;

            if ($expectedHour !== null) {
                $colorData[] = [
                    'dayOfWeek' => ucfirst($dayOfWeek),
                    'date' => $currentDate,
                    'hours' => '00:00',
                    'color' => '#e06c71', // rojo
                ];
            }
        }

        // Obtener festivos y jornadas intensivas
        $rangeDays = DB::table('user_timetable')
            ->where('user_id', $userId)
            ->select('range_holidays', 'range_intensive_workday')
            ->first();

        $specialColorData = [];

        // Festivos
        if (isset($rangeDays) && !is_null($rangeDays->range_holidays)) {
            $holidays = json_decode($rangeDays->range_holidays, true);
            $holidayDays = [];
            foreach ($holidays as $day) {
                $holidayDays[] = $day;
            }
            $specialColorData['holidayRange'] = $holidayDays;
            $specialColorData['holidayColor'] = '#91DDCF'; // color festivo
        }

        // Jornadas intensivas
        if (isset($rangeDays) && !is_null($rangeDays->range_intensive_workday)) {
            $intensiveWorkdays = json_decode($rangeDays->range_intensive_workday, true);

            $intensiveData = [];
            foreach ($intensiveWorkdays as $hours => $days) {
                foreach ($days as $day) {
                    $intensiveData[$hours][] = $day;
                }
            }
            $specialColorData['intensiveWorkRange'] = $intensiveData;
            $specialColorData['intensiveWorkColor'] = '#89A8B2'; // color intensivo
        }

        return response()->json([
            'calendarData' => $calendarData,
            'expectedHours' => $expectedHours,
            'colorData' => $colorData,
            'specialColorData' => $specialColorData,
        ]);
    }

    public function getTasksByDate(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['error' => 'No date provided'], 400);
        }

        // Obtener todos los timesheets del usuario para esa fecha
        $query = DB::table('timesheets')
            ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
            ->join('projects', 'timesheets.project_id', '=', 'projects.id')
            ->join('workspaces', 'projects.workspace', '=', 'workspaces.id')
            ->leftJoin('milestones', 'tasks.milestone_id', '=', 'milestones.id')
            ->leftJoin('task_types', 'tasks.type_id', '=', 'task_types.id')
            ->leftJoin('custom_tasks', 'tasks.id', '=', 'custom_tasks.id_task')
            ->where('timesheets.date', '=', $date)
            ->whereRaw("find_in_set('" . $userId . "',tasks.assign_to)")
            ->select('projects.name as project_name', 'milestones.title as milestone_title', 'task_types.name as task_title', 'timesheets.time', 'tasks.id as task_id', 'workspaces.name as workspace_name', 'custom_tasks.name as custom_task_name');

        if ($request->has('workspace_id') && $request->get('all') != 'true') {
            $query->where('projects.workspace', '=', $request->workspace_id);
        }

        $timesheets = $query->get();

        // Agrupar por proyecto y milestone para mostrar desglose
        $tasks = [];
        $totalMinutes = 0;

        foreach ($timesheets as $timesheet) {
            $rawTaskTitle = $timesheet->task_title ?? '';
            // task_types.name is coming from DB; translate it here (server-side)
            // so the frontend can just render the already-translated label.
            $translatedTaskTitle = __($rawTaskTitle);

            if (strtolower($rawTaskTitle) == 'custom') {
                $customName = $timesheet->custom_task_name;
                if (!empty($customName)) {
                    $rawTaskTitle = $customName;
                    $translatedTaskTitle = $customName;
                }
            }

            // Keep grouping key stable (use raw DB values), but display translated fallback.
            $milestoneTitleKey = $timesheet->milestone_title ?? $rawTaskTitle;
            $milestoneTitle = $timesheet->milestone_title ?? $translatedTaskTitle;
            // Clave única considerando el workspace para diferenciar proyectos con mismo nombre en diferentes workspaces
            $key = $timesheet->project_name . '_' . $milestoneTitleKey . '_' . $rawTaskTitle . '_' . $timesheet->workspace_name;

            if (!isset($tasks[$key])) {
                $tasks[$key] = [
                    'projectName' => $timesheet->project_name,
                    'workspaceName' => $timesheet->workspace_name,
                    'milestoneTitle' => $milestoneTitle,
                    'taskTitle' => $translatedTaskTitle,
                    'taskId' => $timesheet->task_id,
                    'totalTime' => 0,
                    'totalMinutes' => 0
                ];
            }

            list($h, $m, $s) = explode(':', $timesheet->time);
            $minutes = ($h * 60) + $m;
            $tasks[$key]['totalMinutes'] += $minutes;
            $totalMinutes += $minutes;
        }

        // Formatear horas
        foreach ($tasks as &$task) {
            $hours = floor($task['totalMinutes'] / 60);
            $minutes = $task['totalMinutes'] % 60;
            $task['totalTime'] = sprintf('%02d:%02d', $hours, $minutes);
        }

        $totalHours = floor($totalMinutes / 60);
        $totalMins = $totalMinutes % 60;
        $formattedTotalHours = sprintf('%02d:%02d', $totalHours, $totalMins);

        return response()->json([
            'tasks' => array_values($tasks),
            'formattedTotalHours' => $formattedTotalHours,
            'date' => $date
        ]);
    }
}
