<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Project;
use App\Models\Milestone;

class HomeController extends Controller
{
    public function landingPage()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        } else {

            return redirect('login');
        }
        $setting = Utility::getAdminPaymentSettings();
    }
    public function LoginWithAdmin(Request $request, User $user,  $id)
    {
        $user =    User::find($id);
        $from =     Auth::user();
        if ($user && auth()->check()) {
            $manager = app('impersonate');
            $manager->take($from, $user);

            return redirect('dashboard');
        }
    }

    public function ExitAdmin(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect('/home');
    }

    public function getAllAverageTimes($workspaceID)
    {
        // Obtener todos los milestones de todos los proyectos dentro del workspace
        $milestones = DB::table('milestones')
            ->join('projects', 'projects.id', '=', 'milestones.project_id')
            ->where('projects.workspace', '=', $workspaceID)
            ->select(
                'projects.start_date as project_start_date',
                'projects.end_date as project_end_date',
                'milestones.start_date',
                'milestones.end_date',
                'milestones.id', 
                'milestones.title', 
                'milestones.task_start_date', 
                'milestones.finalization_date'
            )
            ->get();

        $groupedMilestones = [];

        foreach ($milestones as $milestone) {
            $year = date('Y', strtotime($milestone->project_start_date));
            $month = date('F', strtotime($milestone->start_date)); // Nombre del mes
            $quarter = 'Q' . ceil(date('n', strtotime($milestone->start_date)) / 3); // Trimestre

            $creation_date = Carbon::parse($milestone->start_date);
            $estimated_date = Carbon::parse($milestone->end_date);
            $task_start_date = Carbon::parse($milestone->task_start_date);
            $finalization_date = $milestone->finalization_date ? Carbon::parse($milestone->finalization_date) : Carbon::now();

            // Cálculos de tiempo
            $deliveryTime = $creation_date->diffInDays($finalization_date);
            $startUpTime = $creation_date->diffInDays($task_start_date);
            $delayTime = max(0, $estimated_date->diffInDays($finalization_date, false)); // Evita valores negativos
            $workingTime = $deliveryTime - $startUpTime - $delayTime;

            // Inicializar la estructura del año si no existe
            if (!isset($groupedMilestones[$year])) {
                $groupedMilestones[$year] = [
                    'months' => [],
                    'quarters' => [],
                    'yearly' => [
                        'total' => 0,
                        'sumDelivery' => 0,
                        'sumStartUp' => 0,
                        'sumWorking' => 0,
                        'sumDelay' => 0,
                        'averageDelivery' => 0,
                        'averageStartUp' => 0,
                        'averageWorking' => 0,
                        'averageDelay' => 0,
                    ]
                ];
            }

            // ---- AGRUPACIÓN POR MESES ----
            if (!isset($groupedMilestones[$year]['months'][$month])) {
                $groupedMilestones[$year]['months'][$month] = [
                    'total' => 0,
                    'sumDelivery' => 0,
                    'sumStartUp' => 0,
                    'sumWorking' => 0,
                    'sumDelay' => 0,
                    'averageDelivery' => 0,
                    'averageStartUp' => 0,
                    'averageWorking' => 0,
                    'averageDelay' => 0,
                ];
            }

            // Acumular valores
            $groupedMilestones[$year]['months'][$month]['total']++;
            $groupedMilestones[$year]['months'][$month]['sumDelivery'] += $deliveryTime;
            $groupedMilestones[$year]['months'][$month]['sumStartUp'] += $startUpTime;
            $groupedMilestones[$year]['months'][$month]['sumWorking'] += $workingTime;
            $groupedMilestones[$year]['months'][$month]['sumDelay'] += $delayTime;

            // ---- AGRUPACIÓN POR TRIMESTRES ----
            if (!isset($groupedMilestones[$year]['quarters'][$quarter])) {
                $groupedMilestones[$year]['quarters'][$quarter] = [
                    'total' => 0,
                    'sumDelivery' => 0,
                    'sumStartUp' => 0,
                    'sumWorking' => 0,
                    'sumDelay' => 0,
                    'averageDelivery' => 0,
                    'averageStartUp' => 0,
                    'averageWorking' => 0,
                    'averageDelay' => 0,
                ];
            }

            // Acumular valores
            $groupedMilestones[$year]['quarters'][$quarter]['total']++;
            $groupedMilestones[$year]['quarters'][$quarter]['sumDelivery'] += $deliveryTime;
            $groupedMilestones[$year]['quarters'][$quarter]['sumStartUp'] += $startUpTime;
            $groupedMilestones[$year]['quarters'][$quarter]['sumWorking'] += $workingTime;
            $groupedMilestones[$year]['quarters'][$quarter]['sumDelay'] += $delayTime;

            // ---- AGRUPACIÓN POR AÑO (YEARLY) ----
            $groupedMilestones[$year]['yearly']['total']++;
            $groupedMilestones[$year]['yearly']['sumDelivery'] += $deliveryTime;
            $groupedMilestones[$year]['yearly']['sumStartUp'] += $startUpTime;
            $groupedMilestones[$year]['yearly']['sumWorking'] += $workingTime;
            $groupedMilestones[$year]['yearly']['sumDelay'] += $delayTime;
        }

        // Calcular promedios
        foreach ($groupedMilestones as $year => &$yearData) {
            foreach ($yearData['months'] as $month => &$monthData) {
                if ($monthData['total'] > 0) {
                    $monthData['averageDelivery'] = round($monthData['sumDelivery'] / $monthData['total']);
                    $monthData['averageStartUp'] = round($monthData['sumStartUp'] / $monthData['total']);
                    $monthData['averageWorking'] = round($monthData['sumWorking'] / $monthData['total']);
                    $monthData['averageDelay'] = round($monthData['sumDelay'] / $monthData['total']);
                }
                unset($monthData['sumDelivery'], $monthData['sumStartUp'], $monthData['sumWorking'], $monthData['sumDelay'], $monthData['total']);
            }

            foreach ($yearData['quarters'] as $quarter => &$quarterData) {
                if ($quarterData['total'] > 0) {
                    $quarterData['averageDelivery'] = round($quarterData['sumDelivery'] / $quarterData['total']);
                    $quarterData['averageStartUp'] = round($quarterData['sumStartUp'] / $quarterData['total']);
                    $quarterData['averageWorking'] = round($quarterData['sumWorking'] / $quarterData['total']);
                    $quarterData['averageDelay'] = round($quarterData['sumDelay'] / $quarterData['total']);
                }
                unset($quarterData['sumDelivery'], $quarterData['sumStartUp'], $quarterData['sumWorking'], $quarterData['sumDelay'], $quarterData['total']);
            }

            // Calcular promedios anuales
            if ($yearData['yearly']['total'] > 0) {
                $yearData['yearly']['averageDelivery'] = round($yearData['yearly']['sumDelivery'] / $yearData['yearly']['total']);
                $yearData['yearly']['averageStartUp'] = round($yearData['yearly']['sumStartUp'] / $yearData['yearly']['total']);
                $yearData['yearly']['averageWorking'] = round($yearData['yearly']['sumWorking'] / $yearData['yearly']['total']);
                $yearData['yearly']['averageDelay'] = round($yearData['yearly']['sumDelay'] / $yearData['yearly']['total']);
            }
            unset($yearData['yearly']['sumDelivery'], $yearData['yearly']['sumStartUp'], $yearData['yearly']['sumWorking'], $yearData['yearly']['sumDelay'], $yearData['yearly']['total']);
        }
       //\Log::debug("Milestones organizados por año: " . json_encode($groupedMilestones, JSON_PRETTY_PRINT));

        return $groupedMilestones;
    }

    public function index($slug = '')
    {
        $userObj = Auth::user();
        if ($userObj->type == 'admin') {
            $users = User::select('users.*')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
                ->where('user_workspaces.permission', '=', 'Member')->distinct()->get();
            return view('users.index', compact('users'));
        }

        $technicians = User::where('type','=','user')->get();
        $comerciales = User::where('type','=','client')->get();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('complete', '=', '1')->first();

            //GET ALL PROJECTS AND MILESTONE OF THE CURRENT WORKSPACE
            $totalProject = Project::where('workspace', '=', $currentWorkspace->id)->count();
            $totalWorkspaceMilestones = Milestone::join("projects", "projects.id", "=", "milestones.project_id")
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->count();

            /*$totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")
                ->where("user_id", "=", $userObj->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)->count();*/

            if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member') {
                
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)->count();

                //TASK FOR THE ACTUAL USER
                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)->count();

                //MILESTONE FOR THE ACTUAL USER
                $totalMilestones = UserProject::join("milestones", "milestones.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)
                    ->whereRaw("find_in_set('" . $userObj->id . "',milestones.assign_to)")->count();

                $completeTask = 0; // inicializar el contador a cero por defecto

                // if ($doneStage) { // Verificar si $doneStage no es nulo
                //     $completeTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")
                //         ->join("projects", "projects.id", "=", "client_projects.project_id")
                //         ->where('projects.workspace', '=', $currentWorkspace->id)
                //         ->where("client_id", "=", $userObj->id)
                //         ->where('tasks.status', '=', $doneStage->id)
                //         ->count();
                // }
                // $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")
                //     ->join("projects", "projects.id", "=", "user_projects.project_id")
                //     ->where("user_id", "=", $userObj->id)
                //     ->where('projects.workspace', '=', $currentWorkspace->id)
                //     ->where('tasks.status', '=', $doneStage->id)->count();

                $tasks = Task::select([
                    'tasks.*',
                    // 'stages.name as status',
                    // 'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    // ->join("stages", "stages.id", "=", "tasks.status")
                    // ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)->limit(3)->get();
                // ->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
            } else {
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->where('bug_reports.assign_to', '=', $userObj->id)->count();

                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)
                    ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();

                $totalMilestones = UserProject::join("milestones", "milestones.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)
                    ->whereRaw("find_in_set('" . $userObj->id . "',milestones.assign_to)")->count();

                $completeTask = 0; // inicializar el contador a cero por defecto

                if ($doneStage) { // Verificar si $doneStage no es nulo
                    $completeTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")
                        ->join("projects", "projects.id", "=", "client_projects.project_id")
                        ->where('projects.workspace', '=', $currentWorkspace->id)
                        ->where("client_id", "=", $userObj->id)
                        // ->where('tasks.status', '=', $doneStage->id)
                        ->count();
                }
                // $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")
                //     ->join("projects", "projects.id", "=", "user_projects.project_id")
                //     ->where("user_id", "=", $userObj->id)
                //     ->where('projects.workspace', '=', $currentWorkspace->id)
                //     ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                //     ->where('tasks.status', '=', $doneStage->id)->count();

                $tasks = Task::select([
                    'tasks.*',
                    // 'stages.name as status',
                    // 'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    // ->join("stages", "stages.id", "=", "tasks.status")
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->limit(3)->get();
                // ->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
            }

            $totalTechni = User::where('currant_workspace', '=', $currentWorkspace->id)->where('type', '=', 'user')->count();
            $totalSales = User::where('currant_workspace', '=', $currentWorkspace->id)->where('type', '=', 'client')->count();

            /* NOW WE USE ALL WORKSPACE PROJECTS INSTED ONLY USER
            $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")
                ->where("user_id", "=", $userObj->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');*/

            $projectProcess = Project::where('workspace', '=', $currentWorkspace->id)
                ->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');

            $arrProcessPer = [];
            $arrProcessLabel = [];

            foreach ($projectProcess as $lable => $process) {
                $arrProcessLabel[] = $lable;
                if ($totalProject == 0) {
                    $arrProcessPer[] = 0.00;
                } else {
                    $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                }
            }
            $arrProcessClass = [
                'text-success',
                'text-primary',
                'text-danger',
            ];

            $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                'workspace_id' => $currentWorkspace->id,
                'duration' => 'week',
            ]);

            $tasksUsers = Task::orderBy('tasks.id', 'desc')
                ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                ->join("projects", "projects.id", "=", "user_projects.project_id")
                // ->join("stages", "stages.id", "=", "tasks.status")
                ->join("users", function ($join) {
                    $join->on("users.id", "=", DB::raw("CAST(SUBSTRING_INDEX(tasks.assign_to, ',', 1) AS SIGNED)"))
                        ->orWhere("users.id", "=", DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -2), ',', 1) AS SIGNED)"))
                        ->orWhere("users.id", "=", DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -1), ',', 1) AS SIGNED)"));
                })
                ->where("user_id", "=", $userObj->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->limit(5)
                ->select(['users.name', 'users.id'])
                ->distinct()
                ->get()
                ->pluck('name', 'id')
                ->toArray();

            $taskTypes = TaskType::all()->toArray();

            //getting all averagesTimes of a workspace
            $averageTimes = $this->getAllAverageTimes($currentWorkspace->id);
            $averageTimesKeys = array_keys($averageTimes);

            return view('home', compact(
                'currentWorkspace',
                'totalProject',
                'totalBugs',
                'totalTask',
                'totalMilestones',
                'totalTechni',
                'totalSales',
                'arrProcessLabel',
                'arrProcessPer',
                'arrProcessClass',
                'completeTask',
                'tasks',
                'chartData',
                'tasksUsers',
                'taskTypes',
                'technicians',
                'comerciales',
                'projectProcess',
                'averageTimes',
                'averageTimesKeys',
                'totalWorkspaceMilestones'
            ));

            // }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }

    public function showTutorial($slug){
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        return view('tutorial.tutorialHome', compact('currentWorkspace'));
    }
}
