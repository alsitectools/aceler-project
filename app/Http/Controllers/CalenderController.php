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

    // Function to modify colors of the timesheet
    public function getTimesheetColor()
    {
        $userId = Auth::id();

        // Fetch all timesheets for the user
        $timesheets = DB::table('timesheets')
            ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
            ->join('users', 'tasks.assign_to', '=', 'users.id')
            ->select('tasks.*', 'timesheets.*')
            ->where('users.id', '=', $userId)
            ->get();

        Log::debug("getTimesheetColor INFO:::" . $timesheets);

        // Prepare calendar data
        $calendarData = [];
        foreach ($timesheets as $timesheet) {
            $dayOfWeek = $this->getDayOfWeek($timesheet->date);
            $calendarData[] = [
                'date' => $timesheet->date,
                'dayOfWeek' => $dayOfWeek,
                'hours' => $timesheet->time,
            ];
        }

        Log::debug("calendarData INFO:::" . json_encode($calendarData));

        // Get user timetable
        $timetable = UserTimetable::where('user_id', $userId)->first();
        $expectedHours = [
            'monday' => $timetable->monday,
            'tuesday' => $timetable->tuesday,
            'wednesday' => $timetable->wednesday,
            'thursday' => $timetable->thursday,
            'friday' => $timetable->friday,
            'saturday' => $timetable->saturday,
            'sunday' => $timetable->sunday,
        ];

        Log::debug("expectedHours INFO:::" . json_encode($expectedHours));

        // Identify all unique weeks from calendarData
        $uniqueWeeks = [];
        foreach ($calendarData as $data) {
            $weekKey = Carbon::parse($data['date'])->startOfWeek()->toDateString();
            $uniqueWeeks[$weekKey] = $data['date'];
        }

        // Prepare color data for all weeks
        $colorData = [];

        foreach ($uniqueWeeks as $weekStartDate) {
            // Get all days in the week for the current date
            $weekDays = $this->getWeekDaysOfMonth($weekStartDate);
            $weekDates = $weekDays['datePeriod'];

            foreach ($expectedHours as $day => $expectedHour) {
                $dayColor = '#e06c71'; // Default color
                $workedHours = 0;
                $currentDate = null;

                // Find the corresponding date for the day
                foreach ($weekDates as $date) {
                    if (strtolower($this->getDayOfWeek($date)) == $day) {
                        $currentDate = $date;
                        break;
                    }
                }

                // Check if there are hours worked on this day
                foreach ($calendarData as $dataDay) {
                    if ($dataDay['date'] === $currentDate) {
                        $workedHours = $dataDay['hours'];
                        
                        if ($workedHours == 0) {
                            $dayColor = '#e06c71'; // No hours worked
                        } elseif ($workedHours < $expectedHour) {
                            $dayColor = '#fcf75e'; // Partial hours worked
                        } elseif ($workedHours == $expectedHour) {
                            $dayColor = '#cbd7bf'; // Hours met
                        } else {
                            $dayColor = '#b2e2f2'; // Overtime
                        }
                        break;
                    }
                }

                if ($expectedHour !== null) {
                    // Add data to colorData
                    $colorData[] = [
                        'dayOfWeek' => ucfirst($day),
                        'date' => $currentDate,
                        'hours' => $workedHours,
                        'color' => $dayColor,
                    ];
                }
            }
        }

        Log::debug("colorData INFO:::" . json_encode($colorData));

        return response()->json([
            'calendarData' => $calendarData,
            'expectedHours' => $expectedHours,
            'colorData' => $colorData,
        ]);
    }

}
