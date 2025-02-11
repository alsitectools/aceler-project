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
        $timetable = Timetable::where('user_id', $userId)->first();
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

    public function getTimesheetColor()
    {
        $userId = Auth::id();

        // Obtener todas las tareas del usuario
        $timesheets = DB::table('timesheets')
            ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
            ->join('users', 'tasks.assign_to', '=', 'users.id')
            ->select('tasks.*', 'timesheets.*')
            ->where('users.id', '=', $userId)
            ->get();

        // Obtener el horario del usuario
        $timetable = UserTimetable::where('user_id', $userId)->first();
        if (!$timetable) {
            return response()->json(['error' => 'No timetable found'], 404);
        }

        $expectedHours = [
            'monday' => $timetable->monday,
            'tuesday' => $timetable->tuesday,
            'wednesday' => $timetable->wednesday,
            'thursday' => $timetable->thursday,
            'friday' => $timetable->friday,
            'saturday' => $timetable->saturday,
            'sunday' => $timetable->sunday,
        ];

        $calendarData = [];
        foreach ($timesheets as $timesheet) {
            $dayOfWeek = $this->getDayOfWeek($timesheet->date);
            $calendarData[] = [
                'date' => $timesheet->date,
                'dayOfWeek' => $dayOfWeek,
                'hours' => $timesheet->time,
            ];
        }

        $today = Carbon::now()->toDateString();
        $currentYear = Carbon::now()->year;

        // Generar todas las semanas del año
        $startOfYear = Carbon::create($currentYear, 1, 1)->startOfWeek();
        $endOfYear = Carbon::create($currentYear, 12, 31)->endOfWeek();

        $period = CarbonPeriod::create($startOfYear, '1 week', $endOfYear);

        $colorData = [];

        foreach ($period as $weekStartDate) {
            $weekStartDate = $weekStartDate->startOfWeek();
            $weekDays = $this->getWeekDaysOfMonth($weekStartDate->toDateString());

            foreach ($weekDays['datePeriod'] as $currentDate) {
                $dayOfWeek = strtolower($this->getDayOfWeek($currentDate));
                $expectedHour = $expectedHours[$dayOfWeek] ?? null;

                // Excluir días futuros
                if ($currentDate > $today) {
                    continue;
                }

                // Comprobar si hay horas trabajadas para este día
                $workedHours = null;
                $dayColor = '#e06c71'; // Color rojo por defecto para días sin imputaciones

                foreach ($calendarData as $dataDay) {
                    if ($dataDay['date'] === $currentDate) {
                        $workedHours = $dataDay['hours'];

                        $workedHoursFormatted = date('H:i', strtotime($workedHours));

                        if ($workedHoursFormatted == '00:00') {
                            $dayColor = '#e06c71'; // No se imputaron horas (rojo)
                        } elseif ($workedHoursFormatted < $expectedHour) {
                            $dayColor = '#fcf75e'; // Horas parciales (amarillo)
                        } elseif ($workedHoursFormatted == $expectedHour) {
                            $dayColor = '#89e186'; // Horas completas (verde)
                        } elseif($workedHoursFormatted > $expectedHour) {
                            $dayColor = '#b2e2f2'; // Horas extras (azul)
                        }
                        
                        break;
                    }
                }

                // Si no hay horas imputadas y es un día pasado, asignar 00:00
                if (!$workedHours && $currentDate < $today && $expectedHour !== null) {
                    $workedHours = '00:00';
                }

                // Agregar al colorData
                if ($expectedHour !== null) {
                    $colorData[] = [
                        'dayOfWeek' => ucfirst($dayOfWeek),
                        'date' => $currentDate,
                        'hours' => $workedHours ? date('H:i', strtotime($workedHours)) : '00:00',
                        'color' => $dayColor,
                    ];
                }
            }
        }

        //get user holidays/intensive workday

        $rangeDays = DB::table('user_timetable')
        ->where('user_id', $userId)
        ->select('range_holidays', 'range_intensive_workday')
        ->first();
    
        // Inicializar variables de resultado
        $specialColorData = [];
        
        // Procesar el campo `range_holidays` si no es null
        if (!is_null($rangeDays->range_holidays)) {
            $holidays = json_decode($rangeDays->range_holidays, true); // Decodificar JSON
        
            $holidayDays = [];
            foreach ($holidays as $day) {
                $holidayDays[] = $day; // Asumiendo que cada elemento en JSON es un día individual
            }
        
            // Agregar holidays al diccionario con color
            $specialColorData['holidayRange'] = $holidayDays;
            $specialColorData['holidayColor'] = '#91DDCF'; // Color rosa para días festivos
        }
        
        // Procesar el campo `range_intensive_workday` si no es null
        if (!is_null($rangeDays->range_intensive_workday)) {
            $intensiveWorkdays = json_decode($rangeDays->range_intensive_workday, true);
        
            $intensiveData = [];
            foreach ($intensiveWorkdays as $hours => $days) {
                foreach ($days as $day) {
                    $intensiveData[$hours][] = $day; // Agrupando los días bajo las horas específicas
                }
            }
        
            // Agregar intensive workdays al diccionario con color
            $specialColorData['intensiveWorkRange'] = $intensiveData;
            $specialColorData['intensiveWorkColor'] = '#89A8B2'; // Color púrpura para trabajo intensivo
        }

        
        return response()->json([
            'calendarData' => $calendarData,
            'expectedHours' => $expectedHours,
            'colorData' => $colorData,
            'specialColorData' => $specialColorData,
        ]);
    }

}
