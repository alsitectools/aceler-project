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
use Illuminate\Support\Facades\App;
use App\Services\AverageTimeService;


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

    public function getAllAverageTimes(int $workspaceID): array
    {
        return app(AverageTimeService::class)->getAllAverageTimes($workspaceID);
    }

    public function index($slug = '')
    {
        $userObj = Auth::user();
        if ($userObj->type == 'admin') {
            $users = User::select('users.*')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
                ->where('user_workspaces.permission', '=', 'Member')->distinct()->get();
            return view('users.index', compact('users'));
        }

        $technicians = User::where('type', '=', 'user')->get();
        $comerciales = User::where('type', '=', 'client')->get();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('complete', '=', '1')->first();

            //GET ALL PROJECTS AND MILESTONE OF THE CURRENT WORKSPACE
            $totalProject = Project::where('workspace', '=', $currentWorkspace->id)->count();
            $totalWorkspaceMilestones = Milestone::join("projects", "projects.id", "=", "milestones.project_id")
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->count();
            $notAssignedMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.milestone_assigned_to_user', '')
                ->count();
            $assignedMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.milestone_assigned_to_user', Auth::user()->id)
                ->where('milestones.status', '<=', 3)
                ->count();
            $forReviewMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.milestone_assigned_to_user', Auth::user()->id)
                ->where('milestones.status', 3)
                ->count();

            $activeMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereIn('milestones.status', [1, 2])
                ->where('milestones.is_waiting', 0)
                ->where(function($q) {
                    $q->whereNotNull('milestones.milestone_assigned_to_user')
                      ->where('milestones.milestone_assigned_to_user', '!=', '');
                })
                ->count();

            $totalMilestonesGlobal = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->count();

            $reviewMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.status', 3)
                ->count();

            $unassignedMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where(function($q) {
                    $q->where('milestone_assigned_to_user', '')->orWhereNull('milestone_assigned_to_user');
                })
                ->count();

            $finishedMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.status', 4)
                ->count();

            $pausedMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.is_waiting', 1)
                ->count();

            // My activity - user's milestones by status
            $myEnPlazoMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->whereIn('milestones.status', [1, 2])
                ->where('milestones.is_waiting', 0)
                ->where('milestones.end_date', '>=', now()->toDateString())
                ->count();

            $myFueraPlazoMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->whereIn('milestones.status', [1, 2])
                ->where('milestones.is_waiting', 0)
                ->where('milestones.end_date', '<', now()->toDateString())
                ->count();

            $myEnRevisionMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.status', 3)
                ->count();

            $myFinalizadosMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.status', 4)
                ->count();

            $myEnPausaMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.is_waiting', 1)
                ->count();

            // My activity - user's milestones by priority
            $myAltaPriorityMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.priority', 'alta')
                ->count();

            $myMediaPriorityMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.priority', 'media')
                ->count();

            $myBajaPriorityMilestones = Milestone::join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, milestones.milestone_assigned_to_user)", [$userObj->id])
                ->where('milestones.priority', 'baja')
                ->count();

            // My tasks (assigned to user) in milestones of this workspace
            $myTaskTotal = Task::join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$userObj->id])
                ->whereIn('milestones.status', [2, 3])
                ->count();

            // Subquery: latest review state per task (max id per task_id)
            $latestReview = DB::table('task_review_states')
                ->select('task_id', 'state_code')
                ->whereIn('id', function ($q) {
                    $q->selectRaw('MAX(id)')->from('task_review_states')->groupBy('task_id');
                });

            $myTaskReviewed = Task::join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->joinSub($latestReview, 'latest_review', function ($join) {
                    $join->on('latest_review.task_id', '=', 'tasks.id');
                })
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$userObj->id])
                ->whereIn('milestones.status', [2, 3])
                ->where('latest_review.state_code', 'reviewed')
                ->count();

            $myTaskChanges = Task::join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->joinSub($latestReview, 'latest_review', function ($join) {
                    $join->on('latest_review.task_id', '=', 'tasks.id');
                })
                ->where('projects.workspace', $currentWorkspace->id)
                ->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$userObj->id])
                ->whereIn('milestones.status', [2, 3])
                ->where('latest_review.state_code', 'changes')
                ->count();

            $today = max(
                Carbon::now()->toDateString(),
                DB::table('timesheets')->where('created_by', $userObj->id)->max('date')
            );
            $monthStart = Carbon::parse($today)->startOfMonth()->toDateString();

            $myMonthSeconds = DB::table('timesheets')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('timesheets.created_by', $userObj->id)
                ->whereBetween('timesheets.date', [$monthStart, $today])
                ->sum(DB::raw('TIME_TO_SEC(timesheets.time)'));

            $myMonthHours = $myMonthSeconds > 0
                ? sprintf('%02d:%02d', floor($myMonthSeconds / 3600), floor(($myMonthSeconds % 3600) / 60))
                : '00:00';

            $myMonthTimesheets = DB::table('timesheets')
                ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->join('milestones', 'milestones.id', '=', 'tasks.milestone_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->leftJoin('task_types', 'task_types.id', '=', 'tasks.type_id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('timesheets.created_by', $userObj->id)
                ->whereBetween('timesheets.date', [$monthStart, $today])
                ->orderBy('timesheets.date')
                ->get([
                    'timesheets.date',
                    'timesheets.time',
                    'timesheets.created_at',
                    'timesheets.updated_at',
                    'projects.name as project_name',
                    'milestones.title as milestone_title',
                    'task_types.name as task_name',
                ]);

            $totalTaskByType = Task::join('milestones', 'tasks.milestone_id', '=', 'milestones.id')
                ->join('projects', 'milestones.project_id', '=', 'projects.id')
                ->where('projects.workspace', $currentWorkspace->id)
                ->where('milestones.status', '!=', 4)
                ->where('milestones.is_waiting', '!=', 1)
                ->join('project_types', 'projects.type', '=', 'project_types.id')
                ->select('project_types.name', DB::raw('count(tasks.id) as count'))
                ->groupBy('project_types.id', 'project_types.name')
                ->pluck('count', 'name')
                ->toArray();

            $totalTask = array_sum($totalTaskByType);
            /*$totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")
                ->where("user_id", "=", $userObj->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)->count();*/

            if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member') {

                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")
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
                'totalWorkspaceMilestones',
                'notAssignedMilestones',
                'assignedMilestones',
                'forReviewMilestones',
                'activeMilestones',
                'totalMilestonesGlobal',
                'reviewMilestones',
                'unassignedMilestones',
                'finishedMilestones',
                'pausedMilestones',
                'myEnPlazoMilestones',
                'myFueraPlazoMilestones',
                'myEnRevisionMilestones',
                'myFinalizadosMilestones',
                'myEnPausaMilestones',
                'myAltaPriorityMilestones',
                'myMediaPriorityMilestones',
                'myBajaPriorityMilestones',
                'totalTaskByType',
                'myTaskTotal',
                'myTaskReviewed',
                'myTaskChanges',
                'myMonthHours',
                'myMonthTimesheets'
            ));

            // }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }

    public function showTutorial($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        return view('tutorial.tutorialHome', compact('currentWorkspace'));
    }
}
