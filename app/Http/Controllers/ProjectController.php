<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // Importar Storage para gestionar archivos
use App\Exports\projectsExport;
use Illuminate\Http\UploadedFile;
use App\Imports\projectsImport;
use App\Models\ActivityLog;
use App\Models\BugComment;
use App\Models\Puntuacion;
use App\Models\PuntuacionTarea;
use App\Models\System;
use App\Models\BugFile;
use App\Models\BugReport;
use App\Models\BugStage;
use App\Models\Client;
use App\Models\ClientProject;
use App\Models\ClientsMo;
use App\Models\Delegation;
use App\Models\Notification;
use App\Models\Comment;
use App\Models\Mail\SendInvication;
use App\Models\Mail\SendLoginDetail;
use App\Models\Mail\SendWorkspaceInvication;
use App\Models\Mail\ShareProjectToClient;
use App\Models\Milestone;
use App\Models\MilestonePhases;
use App\Models\MilestoneStageProject;
use App\Models\MilestoneStages;
use App\Models\CustomTasks;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\MasterObra;
use App\Models\PotentialClient;
use App\Models\ProjectFile;
use App\Models\Stage;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskFile;
use App\Models\Timesheet;
use App\Models\TimeTracker;
use App\Models\User;
use App\Models\Log;
use App\Models\MilestoneFile;
use App\Models\UserProject;
use App\Models\UserTimetable;
use App\Models\UserWorkspace;
use App\Models\Utility;
use App\Models\Workspace;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use SendGrid;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Response;
use App\Helpers\AxaptaExportHelper;
use App\Models\ExportBatch;
use App\Models\ExportLedgerLine;

class ProjectController extends Controller
{

    public function index($slug)
    {
        /* Cuando actualizamos la columna de ultima actualizacion del proyecto?
            $project = Project::find($project_id);
            $project->updated_at = now(); // Establece `updated_at` a la fecha y hora actuales
            $project->save(); */

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        // Cargamos los proyectos con la relación 'delegation' para evitar N+1 queries
        $projects = Project::with('delegation')
            ->where('projects.workspace', $currentWorkspace->id)
            ->get();

        $project_type = ProjectType::select('id', 'name')->get();

        return view('projects.index', compact('currentWorkspace', 'projects', 'project_type'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $workspace = $request->input('workspace');

        // Realiza la consulta
        $projects = Project::where('name', 'LIKE', "%{$query}%")
            ->where('workspace', $workspace)
            ->get(['id', 'name']);

        return response()->json($projects);
    }

    /**
     * Sanitiza una cadena para ser usada como nombre de DIRECTORIO.
     * Reemplaza espacios y caracteres especiales con guiones bajos.
     *
     * @param string $string Cadena a sanitizar
     * @return string Cadena sanitizada
     */
    private function sanitizePath($string)
    {
        // Reemplaza espacios por guiones bajos
        $string = str_replace(' ', '_', $string);
        // Reemplaza cualquier carácter que NO sea alfanumérico, guion bajo, guion medio o punto por guion bajo
        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $string);
    }

    /**
     * Normaliza un nombre de archivo SIN cambiar caracteres legibles (á, ñ, espacios, etc.).
     * Solo evita que vengan segmentos de ruta (/, \\) o bytes nulos.
     */
    private function cleanFileName(string $fileName): string
    {
        $fileName = str_replace("\0", '', $fileName);
        // Unificar separadores por seguridad y extraer el basename
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = basename($fileName);
        return trim($fileName);
    }

    private function parseTimeToDecimal(?string $time): float
    {
        if (empty($time)) {
            return 0.0;
        }

        $parts = explode(':', $time);
        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);

        return $hours + ($minutes / 60);
    }

    private function formatSecondsAsHoursMinutes(int $totalSeconds): string
    {
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function buildMySummaryChartData($projectSummaries, string $totalImputedTime, string $rangeLabel): array
    {
        $projects = $projectSummaries->map(function ($summary) {
            return [
                'name' => $summary->name,
                'short_name' => Str::limit($summary->name, 18),
                'hours' => (float) $summary->decimal_total_time,
                'formatted_time' => $summary->formatted_total_time,
                'project_url' => $summary->project_url,
            ];
        })->values();

        return [
            'projects' => $projects,
            'maxHours' => round((float) $projects->max('hours'), 2),
            'totalTime' => $totalImputedTime,
            'rangeLabel' => $rangeLabel,
        ];
    }

    private function resolveMySummaryDateRange(Request $request): array
    {
        $today = Carbon::today();
        $rangeType = $request->input('range_type', 'preset');
        $preset = $request->input('preset', 'last_month');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $startDate = null;
        $endDate = null;
        $appliedLabel = __('Last month');

        if (
            $rangeType === 'custom'
            && is_string($startDateInput)
            && is_string($endDateInput)
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDateInput)
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDateInput)
        ) {
            $parsedStartDate = Carbon::createFromFormat('Y-m-d', $startDateInput)->startOfDay();
            $parsedEndDate = Carbon::createFromFormat('Y-m-d', $endDateInput)->endOfDay();

            if ($parsedStartDate->gt($parsedEndDate)) {
                [$parsedStartDate, $parsedEndDate] = [$parsedEndDate->copy()->startOfDay(), $parsedStartDate->copy()->endOfDay()];
            }

            $startDate = $parsedStartDate;
            $endDate = $parsedEndDate;
            $appliedLabel = __('Custom range');
        } else {
            $rangeType = 'preset';

            switch ($preset) {
                case 'last_month':
                    $startDate = $today->copy()->subDays(29)->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $appliedLabel = __('Last month');
                    break;
                case 'last_quarter':
                    $startDate = $today->copy()->subDays(89)->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $appliedLabel = __('Last quarter');
                    break;
                case 'last_year':
                    $startDate = $today->copy()->subDays(364)->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $appliedLabel = __('Last year');
                    break;
                case 'last_week':
                    $preset = 'last_week';
                    $startDate = $today->copy()->subDays(6)->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $appliedLabel = __('Last week');
                    break;
                default:
                    $preset = 'last_month';
                    $startDate = $today->copy()->subDays(29)->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $appliedLabel = __('Last month');
                    break;
            }
        }

        return [
            'rangeType' => $rangeType,
            'preset' => $preset,
            'startDateInput' => $startDate->toDateString(),
            'endDateInput' => $endDate->toDateString(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'appliedLabel' => $appliedLabel,
        ];
    }

    private function resolveMyDayDateRange(Request $request): array
    {
        $rangeType = $request->input('my_day_range_type', 'preset');
        $preset = $request->input('my_day_preset', 'this_week');
        $startDateInput = $request->input('my_day_start_date');
        $endDateInput = $request->input('my_day_end_date');

        $today = Carbon::today();
        $startDate = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endDate = $today->copy()->endOfWeek(Carbon::SUNDAY);

        if ($rangeType === 'custom' && $startDateInput && $endDateInput) {
            $parsedStart = Carbon::parse($startDateInput)->startOfDay();
            $parsedEnd = Carbon::parse($endDateInput)->endOfDay();

            if ($parsedStart->lte($parsedEnd)) {
                $startDate = $parsedStart;
                $endDate = $parsedEnd;
            } else {
                $startDate = $parsedEnd->copy()->startOfDay();
                $endDate = $parsedStart->copy()->endOfDay();
            }
        } else {
            $rangeType = 'preset';

            switch ($preset) {
                case 'all':
                    $startDate = null;
                    $endDate = null;
                    break;
                case 'today':
                    $startDate = $today->copy()->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    break;
                case 'this_month':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    break;
                case 'this_week':
                    $startDate = $today->copy()->startOfWeek(Carbon::MONDAY);
                    $endDate = $today->copy()->endOfWeek(Carbon::SUNDAY);
                    break;
                default:
                    $preset = 'this_week';
                    $startDate = $today->copy()->startOfWeek(Carbon::MONDAY);
                    $endDate = $today->copy()->endOfWeek(Carbon::SUNDAY);
                    break;
            }
        }

        return [
            'rangeType' => $rangeType,
            'preset' => $preset,
            'startDateInput' => $rangeType === 'custom' ? $startDateInput : null,
            'endDateInput' => $rangeType === 'custom' ? $endDateInput : null,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    private function getIntensiveHoursByDate(?string $rangeIntensiveWorkday): array
    {
        if (empty($rangeIntensiveWorkday)) {
            return [];
        }

        $decodedIntensive = json_decode($rangeIntensiveWorkday, true);
        if (!is_array($decodedIntensive)) {
            return [];
        }

        $intensiveHoursByDate = [];

        foreach ($decodedIntensive as $hours => $dates) {
            if (!is_array($dates)) {
                continue;
            }

            foreach ($dates as $date) {
                if (empty($date)) {
                    continue;
                }

                $intensiveHoursByDate[$date] = $hours;
            }
        }

        return $intensiveHoursByDate;
    }

    private function getExpectedHoursByDate(?UserTimetable $timeTable, ?string $date): float
    {
        if (!$timeTable || empty($date)) {
            return 0.0;
        }

        $targetDate = Carbon::parse($date)->toDateString();
        $intensiveHoursByDate = $this->getIntensiveHoursByDate($timeTable->range_intensive_workday ?? null);

        if (isset($intensiveHoursByDate[$targetDate])) {
            return $this->parseTimeToDecimal($intensiveHoursByDate[$targetDate]);
        }

        $dayOfWeek = strtolower(Carbon::parse($targetDate)->format('l'));
        return $this->parseTimeToDecimal($timeTable->$dayOfWeek ?? '00:00');
    }

    private function resolveDayColor(float $workedHours, float $expectedHour): string
    {
        if ($workedHours == 0) {
            return '#e06c71';
        }

        if ($workedHours < $expectedHour) {
            return '#fcf75e';
        }

        if ($workedHours == $expectedHour) {
            return '#89e186';
        }

        return '#b2e2f2';
    }

    private function getUserHolidayDates(int $userId): array
    {
        $timeTable = UserTimetable::where('user_id', $userId)->first();

        if (!$timeTable || empty($timeTable->range_holidays)) {
            return [];
        }

        $decodedHolidays = json_decode($timeTable->range_holidays, true);
        if (!is_array($decodedHolidays)) {
            return [];
        }

        $holidayDates = [];
        foreach (array_values($decodedHolidays) as $holidayDate) {
            if (empty($holidayDate)) {
                continue;
            }

            try {
                $holidayDates[] = Carbon::parse($holidayDate)->toDateString();
            } catch (\Throwable $e) {
                continue;
            }
        }

        return array_values(array_unique($holidayDates));
    }

    private function isUserHolidayDate(int $userId, string $date): bool
    {
        try {
            $normalizedDate = Carbon::parse($date)->toDateString();
        } catch (\Throwable $e) {
            return false;
        }

        return in_array($normalizedDate, $this->getUserHolidayDates($userId), true);
    }

    /**
     * Genera un nombre único para un archivo si ya existe uno con el mismo nombre.
     * Ejemplo: archivo.pdf -> archivo (2).pdf -> archivo (3).pdf
     *
     * @param string $fileName Nombre original del archivo
     * @param array $existingNames Array de nombres existentes
     * @return string Nombre único del archivo
     */
    private function generateUniqueFileName(string $fileName, array $existingNames): string
    {
        if (!in_array($fileName, $existingNames)) {
            return $fileName;
        }

        // ✅ Separar nombre base y extensión
        $lastDotIndex = strrpos($fileName, '.');
        if ($lastDotIndex !== false && $lastDotIndex > 0) {
            $baseName = substr($fileName, 0, $lastDotIndex);
            $extension = substr($fileName, $lastDotIndex);
        } else {
            $baseName = $fileName;
            $extension = '';
        }

        // Verificar si ya tiene un sufijo numérico como " (2)" o "_(2)"
        $originalBaseName = $baseName;
        $startCounter = 2;

        if (preg_match('/^(.+?)[ _]\((\d+)\)$/', $baseName, $matches)) {
            $originalBaseName = $matches[1];
            $startCounter = (int)$matches[2] + 1;
        }

        // Buscar el siguiente número disponible
        $counter = $startCounter;
        $newFileName = "{$originalBaseName}_({$counter}){$extension}";

        while (in_array($newFileName, $existingNames)) {
            $counter++;
            $newFileName = "{$originalBaseName}_({$counter}){$extension}";
        }

        return $newFileName;
    }


    public function tracker($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $treckers = TimeTracker::where('project_id', $id)->with('project', 'task')->get();
        $project = Project::where('id', $id)->first();

        if (isset($project) && $project != null) {
            return view('projects.tracker', compact('currentWorkspace', 'treckers', 'id', 'project'));
        } else {
            return redirect()->back()->with('error', __('Tracker Not Found.'));
        }
    }

    //Funcion que guarda en la BBDD que empleado a participado en el proyecto
    public function employeesInProject($userID, $projectID)
    {
        $project = Project::find($projectID);
        $user = User::find($userID);
        $existingRecord = UserProject::where('user_id', $user->id)->where('project_id', $project->id)->first();

        if (!$existingRecord && $project && $user) {

            $arrData = [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'permission' => json_encode(Utility::getAllPermission())
            ];

            UserProject::create($arrData);
        }
    }

    public function store($slug, Request $request)
    {
        $getReload = $request->get('isReload', false);

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        // Validación de la solicitud
        $request->validate([
            'project_type' => 'required',
            'ref_mo' => 'nullable|string',
            'name' => 'required|string',
            'clipo' => 'nullable|string',
            // 'delegacion' => $request->project_type != 'jobsite' ? 'required|exists:delegations,id' : 'nullable'

        ]);
        \Log::info(["Info de la request:" => $request->all()]);


        // Configuración de pago del administrador
        $setting = Utility::getAdminPaymentSettings();

        $potentialClient = is_numeric($request->clipo)
            ? PotentialClient::find($request->clipo)
            : null;

        $clipo = $potentialClient ? $potentialClient->name : $request->clipo;


        // Datos del proyecto a crear
        $post = $request->all();
        $post['ref_mo'] = $request->ref_mo;
        $post['type'] = $request->project_type;
        $post['clipo'] = $clipo;
        $post['start_date'] = $post['end_date'] = date('Y-m-d');
        $post['workspace'] = $currentWorkspace->id;
        $post['created_by'] = $objUser->id;
        $post['ref_delegation'] = $request->delegacion ?? null;
        // Creación del proyecto
        $objProject = Project::create($post);

        // Verificación y actualización de MasterObra si existe
        $mo_id = MasterObra::where('ref_mo', $request->ref_mo)->first();

        if ($mo_id) {
            $objMO = MasterObra::where('id', $mo_id->id)->first();
            $objMO->project_id = $objProject->id;
            $objMO->save();
        }

        // Guardar configuración del proyecto en el campo 'copylinksetting'
        $data = [
            'basic_details' => 'on',
            'member' => 'on',
            'client' => 'on',
            'attachment' => 'on',
            'bug_report' => 'on',
            'task' => 'on',
            'password_protected' => 'off'
        ];
        $objProject->copylinksetting = json_encode($data);
        $objProject->save();

        $permission = Auth::user()->type == 'admin' ? 'Owner' : 'Member';
        $this->inviteUser($objUser, $objProject, $permission);

        // Configuración de notificaciones
        $settings = Utility::getPaymentSetting($currentWorkspace->id);
        $uArr = [
            'app_name' => $setting['app_name'],
            'user_name' => Auth::user()->name,
            'project_name' => $objProject->name,
            'app_url' => env('APP_URL'),
        ];

        // Notificación en Slack y Telegram
        if (isset($settings['project_notification']) && $settings['project_notification'] == 1) {
            Utility::send_slack_msg('New Project', $currentWorkspace->id, $uArr);
        }

        //Implements activity log when a project is created

        if ($getReload) {

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $objProject->id,
                    'log_type' => 'has created a new project',
                    'remark' => json_encode(['projectName' => $objProject->name]),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Project created successfully.', 'project_id' => $objProject]);
        } else {

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $objProject->id,
                    'log_type' => 'has created a new project',
                    'remark' => json_encode(['projectName' => $objProject->name]),
                ]
            );

            return redirect()->route('projects.index', $currentWorkspace->slug)
                ->with('success', __('Project Created Successfully!'));
        }
    }

    public function export()
    {
        $name = 'projects_' . date('Y-m-d i:h:s');
        $data = Excel::download(new projectsExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function import(Request $request)
    {

        $slug = $request->slug;

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();

        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $customers = (new projectsImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($customers) - 1;
        // dd($totalCustomer);

        $errorArray = [];
        for ($i = 1; $i <= count($customers) - 1; $i++) {
            $customer = $customers[$i];

            $customerData = new Project();
            // dd($customer[0]);
            $customerData->name = $customer[0];
            $customerData->ref_mo = $customer[1];
            $customerData->type = $customer[2];
            $customerData->status = $customer[3];
            $customerData->description = $customer[4];
            $customerData->start_date = $customer[5];

            $customerData->end_date = $customer[6];
            $customerData->budget = $customer[7];
            $customerData->workspace = $currentWorkspace->id;

            $customerData->created_by = $objUser->id;

            if (empty($customerData)) {
                $errorArray[] = $customerData;
            } else {
                $customerData->save();
            }

            $Data = new UserProject();

            $Data->user_id = $objUser->id;
            $Data->project_id = $customerData->id;
            $Data->is_active = "1";

            if (empty($Data)) {
                $errorArray[] = $Data;
            } else {
                $Data->save();
            }
        }
        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg'] = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg'] = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');

            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function importFile($slug)
    {
        return view('projects.import', compact("slug"));
    }

    public function inviteUser(User $user, Project $project, $permission)
    {
        $authuser = Auth::user();
        $setting = Utility::getAdminPaymentSettings();

        // assign project
        $existingRecord = UserProject::where('user_id', $user->id)->where('project_id', $project->id)->first();

        if (!$existingRecord) {
            $arrData = [];
            $arrData['user_id'] = $user->id;
            $arrData['project_id'] = $project->id;
            $arrData['permission'] = json_encode(Utility::getAllPermission());
            UserProject::create($arrData);
            if ($permission != 'Owner') {
                try {

                    $uArr = [
                        'user_name' => $user->name,
                        'app_name'  => $setting['app_name'],
                        'project_name' => $project->name,
                        'project_status' => $project->status,
                        'app_url' => env('APP_URL'),
                    ];

                    // Send Email
                    $resp = Utility::sendEmailTemplate('Project Assigned', $user->id, $uArr);
                    // Mail::to($user->email)->send(new SendInvication($user, $project));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
                //Utility::sendNotification('project_assign', $project->workspaceData, $user->id, $project);
            }
        }
    }

    public function invite(Request $request, $slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $post = $request->all();
        $userList = $post['users_list'];

        $objProject = Project::find($projectID);
        foreach ($userList as $email) {
            $permission = 'Member';
            $registerUsers = User::where('email', $email)->first();
            if ($registerUsers) {
                $this->inviteUser($registerUsers, $objProject, $permission);
            } else {
                $arrUser = [];
                $arrUser['name'] = 'No Name';
                $arrUser['email'] = $email;
                $password = Str::random(8);
                $arrUser['password'] = Hash::make($password);
                $arrUser['currant_workspace'] = $objProject->workspace;
                $arrUser['lang'] = $currentWorkspace->lang;
                $registerUsers = User::create($arrUser);
                $registerUsers->password = $password;

                try {
                    Mail::to($email)->send(new SendLoginDetail($registerUsers));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

                $this->inviteUser($registerUsers, $objProject, $permission);
            }

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $objProject->id,
                    'log_type' => 'Invite User',
                    'remark' => json_encode(['user_id' => $registerUsers->id]),
                ]
            );
        }
        return redirect()->back()->with('success', __('Users Invited Successfully!')
            . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function userPermission($slug, $project_id, $user_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id);
        $user = User::find($user_id);
        $permissions = $user->getPermission($project_id);
        if (!$permissions) {
            $permissions = [];
        }

        return view('projects.user_permission', compact('currentWorkspace', 'project', 'user', 'permissions'));
    }

    public function userPermissionStore($slug, $project_id, $user_id, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $userProject = UserProject::where('user_id', '=', $user_id)->where('project_id', '=', $project_id)->first();
        $userProject->permission = json_encode($request->permissions);
        $userProject->save();

        return redirect()->back()->with('success', __('Permission Updated Successfully!'));
    }

    private function getProjectsPhasesAndStages($project)
    {
        if ($project->type == 3 || $project->type === 5) {
            $project->load(['milestones.phase', 'milestones.stage.stageProject']);
        } else {
            $project->load('milestones');
        }
        return $project;
    }
    public function exportProjectsToAxapta()
    {
        //el campo puntos en axapta no pilla las horas y lo pilla en hDecimal
        //el export no debe ser por suma del total, sino por dia, es decir, cada dia imputado tendra unas puntuaciones, cada linea es un dia imputado
        \Log::info("=== EXPORT AXAPTA - DELTA MODE ===");

        // Obtener todos los proyectos de tipo = 1
        $projects = Project::where('type', 1)->get();

        if ($projects->isEmpty()) {
            \Log::warning("No hay proyectos de tipo 1");
            return response()->json(['error' => 'No projects found'], 404);
        }

        $fileContent = '';
        $fieldWidths = [
            'regId' => 10,
            'fecha' => 10,
            'empresa' => 20,
            'delegacion' => 20,
            'empleado' => 20,
            'masterobrasid' => 20,
            'obra' => 20,
            'descripcion' => 50,
            'op' => 10,
            'horas' => 10,
            'ref' => 14,
            'linea' => 10,
            'hrDecimal' => 10,
            'puntos' => 10,
        ];

        // Contador global para regId
        $regId = 0;
        // Almacenar líneas a exportar
        $linesToExport = [];
        // Almacenar registros para ledger
        $ledgerRecords = [];

        // Agregar encabezado
        $headerLine = '';
        $headerLine .= str_pad('RegId', $fieldWidths['regId']);
        $headerLine .= str_pad('fecha', $fieldWidths['fecha']);
        $headerLine .= str_pad('Empresa', $fieldWidths['empresa']);
        $headerLine .= str_pad('delegacion', $fieldWidths['delegacion']);
        $headerLine .= str_pad('Empleado', $fieldWidths['empleado']);
        $headerLine .= str_pad('Masterobrasid', $fieldWidths['masterobrasid']);
        $headerLine .= str_pad('Obra', $fieldWidths['obra']);
        $headerLine .= str_pad('Descripcion', $fieldWidths['descripcion']);
        $headerLine .= str_pad('Op', $fieldWidths['op']);
        $headerLine .= str_pad('Horas', $fieldWidths['horas']);
        $headerLine .= str_pad('Ref', $fieldWidths['ref']);
        $headerLine .= str_pad('Linea', $fieldWidths['linea']);
        $headerLine .= str_pad('HrDecimal', $fieldWidths['hrDecimal']);
        $headerLine .= str_pad('Puntos', $fieldWidths['puntos']);

        $fileContent .= $headerLine . PHP_EOL;

        // Procesar cada proyecto
        foreach ($projects as $project) {
            \Log::info("Procesando proyecto: {$project->id} - {$project->name}");

            // Obtener datos del proyecto
            $projectData = AxaptaExportHelper::getProjectExportData($project);

            // Obtener milestones
            $milestones = Milestone::where('project_id', $project->id)->get();
            \Log::info("  Milestones encontrados: " . $milestones->count());

            // Procesar cambios (deltas) en datos actuales
            foreach ($milestones as $milestone) {
                \Log::info("  Procesando milestone: {$milestone->id} - {$milestone->title}");

                // Obtener todas las tareas y sus timesheets
                $tasks = Task::where('milestone_id', $milestone->id)->get();

                // Obtener usuarios únicos en esta milestone
                $uniqueUsers = Timesheet::whereIn('task_id', $tasks->pluck('id'))
                    ->select('created_by')
                    ->distinct()
                    ->pluck('created_by');

                foreach ($uniqueUsers as $userId) {
                    $user = User::find($userId);
                    if (!$user) continue;

                    $employeeNumber = $user->number_employee ?? '0';
                    \Log::info("    Procesando usuario: {$userId} - {$employeeNumber}");

                    // Calcular estado deseado (actual)
                    $desired = AxaptaExportHelper::calculateDesiredState($project->id, $milestone->id, $userId);
                    \Log::info("      Desired - Horas: {$desired->hours_decimal}, Puntos: {$desired->puntos}, HrDecimal: {$desired->hr_decimal}");

                    // Calcular estado exportado (histórico)
                    $exported = AxaptaExportHelper::calculateExportedState($project->id, $milestone->id, $user);
                    \Log::info("      Exported - Horas: {$exported->hours_decimal}, Puntos: {$exported->puntos}, HrDecimal: {$exported->hr_decimal}");

                    // Calcular delta
                    $delta = AxaptaExportHelper::calculateDelta($desired, $exported);
                    \Log::info("      Delta - Horas: {$delta->hours_decimal}, Puntos: {$delta->puntos}, HrDecimal: {$delta->hr_decimal}");

                    // Si hay delta, generar líneas
                    if (AxaptaExportHelper::hasDelta($delta)) {
                        $this->generateExportLines(
                            $project,
                            $milestone,
                            $user,
                            $delta,
                            $projectData,
                            $fieldWidths,
                            $regId,
                            $linesToExport,
                            $ledgerRecords
                        );
                    }
                }
            }

            // Detectar registros borrados
            $deletedRecords = AxaptaExportHelper::detectDeletedRecords($project->id);
            \Log::info("  Registros borrados detectados: " . count($deletedRecords));

            foreach ($deletedRecords as $deleted) {
                \Log::info("  Generando reversión para milestone borrada: {$deleted['milestone_id']} - {$deleted['reason']}");

                // Obtener el último estado exportado
                $exported = ExportLedgerLine::where('project_id', $deleted['project_id'])
                    ->where('milestone_id', $deleted['milestone_id'])
                    ->where('employee_number', $deleted['employee_number'])
                    ->get();

                if ($exported->isNotEmpty()) {
                    $totalHours = $exported->sum('hours_decimal');
                    $totalPuntos = $exported->sum('puntos');
                    $totalHrDecimal = $exported->sum('hr_decimal');

                    // Generar delta negativo
                    $negativeDelta = (object)[
                        'hours_decimal' => -$totalHours,
                        'puntos' => -$totalPuntos,
                        'hr_decimal' => -$totalHrDecimal
                    ];

                    $user = User::where('number_employee', $deleted['employee_number'])->first();
                    if ($user) {
                        $milestone = Milestone::find($deleted['milestone_id']);
                        $this->generateExportLines(
                            $project,
                            $milestone ?? (object)['id' => $deleted['milestone_id'], 'title' => 'DELETED'],
                            $user,
                            $negativeDelta,
                            $projectData,
                            $fieldWidths,
                            $regId,
                            $linesToExport,
                            $ledgerRecords,
                            $deleted['reason']
                        );
                    }
                }
            }
        }

        // Si no hay líneas, retornar advertencia
        if (empty($linesToExport)) {
            \Log::info("No hay cambios para exportar - todos los proyectos están al día");
            return response()->json(['message' => 'Todos los proyectos están al día de la exportación'], 200);
        }

        // Agregar líneas al contenido del archivo
        foreach ($linesToExport as $line) {
            $fileContent .= $line . PHP_EOL;
        }

        \Log::info("Contenido final: " . strlen($fileContent) . " caracteres");

        // Crear batch de exportación
        $batch = ExportBatch::create([
            'file_name' => "Exp_TiemposAX_" . now()->format('Y-m-d-H-i-s') . ".fil",
            'created_by' => Auth::id(),
            'created_at' => now()
        ]);

        // Insertar registros en ledger
        foreach ($ledgerRecords as $record) {
            $record['batch_id'] = $batch->id;
            ExportLedgerLine::create($record);
        }

        \Log::info("Batch creado: {$batch->id}, Registros in ledger: " . count($ledgerRecords));

        // Generar nombre del archivo
        $fileName = $batch->file_name;

        // Retornar el contenido en base64 para descarga
        return response()->json([
            'success' => true,
            'fileName' => $fileName,
            'fileContent' => base64_encode($fileContent),
            'batchId' => $batch->id,
            'linesExported' => count($ledgerRecords)
        ]);
    }

    /**
     * Genera las líneas de exportación (considerando división de horas)
     */
    private function generateExportLines(
        $project,
        $milestone,
        $user,
        $delta,
        $projectData,
        $fieldWidths,
        &$regId,
        &$linesToExport,
        &$ledgerRecords,
        $reason = null
    ) {
        // Dividir horas si es necesario
        $splitLines = AxaptaExportHelper::splitHoursIfNeeded(
            $delta->hours_decimal,
            $delta->puntos,
            $delta->hr_decimal
        );

        $fecha = date('Ymd'); // YYYYMMDD
        $op = '210'; // siempre 210
        $lineNumberInMilestone = 0;

        foreach ($splitLines as $splitLine) {
            $lineNumberInMilestone++;
            $regId++;

            // Convertir horas decimales a formato HH:MM:SS
            $horasFormatted = AxaptaExportHelper::decimalToTimeFormat($splitLine->hours_decimal);

            // Preparar línea con ancho fijo
            $line = '';
            $line .= str_pad($regId, $fieldWidths['regId']);
            $line .= str_pad($fecha, $fieldWidths['fecha']);
            $line .= str_pad($projectData->empresa, $fieldWidths['empresa']);
            $line .= str_pad($projectData->delegacion, $fieldWidths['delegacion']);
            $line .= str_pad($user->number_employee ?? '0', $fieldWidths['empleado']);
            $line .= str_pad($projectData->masterobrasid, $fieldWidths['masterobrasid']);
            $line .= str_pad('', $fieldWidths['obra']); // obra vacío
            $line .= str_pad('', $fieldWidths['descripcion']); // descripcion vacío
            $line .= str_pad($op, $fieldWidths['op']);
            $line .= str_pad($horasFormatted, $fieldWidths['horas']);
            $line .= str_pad($projectData->ref, $fieldWidths['ref']);
            $line .= str_pad($lineNumberInMilestone, $fieldWidths['linea']);
            $line .= str_pad(number_format($splitLine->hr_decimal, 2, '.', ''), $fieldWidths['hrDecimal']);
            $line .= str_pad(number_format($splitLine->puntos, 2, '.', ''), $fieldWidths['puntos']);

            $linesToExport[] = $line;

            // Registrar en ledger
            $ledgerRecords[] = [
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'employee_number' => $user->number_employee ?? '0',
                'empresa' => $projectData->empresa,
                'delegacion' => $projectData->delegacion,
                'masterobrasid' => $projectData->masterobrasid,
                'ref' => $projectData->ref,
                'op' => $op,
                'hours_decimal' => $splitLine->hours_decimal,
                'puntos' => $splitLine->puntos,
                'hr_decimal' => $splitLine->hr_decimal,
                'created_at' => now()
            ];

            \Log::info("      Línea generada {$regId} - Horas: {$splitLine->hours_decimal}, Puntos: {$splitLine->puntos}, HrDecimal: {$splitLine->hr_decimal}");
        }
    }

    // FUNCION QUE SE LLAMA AL ESTAR DENTRO DE UN PROYECTO
    public function show($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        // ✅ Validar que el proyecto pertenece a ese workspace y que el usuario es participante
        $projectQuery = Project::where('workspace', $currentWorkspace->id)
            ->where('id', $projectID)
            ->with('activities.user');

        $project = $projectQuery->first();

        if (!$project) {
            return redirect()->back()->with('error', __("Project Not Found."));
        }

        // ✅ VERIFICAR SI EL USUARIO ESTÁ REGISTRADO EN ESTE WORKSPACE
        $userWorkspace = UserWorkspace::where('user_id', $objUser->id)
            ->where('workspace_id', $currentWorkspace->id)
            ->first();

        if (!$userWorkspace) {
            // 🔥 SI NO ESTÁ REGISTRADO, AÑADIRLO COMO MIEMBRO
            UserWorkspace::create([
                'user_id' => $objUser->id,
                'workspace_id' => $currentWorkspace->id,
                'permission' => 'Member',
                'is_active' => 1,
            ]);
            \Log::info("Usuario {$objUser->id} añadido al workspace {$currentWorkspace->id}");
        }

        // 🔥 ACTUALIZAR EL WORKSPACE ACTIVO DEL USUARIO AL WORKSPACE DEL PROYECTO
        if ($currentWorkspace && $objUser->currant_workspace !== $currentWorkspace->id) {
            $objUser->currant_workspace = $currentWorkspace->id;
            $objUser->save();
            \Log::info("Workspace del usuario actualizado a: " . $currentWorkspace->id);
        }

        if ($objUser && $currentWorkspace) {
            $project = Project::where('workspace', '=', $currentWorkspace->id)
                ->where('id', '=', $projectID)
                ->with('activities.user')
                ->first();

            if ($project) {
                $this->getProjectsPhasesAndStages($project);

                $chartData = $this->getProjectChart([
                    'workspace_id' => $currentWorkspace->id,
                    'project_id' => $projectID,
                    'duration' => 'week',
                ]);

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

                $storage = \Storage::disk('local');

                // Obtener archivos del proyecto
                $projectFolder = 'project_files' . '/' . $this->sanitizePath($project->name);
                // $projectFiles = $storage->files($projectFolder);
                $projectFiles = ProjectFile::where('project_id', '=', $projectID)->get();

                // \Log::info(["Archivos del proyecto:"=> $Files_project]);

                //  Obtener los milestones del proyecto
                $milestonesQuery = Milestone::where('project_id', '=', $projectID)
                    ->whereHas('files') // Filtra milestones que tienen archivos
                    ->with(['files']);

                // Si el proyecto es tipo 3, cargar también las phases
                if ($project->type == 3) {
                    $milestonesQuery->with(['phases']);
                }

                $milestones = $milestonesQuery
                    ->select('id', 'title')
                    ->get(); // Obtiene una colección de objetos Eloquent


                // NUEVO: Total de milestones creados en el proyecto
                $totalMilestones = Milestone::where('project_id', '=', $projectID)->count();

                //  Array para almacenar los archivos de cada milestone
                $milestoneFiles = [];

                foreach ($milestones as $milestone) {
                    $files = MilestoneFile::where('milestone_id', '=', $milestone->id)->get();
                    // $files = $storage->exists($milestoneFolder) ? $storage->files($milestoneFolder) : [];
                    //  Guardar los archivos con su milestone
                    $milestoneFiles[] = [
                        'title' => $milestone->title,
                        'files' => $files,
                        // 'objsFiles'

                    ];
                }

                //Implementation of average time

                //getting all the info about the project milestones
                $milestones = DB::table('milestones')
                    ->where('project_id', '=', $projectID)
                    ->select('id', 'title', 'end_date', 'start_date', 'task_start_date', 'finalization_date')
                    ->get();

                $milestoneDelivery = [];
                $milestoneWorkingTime = [];
                $milestoneStartUpTime = [];
                $milestoneDelayTime = [];

                foreach ($milestones as $milestone) {

                    $creation_date = Carbon::parse($milestone->start_date);
                    $estimated_date = Carbon::parse($milestone->end_date);
                    $task_start_date = Carbon::parse($milestone->task_start_date);
                    $finalization_date = $milestone->finalization_date ? Carbon::parse($milestone->finalization_date) : Carbon::now();

                    // Tiempo total de entrega desde la creación hasta la finalización
                    $deliveryTime = $creation_date->diffInDays($finalization_date);
                    $milestoneDelivery[] = $deliveryTime;

                    // Tiempo de inicio desde la creación hasta el inicio real de la tarea
                    $startUpTime = $creation_date->diffInDays($task_start_date);
                    $milestoneStartUpTime[] = $startUpTime;

                    // Tiempo de retraso (finalización - fecha estimada)
                    $delayTime = max(0, $estimated_date->diffInDays($finalization_date, false)); // Evita valores negativos
                    $milestoneDelayTime[] = $delayTime;

                    // Corrección: Tiempo de trabajo real
                    $workingTime = $deliveryTime - $startUpTime - $delayTime;
                    if ($workingTime < 0) $workingTime = 0;
                    $milestoneWorkingTime[] = $workingTime;
                }

                if (count($milestoneDelivery) != 0) {
                    //Average for statistics
                    $averageDelivery = round(array_sum($milestoneDelivery) / count($milestoneDelivery));
                    $averageWorkingTime = round(array_sum($milestoneWorkingTime) / count($milestoneWorkingTime));
                    $averageStartUpTime = round(array_sum($milestoneStartUpTime) / count($milestoneStartUpTime));
                    $averageDelayTime = round(array_sum($milestoneDelayTime) / count($milestoneDelayTime));
                } else {
                    $averageDelivery = 0;
                    $averageWorkingTime = 0;
                    $averageStartUpTime = 0;
                    $averageDelayTime = 0;
                }



                // foreach ($milestones as $milestone) {

                //     //average delivery time
                //     $startDate = Carbon::parse($milestone->start_date);
                //     //average working time
                //     $task_start_date = Carbon::parse($milestone->task_start_date);
                //     //average delay time
                //     $end_date = Carbon::parse($milestone->end_date);

                //     //if the finalizationdate is null, we should use the current date
                //     $finalizationDateDelivery = $milestone->finalization_date ? Carbon::parse($milestone->finalization_date) : Carbon::now();

                //     // if there is not a finalization date, use null (this milestone won't be used for the average)
                //     $finalizationDate = $milestone->finalization_date ? Carbon::parse($milestone->finalization_date) : null;

                //     // Calculate the difference in days
                //     $deliveryTime = $finalizationDateDelivery->diffInDays($startDate); 
                //     // Store an arrays
                //     $milestoneDelivery[] = $deliveryTime;

                //     // Calculate the difference in days
                //     if ($finalizationDate != null) {
                //         $workingTime = $finalizationDate->diffInDays($task_start_date);
                //         $milestoneWorkingTime[] = $workingTime;

                //         $startupTime = $finalizationDate->diffInDays($startDate);
                //         $milestoneStartUpTime[] = $startupTime;

                //         $delayTime = $finalizationDate->diffInDays($end_date);
                //         $milestoneDelayTime[] = $delayTime;
                //     }      

                // }
                // //Average for the statistics
                // $averageDelivery = round(array_sum($milestoneDelivery) / count($milestoneDelivery));
                // $averageWorkingTime = round(array_sum($milestoneWorkingTime) / count($milestoneWorkingTime));
                // $averageStartUpTime = round(array_sum($milestoneStartUpTime) / count($milestoneStartUpTime));
                // $averageDelayTime = round(array_sum($milestoneDelayTime) / count($milestoneDelayTime));

                //HORAS TOTALES IMPUTADAS AL PROYECTO
                $totalHours = round(
                    \DB::table('timesheets')
                        ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                        ->where('tasks.project_id', $projectID)
                        ->sum(\DB::raw('TIME_TO_SEC(timesheets.time)')) / 3600,
                    2
                );


                //USUARIOS QUE HAN CREADO UNA HOJA DE ENCARGO    
                $milestoneCreators = \App\Models\User::select('users.*')
                    ->join('milestones', 'milestones.created_by', '=', 'users.id')
                    ->where('milestones.project_id', $projectID)
                    ->selectRaw('users.*, COUNT(milestones.id) as milestones_count')
                    ->groupBy('users.id')
                    ->get();

                //USUARIOS QUE HAN IMPUTADO HORAS EN EL PROYECTO
                $usersWithHours = DB::table('timesheets')
                    ->join('tasks', 'tasks.id', '=', 'timesheets.task_id') // filtra por tareas del proyecto
                    ->join('users', 'users.id', '=', 'timesheets.created_by')
                    ->where('tasks.project_id', $projectID)
                    ->groupBy('users.id', 'users.name', 'users.email', 'users.avatar')
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.avatar',
                        DB::raw("
                            CONCAT(
                                LPAD(FLOOR(SUM(TIME_TO_SEC(timesheets.time)) / 3600), 2, '0'),      -- horas (00, 01, ..., 10, 100, etc.)
                                ':',
                                LPAD(FLOOR(MOD(SUM(TIME_TO_SEC(timesheets.time)), 3600) / 60), 2, '0') -- minutos (00-59)
                            ) as total_time
                        ")
                    )
                    ->get();

                return view('projects.show', compact(
                    'currentWorkspace',
                    'project',
                    'chartData',
                    'daysleft',
                    'projectFiles',
                    'milestoneFiles',
                    'averageDelivery',
                    'averageWorkingTime',
                    'averageStartUpTime',
                    'averageDelayTime',
                    'totalHours',
                    'milestoneCreators',
                    'usersWithHours',
                    'totalMilestones'
                ));
            } else {
                return redirect()->back()->with('error', __("Project Not Found."));
            }
        } else {
            return redirect()->back()->with('error', __("Workspace Not Found."));
        }
    }

    public function downloadFile(Request $request)
    {
        $inputs = $request->input();

        if (!isset($inputs['idProject'])) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID is required.'
            ], 400);
        }

        // Obtener el proyecto usando el ID
        $project = Project::find($inputs['idProject']);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.'
            ], 404);
        }

        $projectName = $this->sanitizePath($project->name);
        $milestoneName = isset($inputs['milestoneTitle']) ? $this->sanitizePath($inputs['milestoneTitle']) : null;

        if (!isset($inputs['fileName'])) {
            return response()->json([
                'success' => false,
                'message' => 'File name is required.'
            ], 400);
        }

        // ✅ NO sanitizar el nombre del archivo (mantener caracteres originales)
        $requestedFileName = $this->cleanFileName($inputs['fileName']);

        $filePath = '';

        if ($milestoneName !== null) {
            // Buscar primero por el nombre tal cual (sin sanitizar)
            $filePath = 'project_files/' . $projectName . '/' . $milestoneName . '/' . $requestedFileName;

            // Fallback: compatibilidad con archivos antiguos sanitizados
            if (!Storage::disk('local')->exists($filePath)) {
                $legacySanitized = $this->sanitizePath($requestedFileName);
                $filePath = 'project_files/' . $projectName . '/' . $milestoneName . '/' . $legacySanitized;
            }
        } else {
            // Buscar primero por el nombre tal cual (sin sanitizar)
            $filePath = 'project_files/' . $projectName . '/' . $requestedFileName;

            // Fallback: compatibilidad con archivos antiguos sanitizados
            if (!Storage::disk('local')->exists($filePath)) {
                $legacySanitized = $this->sanitizePath($requestedFileName);
                $filePath = 'project_files/' . $projectName . '/' . $legacySanitized;
            }
        }

        if (!Storage::disk('local')->exists($filePath)) {
            \Log::error("File verification failed. Inputs: " . json_encode($inputs) . " | Checked Path: " . $filePath);
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ], 404);
        }
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $filePath)));
        $url = asset('storage/' . $encodedPath);

        return response()->json([
            'success' => true,
            'file_url' => $url
        ]);
    }


    public function deleteFile(Request $request)
    {
        $inputs = $request->input();

        // Obtener el proyecto usando el ID
        $project = Project::findOrFail($inputs['idProject']);
        $projectName = $this->sanitizePath($project->name);
        $milestoneName = $this->sanitizePath($inputs['milestoneTitle']);

        // check if it's a milestone file or a project file
        $filePath = '';
        $delete = false;
        $milestoneId = null;

        if ($inputs['milestoneTitle'] !== null && isset($inputs['milestoneTitle'])) {
            $file = MilestoneFile::where('id', $inputs['fileID'])->first();
            $file->delete();

            $filePath = 'project_files/' . $projectName . '/' . $milestoneName . '/' . $file->file;
        } else {
            $file = ProjectFile::where('project_id', $inputs['idProject'])
                ->where('id', $inputs['fileID'])
                ->first();
            $file->delete();

            $filePath = 'project_files/' . $projectName . '/' . $file->file_path;
        }

        Storage::disk('local')->delete($filePath);

        ActivityLog::create([
            'user_id' => \Auth::user()->id,
            'user_type' => \Auth::user()->type,
            'project_id' => $inputs['idProject'],
            'log_type' => 'has delete a file',
            'remark' => json_encode(['file_name' => $file->name ?? $file->file_name]),
        ]);

        return redirect()->back()->with('success', __('File Deleted Successfully!'));
    }

    public function getProjectChart($arrParam)
    {
        $workspaceId = $arrParam['workspace_id'];
        $projectId = $arrParam['project_id'] ?? null;
        $duration = $arrParam['duration'] ?? null;

        // Initialize the array to store task counts for each stage
        $taskCounts = [];

        // Determine the date range based on the duration
        if ($duration === 'week') {
            $previousWeek = Utility::getFirstSeventhWeekDay(-1);


            foreach ($previousWeek['datePeriod'] as $dateObject) {
                $date = $dateObject->format('Y-m-d');
                $dayName = $dateObject->format('D');
                $taskCounts[$dayName] = Stage::getTaskCountsForDate($workspaceId, $projectId, $date);
            }
        }

        // Get stage names and colors
        $stages = Stage::orderBy('order')->get();
        $stageNames = $stages->pluck('name', 'id')->toArray();
        $stageColors = $stages->pluck('color')->toArray();

        // Prepare the result array
        $arrTask = [
            'label' => array_map(fn($date) => ucfirst(Carbon::parse($date)->isoFormat('ddd')), array_keys($taskCounts)),
            'stages' => $stageNames,
            'color' => $stageColors,
        ];

        foreach ($taskCounts as $date => $counts) {
            foreach ($stages as $stage) {
                $arrTask[$stage->id][] = $counts[$stage->id] ?? 0;
            }
        }

        return $arrTask;
    }


    public function edit($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)->first();

        return view('projects.edit', compact('currentWorkspace', 'project'));
    }

    public function create($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_type = ProjectType::select('id', 'name')->get();
        $project_delegation = Delegation::all();
        $projects = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace->id)->get();
        \Log::debug(["Delegaciones:" => $project_delegation]);

        return view('projects.create', compact('currentWorkspace', 'project_type', 'projects', 'project_delegation'));
    }


    public function popup($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)->first();

        return view('projects.invite', compact('currentWorkspace', 'project'));
    }

    public function userDelete($slug, $project_id, $user_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where(
                'projects.workspace',
                '=',
                $currentWorkspace->id
            )->where('projects.id', '=', $project_id)->first();
        if ($currentWorkspace->permission == 'Owner') {
            if (count($project->user_tasks($user_id)) == 0) {
                UserProject::where('user_id', '=', $user_id)->where('project_id', '=', $project->id)->delete();

                return redirect()->back()->with('success', __('User Deleted Successfully!'));
            } else {
                return redirect()->back()->with('warning', __('Please Remove User From Tasks!'));
            }
        } else {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't Delete Project!"));
        }
    }

    public function sharePopup($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where(
                'projects.workspace',
                '=',
                $currentWorkspace->id
            )->where('projects.id', '=', $projectID)->first();

        return view('projects.share', compact('currentWorkspace', 'project'));
    }

    public function clientDelete($slug, $project_id, $client_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id)->first();
        if ($currentWorkspace->permission == 'Owner') {
            ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $project->id)->delete();

            return redirect()->back()->with('success', __('Client Deleted Successfully!'));
        } else {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't Delete Project!"));
        }
    }

    public function share($slug, $projectID, Request $request)
    {

        $authuser = Auth::user();
        $authusername  = User::where('id', '=', $authuser->id)->first();
        $project = Project::find($projectID);
        $setting = Utility::getAdminPaymentSettings();

        foreach ($request->clients as $client_id) {
            $client = Client::find($client_id);
            $user = Client::find($client_id);
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $workspaceId = $currentWorkspace->id;

            if (ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $projectID)->count() == 0) {
                ClientProject::create(
                    [
                        'client_id' => $client_id,
                        'project_id' => $projectID,
                        'workspace_id' => $currentWorkspace->id,
                        'permission' => json_encode(Utility::getAllPermission()),
                    ]
                );
            }

            try {
                $uArr = [
                    'user_name' => $client->name,
                    'app_name'  => $setting['app_name'],
                    'owner_name' => $authusername->name,
                    'project_name' => $project->name,
                    'project_status' => $project->status,
                    'app_url' => env('APP_URL'),
                ];


                // Send Email
                $resp = Utility::sendclientEmailTemplate('Project Assigned', $user->id, $uArr);
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            ActivityLog::create(
                [
                    'user_id' => \Auth::user()->id,
                    'user_type' => get_class(\Auth::user()),
                    'project_id' => $project->id,
                    'log_type' => 'Share with Client',
                    'remark' => json_encode(['client_id' => $client->id]),
                ]
            );
        }

        return redirect()->back()->with('success', __('Project Share Successfully!')
            . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function update(Request $request, $slug, $projectID)
    {
        $request->validate(
            [
                'name' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)->first();
        $project->update($request->all());

        return redirect()->back()->with('success', __('Project Updated Successfully!'));
    }

    public function destroy($slug, $projectID)
    {
        $objUser = Auth::user();
        $project = Project::find($projectID);

        if (!$project) {
            return redirect()->route('projects.index', $slug)->with('error', __('Project not found!'));
        }

        if ($objUser->id !== $project->created_by) {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't delete this project!"));
        }

        try {
            DB::transaction(function () use ($projectID, $project) {

                $projectFolder = $this->sanitizePath($project->name);

                //** Funciones que eliminan los ficheros del sistema teniendo en cuenta que no esta conectado al sharePoint */

                // Eliminar archivos de hitos (Milestones)
                $milestones = Milestone::where('project_id', $projectID)->get();
                foreach ($milestones as $milestone) {
                    foreach ($milestone->files as $file) {
                        $milestoneFolder = $this->sanitizePath($milestone->title);
                        $dir = 'project_files/' . $projectFolder . '/' . $milestoneFolder;

                        if (Storage::exists($dir)) {
                            if (!Storage::deleteDirectory($dir)) {
                                return redirect()->back()->with('error', __('Error deleting project files'));
                            }
                        }
                    }
                    $milestone->files()->delete();
                }

                $projectFiles = ProjectFile::where('project_id', $projectID)->get();
                foreach ($projectFiles as $file) {
                    // $milestoneFolder = str_replace(' ', '_', $milestone->title); // Removed incorrect line if using projectFolder only next

                    $dir = 'project_files/' . $projectFolder;
                    if (Storage::exists($dir)) {
                        if (!Storage::deleteDirectory($dir)) {
                            return redirect()->back()->with('error', __('Error deleting project files'));
                        }
                    }
                }

                ProjectFile::where('project_id', $projectID)->delete();

                // Eliminar relaciones y registros asociados
                UserProject::where('project_id', $projectID)->delete();
                MasterObra::where('project_id', $projectID)->update(['project_id' => 0]);
                Milestone::where('project_id', $projectID)->delete();
                Task::where('project_id', $projectID)->delete();
                Timesheet::where('project_id', $projectID)->delete();

                // Finalmente, eliminar el proyecto
                $project->delete();
            });

            return redirect()->route('projects.index', $slug)->with('success', __('Project Deleted Successfully!'));
        } catch (\Exception $e) {
            return redirect()->route('projects.index', $slug)->with('error', __('Error deleting project: ') . $e->getMessage());
        }


        // return redirect()->route('projects.index', $slug)->with('success', __('Project Deleted Successfully!'));
    }


    public function leave($slug, $projectID)
    {
        $objUser = Auth::user();
        $userProject = Project::find($projectID);
        UserProject::where('project_id', '=', $userProject->id)->where('user_id', '=', $objUser->id)->delete();

        return redirect()->route('projects.index', $slug)->with('success', __('Project Leave Successfully!'));
    }

    public function collaborator($user, $project)
    {
        // Verifica si el usuario ya es un colaborador del proyecto
        $existingCollaborator = UserWorkspace::where('user_id', $user->id)
            ->where('workspace_id', $project->workspace)
            ->exists();

        // Si no es un colaborador, agrégalo como uno
        if (!$existingCollaborator) {
            $permissions = json_encode(Utility::getAllPermission());

            UserWorkspace::create([
                'user_id' => $user->id,
                'workspace_id' => $project->workspace,
                'permission' => $permissions,
            ]);
        }
    }

    public function getAllParticipatingProjects()
    {
        $user = Auth::user();

        /*
     |------------------------------------------------------------
     | Proyectos donde el usuario participa (TODOS los workspaces)
     |------------------------------------------------------------
     */
        $projects = Project::whereUserIsParticipant($user->id)
            ->with([
                'typeRel:id,name',
                // 👇 cargar el workspace completo sin restricción de columnas
                'workspaceData',
                'milestones'
            ])
            ->orderByDesc('id')
            ->get();

        // 🔍 Asegurar que cada proyecto tiene su workspace cargado correctamente
        $projects = $projects->map(function ($project) {
            if (!$project->workspaceData) {
                // Si por alguna razón el workspace no se cargó, intentar cargarlo manualmente
                $project->workspaceData = Workspace::find($project->workspace);
            }
            return $project;
        });

        /*
     |------------------------------------------------------------
     | Tipos de proyecto
     |------------------------------------------------------------
     */
        $project_type = ProjectType::select('id', 'name')->get();

        /*
     |------------------------------------------------------------
     | Workspace actual (solo para el layout / sidebar)
     |------------------------------------------------------------
     */
        $currentWorkspace = Workspace::find($user->currant_workspace);

        return view(
            'projects.my_projects',
            compact('currentWorkspace', 'projects', 'project_type')
        );
    }

    public function mySummary(Request $request)
    {
        $user = Auth::user();
        $currentWorkspace = Workspace::find($user->currant_workspace);
        $dateRange = $this->resolveMySummaryDateRange($request);
        $myDayDateRange = $this->resolveMyDayDateRange($request);

        $projectSummaries = Timesheet::query()
            ->join('projects', 'timesheets.project_id', '=', 'projects.id')
            ->leftJoin('workspaces', 'projects.workspace', '=', 'workspaces.id')
            ->leftJoin('project_types', 'projects.type', '=', 'project_types.id')
            ->where('timesheets.created_by', $user->id)
            ->whereBetween('timesheets.date', [
                $dateRange['startDate']->toDateString(),
                $dateRange['endDate']->toDateString(),
            ])
            ->select(
                'projects.id',
                'projects.name',
                'projects.type',
                'projects.workspace as workspace_id',
                'workspaces.name as workspace_name',
                'workspaces.slug as workspace_slug',
                'project_types.name as project_type_name',
                DB::raw('SUM(TIME_TO_SEC(timesheets.time)) as total_seconds')
            )
            ->groupBy(
                'projects.id',
                'projects.name',
                'projects.type',
                'projects.workspace',
                'workspaces.name',
                'workspaces.slug',
                'project_types.name'
            )
            ->orderByDesc('total_seconds')
            ->get()
            ->map(function ($summary) {
                $totalSeconds = (int) $summary->total_seconds;
                $summary->formatted_total_time = $this->formatSecondsAsHoursMinutes($totalSeconds);
                $summary->decimal_total_time = round($totalSeconds / 3600, 2);
                $summary->project_url = $summary->workspace_slug
                    ? route('projects.show', [$summary->workspace_slug, $summary->id])
                    : null;

                return $summary;
            });

        $myDayStatus = (int) $request->input('my_day_status', 2);

        $myDayMilestones = Milestone::query()
            ->with([
                'project:id,name,workspace',
                'project.workspaceData:id,name,slug',
                'tasks' => function ($query) use ($user) {
                    $query->select('id', 'milestone_id', 'project_id', 'type_id', 'assign_to', 'estimated_date')
                        ->with([
                            'type:id,name',
                            'customTask:id,id_task,name',
                        ])
                        ->where(function ($taskQuery) use ($user) {
                            $taskQuery->where('assign_to', (string) $user->id)
                                ->orWhereRaw('FIND_IN_SET(?, assign_to)', [(string) $user->id]);
                        })
                        ->orderByRaw('estimated_date IS NULL')
                        ->orderBy('estimated_date');
                },
            ])
            ->where('status', $myDayStatus)
            ->whereHas('project');

        if ($myDayStatus === 1) {
            $myDayMilestones->where('milestone_assigned_to_user', $user->id);
        } else {
            $myDayMilestones->where(function ($query) use ($user) {
                $query->where('milestone_assigned_to_user', $user->id)
                    ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                        $taskQuery->where('assign_to', (string) $user->id)
                            ->orWhereRaw('FIND_IN_SET(?, assign_to)', [(string) $user->id]);
                    });
            });
        }

        if ($myDayDateRange['preset'] !== 'all') {
            $myDayMilestones
                ->whereNotNull('planned_end_date')
                ->where('planned_end_date', '!=', '0000-00-00')
                ->whereDate('planned_end_date', '>=', $myDayDateRange['startDate']->toDateString())
                ->whereDate('planned_end_date', '<=', $myDayDateRange['endDate']->toDateString());
        }

        $myDayMilestones = $myDayMilestones
            ->orderByRaw('planned_end_date IS NULL')
            ->orderBy('planned_end_date')
            ->get();

        $requestersById = User::query()
            ->whereIn('id', $myDayMilestones->pluck('assign_to')->filter()->unique())
            ->get(['id', 'name'])
            ->keyBy('id');

        $myDayMilestones->transform(function ($milestone) use ($requestersById) {
            $workspace = optional($milestone->project)->workspaceData;

            $milestone->requested_by_name = optional($requestersById->get($milestone->assign_to))->name;
            $milestone->workspace_name = optional($workspace)->name;
            $milestone->workspace_slug = optional($workspace)->slug;
            $milestone->project_name = optional($milestone->project)->name;
            $milestone->board_url = $milestone->workspace_slug && $milestone->project_id
                ? route('projects.milestone.board', [$milestone->workspace_slug, $milestone->project_id])
                : null;

            return $milestone;
        });

        $userScore = round((float) PuntuacionTarea::query()
            ->whereIn('id_tarea', function ($query) use ($user, $dateRange) {
                $query->select('timesheets.task_id')
                    ->from('timesheets')
                    ->where('timesheets.created_by', $user->id)
                    ->whereNotNull('timesheets.task_id')
                    ->whereBetween('timesheets.date', [
                        $dateRange['startDate']->toDateString(),
                        $dateRange['endDate']->toDateString(),
                    ])
                    ->distinct();
            })
            ->sum('cantidad_puntaje'), 2);

        $totalImputedTime = $this->formatSecondsAsHoursMinutes((int) $projectSummaries->sum('total_seconds'));
        $selectedFilters = [
            'range_type' => $dateRange['rangeType'],
            'preset' => $dateRange['preset'],
            'start_date' => $dateRange['startDateInput'],
            'end_date' => $dateRange['endDateInput'],
        ];
        $myDaySelectedFilters = [
            'status' => $myDayStatus,
            'range_type' => $myDayDateRange['rangeType'],
            'preset' => $myDayDateRange['preset'],
            'start_date' => $myDayDateRange['startDateInput'],
            'end_date' => $myDayDateRange['endDateInput'],
        ];
        $chartData = $this->buildMySummaryChartData(
            $projectSummaries,
            $totalImputedTime,
            $dateRange['appliedLabel']
        );

        if ($request->ajax()) {
            return response()->json([
                'scoreHtml' => view('projects.partials.my_summary_score', compact(
                    'userScore',
                    'selectedFilters',
                    'dateRange'
                ))->render(),
                'myDayHtml' => view('projects.partials.my_summary_day', compact(
                    'currentWorkspace',
                    'myDayMilestones',
                    'myDaySelectedFilters'
                ))->render(),
                'contentHtml' => view('projects.partials.my_summary_content', compact(
                    'currentWorkspace',
                    'projectSummaries',
                    'userScore',
                    'totalImputedTime',
                    'selectedFilters',
                    'dateRange'
                ))->render(),
                'chartData' => $chartData,
            ]);
        }

        return view(
            'projects.my_summary',
            compact(
                'currentWorkspace',
                'myDayMilestones',
                'myDaySelectedFilters',
                'projectSummaries',
                'userScore',
                'totalImputedTime',
                'chartData',
                'selectedFilters',
                'dateRange'
            )
        );
    }

    public function milestoneBoard($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $stages = Stage::orderBy('order')->get();

        $statusClass = $stages->map(function ($stage) {
            return 'milestone-list-' . str_replace(' ', '_', $stage->id);
        })->toArray();

        // ===============================
        // 📌 VISTA GLOBAL (-1)
        // ===============================
        if ($id == -1) {

            $objUser = Auth::user();

            // 🔹 Milestones visibles para el usuario
            $allmilestones = Milestone::whereHas('project', function ($q) use ($currentWorkspace) {
                $q->where('workspace', $currentWorkspace->id);
            })
                ->where(function ($q) use ($objUser) {
                    $q->where('assign_to', $objUser->id)
                        ->orWhere('milestone_assigned_to_user', $objUser->id)
                        ->orWhere('created_by', $objUser->id)
                        ->orWhere('milestone_assigned_to_user', '')
                        ->orWhereHas('tasks', function ($q2) use ($objUser) {
                            $q2->where('assign_to', $objUser->id);
                        });
                })
                ->with([
                    'tasks:id,milestone_id,assign_to,type_id',
                    'tasks.type:id,name',
                    'tasks.customTask:id,id_task,name',
                    'project:id,workspace,name,type'
                ])

                ->orderBy('created_at', 'desc')
                ->get();

            // dd($allmilestones->pluck('title'));

            // 🔹 TODOS los milestones del workspace (para "ver todos")
            $workspaceProjectsIds = Project::where('workspace', $currentWorkspace->id)
                ->pluck('id')
                ->toArray();

            $allUsersMilestones = Milestone::whereIn('project_id', $workspaceProjectsIds)
                ->with([
                    'tasks:id,milestone_id,assign_to',
                    'project:id,workspace,name,type'
                ])
                ->get();

            // 🔹 Agrupación por estado
            $milestones = $this->groupMilestonesByStatus($allmilestones, $objUser, $stages);
            $milestonesUsers = $this->groupMilestonesByStatus($allUsersMilestones, null, $stages);

            $project_id = -1;

            return view(
                'projects.milestoneboard',
                compact(
                    'currentWorkspace',
                    'milestones',
                    'milestonesUsers',
                    'stages',
                    'statusClass',
                    'project_id'
                )
            );
        }

        // ===============================
        // 📌 VISTA POR PROYECTO
        // ===============================
        $project = Project::find($id);

        if (!$project) {
            abort(404);
        }

        $allmilestones = Milestone::where('project_id', $project->id)
            ->with([
                'tasks:id,milestone_id,assign_to,type_id',
                'tasks.type:id,name',
                'tasks.customTask:id,id_task,name',
            ])
            ->get();


        $milestones = $this->groupMilestonesByStatus($allmilestones, null, $stages);

        $project_id = $project->id;
        $project_name = $project->name;

        return view(
            'projects.milestoneboard',
            compact(
                'currentWorkspace',
                'milestones',
                'stages',
                'statusClass',
                'project_id',
                'project_name'
            )
        );
    }

    /**
     * Vista de "Mis Encargos" - Muestra todos los milestones del usuario actual
     * de TODOS sus workspaces (sin filtrar por workspace específico)
     */
    public function myMilestoneBoard()
    {
        $objUser = Auth::user();

        // Obtener el workspace actual del usuario
        $currentWorkspace = Utility::getWorkspaceBySlug($objUser->currentWorkspace->slug) ?? $objUser->currentWorkspace;

        $stages = Stage::orderBy('order')->get();

        $statusClass = $stages->map(function ($stage) {
            return 'milestone-list-' . str_replace(' ', '_', $stage->id);
        })->toArray();

        // 🔹 Milestones del usuario actual de TODOS los workspaces
        $allmilestones = Milestone::where(function ($q) use ($objUser) {
            $q->where('assign_to', $objUser->id)
                ->orWhere('milestone_assigned_to_user', $objUser->id)
                ->orWhere('created_by', $objUser->id)
                ->orWhereHas('tasks', function ($q2) use ($objUser) {
                    $q2->where('assign_to', $objUser->id);
                });
        })
            ->with([
                'tasks:id,milestone_id,assign_to',
                'project:id,workspace,name,type',
                'project.workspaceData:id,slug,name',
            ])

            ->orderBy('created_at', 'desc')
            ->get();

        // 🔹 Agrupación por estado
        $milestones = $this->groupMilestonesByStatus($allmilestones, $objUser, $stages);

        $project_id = -1;

        return view(
            'projects.my_milestone_board',
            compact(
                'currentWorkspace',
                'milestones',
                'stages',
                'statusClass',
                'project_id'
            )
        );
    }




    /**
     * Obtiene los datos de un milestone, incluyendo sus tareas.
     * Si el usuario autenticado es el creador (assign_to) o el asignado (milestone_assigned_to_user)
     * del milestone, se muestran todas las tareas; en otro caso, se filtran las tareas donde assign_to es el usuario.
     */
    private function getMilestoneData($milestone, $project, $objUser)
    {
        if (!$project) {
            return null;
        }
        $projectType = ProjectType::where('id', $project->type)->value('name');

        if ($objUser) {
            $milestoneIds = Task::where('assign_to', $objUser->id)
                ->pluck('milestone_id')
                ->unique()
                ->toArray();
            // Si el usuario es el creador o está asignado al milestone, mostramos TODAS las tareas
            if (
                $milestone->assign_to == $objUser->id || $milestone->milestone_assigned_to_user == $objUser->id || $milestone->created_by == $objUser->id ||
                in_array($milestone->id, $milestoneIds) || $milestone->milestone_assigned_to_user == ''
            ) {
                $tasksOfmilestone = Task::where('milestone_id', $milestone->id)
                    ->where('project_id', $project->id)
                    ->get();
            } else {
                // En otros casos, se muestran solo las tareas asignadas al usuario
                $tasksOfmilestone = Task::where('milestone_id', $milestone->id)
                    ->where('project_id', $project->id)
                    ->where('assign_to', $objUser->id)
                    ->get();
            }
        } else {
            $tasksOfmilestone = Task::where('milestone_id', $milestone->id)
                ->where('project_id', $project->id)
                ->get();
        }

        $taskData = $tasksOfmilestone->map(function ($task) {
            if (!$task) {
                return null;
            }

            $taskType = TaskType::find($task->type_id);
            if (!$taskType) {
                return null;
            }

            $isCustom = strtolower(trim($taskType->name)) === 'custom';

            // Si es custom, leer el nombre de custom_tasks
            $customName = null;
            if ($isCustom) {
                $customName = \App\Models\CustomTasks::where('id_task', $task->id)->value('name');
            }

            return [
                'id'             => $task->id,
                'name'           => $taskType->name, // se mantiene por compatibilidad
                'display_name'   => $isCustom ? ($customName ?: 'Custom') : $taskType->name, // ✅ NUEVO
                'estimated_date' => $task->estimated_date,
                'technician'     => User::find($task->assign_to),
                'logged_hours'   => $task->getTotalLoggedHours(),
            ];
        })->filter()->values()->toArray();

        // Si es proyecto tipo 3 o 5, cargar las phases y el stage actual
        $phases = [];
        $stage = null;
        if (in_array((int) $project->type, [3, 5], true)) {
            $phases = MilestonePhases::where('id_milestone', $milestone->id)
                ->pluck('phases')
                ->toArray();

            $currentStageRecord = MilestoneStages::where('id_milestone', $milestone->id)
                ->orderByDesc('id')
                ->first(['stages', 'milestone_stage_project_id']);

            if ($currentStageRecord) {
                if (!empty($currentStageRecord->milestone_stage_project_id)) {
                    $stage = MilestoneStageProject::where('project_id', $project->id)
                        ->where('id', $currentStageRecord->milestone_stage_project_id)
                        ->value('name');
                }

                if (empty($stage) && !empty($currentStageRecord->stages)) {
                    $stage = MilestoneStageProject::where('project_id', $project->id)
                        ->where('name', trim((string) $currentStageRecord->stages))
                        ->value('name')
                        ?? trim((string) $currentStageRecord->stages);
                }
            }
        }

        return [
            'id'            => $milestone->id,
            'assined_to_user' => $milestone->milestone_assigned_to_user,
            'priority'      => $milestone->priority,
            'created_by' => $milestone->created_by,
            'title'         => $milestone->title,
            'start_date'    => $milestone->start_date,
            'end_date'      => $milestone->end_date,
            'planned_end_date' => $milestone->planned_end_date,
            'finalization_date' => $milestone->finalization_date,
            'assign_to'     => $milestone->assign_to,
            'daysleft'      => round((strtotime($milestone->end_date) - strtotime(date('Y-m-d'))) / 86400),
            'project_id'    => $project->id,
            'project_name'  => $project->name,
            'project_type'  => $projectType,
            'project_type_id' => $project->type,
            'project_ref'   => $project->ref_mo ? '- ' . $project->ref_mo : '',
            'workspace_name' => optional(Workspace::find($project->workspace))->name ?? 'N/A',
            'workspace_id'  => $project->workspace,
            'tasks'         => $taskData,
            'sales'         => User::find($milestone->assign_to),
            'asiggned_user_data'         => User::find($milestone->milestone_assigned_to_user),
            'is_waiting' => $milestone->is_waiting,
            'phases' => $phases,
            'stage' => $stage,
        ];
        //\Log::info($milestone);
    }

    /**
     * Agrupa los milestones por estado.
     */
    private function groupMilestonesByStatus($allmilestones, $objUser = null, $stages)
    {
        $milestones = [];

        foreach ($stages as $status) {

            $filteredMilestones = $allmilestones->filter(function ($milestone) use ($status) {
                return (int)$milestone->status === (int)$status->id;
            });

            $milestones[$status->id] = $filteredMilestones->map(function ($milestone) use ($objUser) {

                // ✅ Usa el proyecto eager-loaded si existe, si no fallback a find()
                $project = $milestone->relationLoaded('project') ? $milestone->project : null;
                if (!$project) {
                    $project = Project::find($milestone->project_id);
                }

                $data = $this->getMilestoneData($milestone, $project, $objUser);

                // ✅ Añadir workspace_slug/name sin romper nada
                $workspace = null;

                // si viene eager-loaded: project.workspaceData
                if ($project && method_exists($project, 'workspaceData')) {
                    // ojo: workspaceData() en tu Project es hasOne, así que se accede como propiedad
                    $workspace = $project->relationLoaded('workspaceData') ? $project->workspaceData : $project->workspaceData()->first();
                }

                $data['workspace_slug'] = $workspace->slug ?? null;
                $data['workspace_name'] = $workspace->name ?? null;

                return $data;
            })->toArray();

            if (empty($milestones[$status->id])) {
                $milestones[$status->id] = [];
            }
        }

        return empty(array_filter($milestones, fn($ms) => !empty($ms))) ? null : $milestones;
    }



    public function taskBoard($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();
        if ($objUser && $currentWorkspace) {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
            if ($project) {
                $stages = $statusClass = [];
                $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                foreach ($stages as $status) {
                    $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $task = Task::where('project_id', '=', $projectID);
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
                return view('projects.taskboard', compact('currentWorkspace', 'project', 'stages', 'statusClass'));
            } else {
                return redirect()->back()->with('error', __('Task Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Workspace Not Found.'));
        }
    }

    public function waitMilestone($slug, $milestoneID, Request $request)
    {
        $milestone = Milestone::find($milestoneID);

        if (!$milestone) {
            return redirect()->back()->with('error', __('Milestone not found.'));
        }

        // Si hay un comentario, agregarlo al principio del summary
        $pauseComment = $request->input('pause_comment');
        if ($pauseComment) {
            $user = Auth::user()->name;
            $newNote = "[Paused by $user]: \n$pauseComment\n\n";
            $milestone->summary = $newNote . ($milestone->summary ?? '');
        }

        $milestone->is_waiting = true;
        $milestone->save();

        // ✅ Recalcular estado del proyecto
        $milestone->project?->updateProjectStatus();

        return redirect()->back()->with('success', __('Milestone paused successfully.'));
    }


    public function resumeMilestone($slug, $milestoneID, Request $request)
    {
        $milestone = Milestone::find($milestoneID);

        if (!$milestone) {
            return redirect()->back()->with('error', __('Milestone not found.'));
        }

        $milestone->is_waiting = false;
        $milestone->save();

        // ✅ Recalcular estado del proyecto
        $milestone->project?->updateProjectStatus();

        return redirect()->back()->with('success', __('Milestone resumed successfully.'));
    }


    public function clearFinalizationDate($slug, $milestoneID)
    {
        $milestone = Milestone::find($milestoneID);

        if (!$milestone) {
            return response()->json(['success' => false], 404);
        }

        $milestone->finalization_date = null;
        $milestone->save();

        // ✅ Recalcular estado del proyecto
        if ($milestone->project) {
            $milestone->project->updateProjectStatus();
        }

        return response()->json(['success' => true]);
    }



    public function getMilestones($projectId)
    {
        $milestones = Milestone::where('project_id', $projectId)->get();
        return $milestones;
    }

    public function checkHasDrawingTask($slug, $milestoneId)
    {
        // Verifica si el milestone tiene alguna tarea con type_id = 1
        $hasDrawingTask = \App\Models\Task::where('milestone_id', $milestoneId)
            ->where('type_id', 1)
            ->exists();

        return response()->json(['has_drawing_task' => $hasDrawingTask]);
    }


    public function milestoneReview($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::findOrFail($id);
        $systemOptions = System::orderBy('name_system', 'asc')->get();
        // Retorna una vista parcial (para el popup)
        return view('projects.milestoneReview', compact('milestone', 'currentWorkspace', 'systemOptions'));
    }

    private function uploadMilestoneReviewSingleFile(UploadedFile $file, $slug, $milestoneId)
    {
        // ✅ Cargar milestone y proyecto
        $milestone = \App\Models\Milestone::with('project')->findOrFail($milestoneId);
        $project = $milestone->project;

        $originalName = $this->cleanFileName($file->getClientOriginalName());

        $extension = $file->getClientOriginalExtension();
        $fileSize = round($file->getSize() / 1024, 2) . ' KB';
        $uniqueName = $milestone->id . '_' . time() . '_' . uniqid() . '_rf_' . $originalName;

        $projectFolder = $this->sanitizePath($project->name);
        $milestoneFolder = $this->sanitizePath($milestone->title);

        $dir = storage_path('project_files/' . $projectFolder . '/' . $milestoneFolder);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException("No se pudo crear la carpeta: {$dir}");
            }
        }

        // ✅ mover archivo
        $file->move($dir, $uniqueName);

        // ✅ Guardar registro BD
        return \App\Models\MilestoneFile::create([
            'milestone_id' => $milestone->id,
            'file'         => "project_files/{$projectFolder}/{$milestoneFolder}/{$uniqueName}",
            'name'         => $originalName,
            'extension'    => $extension,
            'file_size'    => $fileSize,
            'created_by'   => \Auth::id(),
            'user_type'    => get_class(\Auth::user()),
        ]);
    }

    private function uploadMilestoneReviewFiles(Request $request, $slug, $milestoneId)
    {
        $files = $request->file('review_files', []);
        foreach ($files as $file) {
            $this->uploadMilestoneReviewSingleFile($file, $slug, $milestoneId);
        }
    }

    private function calculatePoints($estimated_time, $imputed_time, $extra_points)
    {
        \Log::debug("Cálculo de puntos: estimated_time={$estimated_time}, imputed_time={$imputed_time}, extra_points={$extra_points}");

        // ✅ Guardas anti-0 / anti-negative
        $estimated_time = (float) $estimated_time;
        $imputed_time   = (float) $imputed_time;
        $extra_points   = (float) $extra_points;

        if ($estimated_time <= 0) {
            $estimated_time = 1;
        }

        $real_time = $estimated_time;

        if ($imputed_time < floor($estimated_time / 2)) {
            $real_time = $estimated_time / 2;
        }

        if ($real_time <= 0) {
            $real_time = 1;
        }

        \Log::debug("real_time ajustado={$real_time}");

        $pointsHour = 0.35 * $estimated_time / $real_time + 0.5;
        \Log::debug("pointsHour={$pointsHour}");

        $totalPoints = $pointsHour + $imputed_time + $extra_points;

        return [
            'totalPoints' => $totalPoints,
            'pointsHour'  => $pointsHour,
        ];
    }


    public function milestoneReviewSubmit(Request $request, $slug, $id)
    {
        try {
            $milestoneId = $request->input('milestone_id', $id);

            if (!$milestoneId || !is_numeric($milestoneId)) {
                return redirect()->back()->with('error', 'Falta el ID del milestone.');
            }

            $milestone = Milestone::with('tasks')->find($milestoneId);
            if (!$milestone) {
                return redirect()->back()->with('error', 'Milestone no encontrado.');
            }

            // ✅ Ahora vienen como array: review_files[]
            $hasPdfs = $request->hasFile('review_files');

            $systems = $request->input('systems', []);
            if (empty($systems)) {
                return redirect()->back()->with('error', 'Debes seleccionar al menos un sistema.');
            }

            // ---------------------------------------
            // ✅ Validación condicional
            // ---------------------------------------
            $rules = [
                'systems'   => ['required', 'array', 'min:1'],
                'systems.*' => ['string'],
            ];

            if ($hasPdfs) {
                $rules = array_merge($rules, [
                    'review_files'    => ['required', 'array', 'min:1', 'max:5'],
                    'review_files.*'  => ['file', 'mimes:pdf', 'max:51200'], // 50MB por archivo
                    'num_plans'       => ['required', 'integer', 'min:1'],
                    'document_format' => ['required', 'in:dwg,pdf,papel'],
                    'detail_level'    => ['required', 'in:oferta,montaje,edificacion'],
                ]);
            }

            $validator = Validator::make($request->all(), $rules, [
                'review_files.max'    => 'Puedes subir como máximo 5 PDFs.',
                'review_files.*.mimes' => 'Todos los archivos deben ser PDF.',
                'review_files.*.max'  => 'Cada PDF no puede superar 50MB.',
                'systems.required'    => 'Debes seleccionar al menos un sistema.',
                'systems.min'         => 'Debes seleccionar al menos un sistema.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // ---------------------------------------
            // ✅ Subir PDFs (solo si existen)
            // ---------------------------------------
            if ($hasPdfs) {
                $this->uploadMilestoneReviewFiles($request, $slug, $milestoneId);
            }

            // ---------------------------------------
            // ✅ Inputs para cálculo:
            // Normal: vienen del request
            // Omit: defaults neutros para evitar 0
            // ---------------------------------------
            if ($hasPdfs) {
                $numPlans       = (int) $request->input('num_plans');
                $documentFormat = $request->input('document_format');
                $detailLevel    = $request->input('detail_level');

                $formatPoints = Puntuacion::where('nombre', $documentFormat)->value('valor') ?? 0;
                $detailPoints = Puntuacion::where('nombre', $detailLevel)->value('valor') ?? 0;
            } else {
                // ✅ Defaults en modo omit
                $numPlans     = 1;
                $formatPoints = 0;
                $detailPoints = 1;
            }

            // ---------------------------------------
            // Calcular systemPoints (igual que antes)
            // ---------------------------------------
            $systemPoints = Puntuacion::whereIn('nombre', $systems)
                ->get()
                ->pluck('valor')
                ->reduce(function ($carry, $item) {
                    return $carry * $item;
                }, 1);

            if ($systemPoints > 2) $systemPoints = 2;

            // ---------------------------------------
            // Calcular tiempo estimado
            // ---------------------------------------
            $estimated_time = ($numPlans * $systemPoints * $detailPoints) + $formatPoints;

            // ✅ guarda anti-0 para evitar división por 0 en calculatePoints
            if ($estimated_time <= 0) {
                $estimated_time = 1;
            }

            // ---------------------------------------
            // Obtener horas imputadas desde Tarea Drawing
            // ---------------------------------------
            $drawingTask = $milestone->tasks()
                ->whereHas('type', function ($q) {
                    $q->where('name', 'Drawing');
                })
                ->first();

            if (!$drawingTask) {
                \Log::warning("No se encontró tarea tipo Drawing en el milestone {$milestone->id}");
                $real_imputed_time = 0;
            } else {
                $real_imputed_time = $drawingTask->timesheets()
                    ->selectRaw('SUM(TIME_TO_SEC(time)) as total_seconds')
                    ->value('total_seconds');

                $real_imputed_time = ($real_imputed_time ?? 0) / 3600;
            }

            // ---------------------------------------
            // Puntos extras por tareas
            // ---------------------------------------
            $extraTaskPoints = TaskType::where('project_type', 1)
                ->whereIn('id', $milestone->tasks()->pluck('type_id'))
                ->sum('puntuacion');

            // ---------------------------------------
            // Calcular puntos
            // ---------------------------------------
            $allPoints = $this->calculatePoints($estimated_time, $real_imputed_time, $extraTaskPoints);

            if ($allPoints['totalPoints'] === null) {
                $allPoints['totalPoints'] = 0;
            }

            foreach ($milestone->tasks as $task) {
                PuntuacionTarea::updateOrCreate(
                    ['id_tarea' => $task->id],
                    [
                        'cantidad_puntaje' => $allPoints['totalPoints'],
                        'user_id'          => $task->assign_to,
                        'puntos_hora'      => $allPoints['pointsHour'],
                    ]
                );
            }

            ActivityLog::create([
                'user_id'    => \Auth::user()->id,
                'user_type'  => get_class(\Auth::user()),
                'project_id' => $milestone->project_id,
                'log_type'   => $hasPdfs ? 'has uploaded review files' : 'has omitted the review files',
                'remark'     => json_encode([
                    'milestoneTitle' => $milestone->title ?? 'Unnamed milestone',
                ]),
            ]);

            return redirect()->back()->with('success', 'Revisión guardada correctamente.');
        } catch (\Throwable $e) {
            \Log::error("Error en milestoneReviewSubmit", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }


    public function deletePuntuaciones($slug, $milestoneId)
    {
        try {
            $milestone = Milestone::with('tasks')->findOrFail($milestoneId);

            // Conseguir los IDs de las tareas del milestone
            $taskIds = $milestone->tasks->pluck('id');

            // Eliminar todas las puntuaciones asociadas
            PuntuacionTarea::whereIn('id_tarea', $taskIds)->delete();

            // Eliminar registros en milestone_files con file que empiece con "project_files/"
            MilestoneFile::where('milestone_id', $milestoneId)
                ->where('file', 'like', 'project_files/%')
                ->delete();

            \Log::info("Se eliminaron las puntuaciones y archivos del milestone ID {$milestoneId}");

            return redirect()
                ->back()
                ->with('success', 'Puntuaciones y archivos eliminados correctamente.');
        } catch (\Throwable $e) {
            \Log::error("Error al eliminar puntuaciones o archivos: " . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Ocurrió un error al eliminar la puntuación o archivos: ' . $e->getMessage());
        }
    }



    public function taskCreate($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $taskType = TaskType::select('id', 'name', 'project_type')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'project_type' => $task->project_type,
                'name' => __($task->name)
            ];
        });

        // Traer proyectos del workspace actual siempre
        $projects = Project::where('workspace', $currentWorkspace->id)->get();

        // Si viene de my_milestone_board con un project_id, incluir su proyecto aunque sea de otro workspace
        if (request()->get('fromMyMilestoneBoard') && request()->get('project_id')) {
            $projectId = request()->get('project_id');
            $otherProject = Project::find($projectId);

            if ($otherProject && !$projects->contains('id', $projectId)) {
                $projects = $projects->concat([$otherProject]);
            }
        }

        $milestones = Milestone::all();

        $users = User::orderBy('name', 'asc')->get();

        return view('projects.taskCreate', compact('currentWorkspace', 'projects', 'taskType', 'milestones', 'users'));
    }

    public function taskStore(Request $request, $slug)
    {
        $request->validate([
            'project_id' => 'required',
            'milestone_id' => 'required',
            'type_id' => 'required',
            'estimated_date' => 'required',
            'task_assign_override' => 'nullable|exists:users,id',
        ]);

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = Auth::user();

        // Si viene de my_milestone_board, permitir proyectos de otros workspaces
        // Si no, validar que el proyecto pertenezca al workspace actual
        if ($request->get('fromMyMilestoneBoard')) {
            $project = Project::where('id', $request->project_id)->first();
        } else {
            $project = Project::where('id', $request->project_id)
                ->where('workspace', $currentWorkspace->id)
                ->first();
        }

        if (!$project) {
            return redirect()->back()->with('error', 'Proyecto no encontrado o no pertenece al espacio de trabajo actual.');
        }

        $selectedMilestone = Milestone::select('id', 'milestone_assigned_to_user')
            ->find($request->milestone_id);

        $canOverrideAssignee = in_array((int) $project->type, [3, 5], true)
            && $selectedMilestone
            && (int) ($selectedMilestone->milestone_assigned_to_user ?? 0) === (int) $user->id;

        $assigneeId = ($canOverrideAssignee && !empty($request->task_assign_override))
            ? (int) $request->task_assign_override
            : (int) $user->id;

        // Detectar si el type_id seleccionado es el "Custom"
        $type = TaskType::find($request->type_id);
        $isCustom = $type && strtolower(trim($type->name)) === 'custom';

        // Si es custom, validar el nombre
        if ($isCustom) {
            $request->validate([
                'custom_task_name' => 'required|string|max:255',
            ]);
        }

        // ---- Duplicados ----
        if ($isCustom) {
            // Para custom: evitar duplicado por milestone + usuario + nombre custom
            $existingTask = Task::where('milestone_id', $request->milestone_id)
                ->where('type_id', $request->type_id)
                ->where('assign_to', $assigneeId)
                ->whereHas('customTask', function ($q) use ($request) {
                    $q->whereRaw('LOWER(name) = ?', [strtolower(trim($request->custom_task_name))]);
                })
                ->first();
        } else {
            // Para no custom: tu regla actual
            $existingTask = Task::where('milestone_id', $request->milestone_id)
                ->where('type_id', $request->type_id)
                ->where('assign_to', $assigneeId)
                ->first();
        }

        if ($existingTask) {
            return redirect()->back()->with('error', 'Error, no se pueden duplicar tareas');
        }

        // Crear la Task
        $task = new Task();
        $task->project_id = $request->project_id;
        $task->milestone_id = $request->milestone_id;
        $task->type_id = $request->type_id;
        $task->start_date = date('Y-m-d');
        $task->estimated_date = $request->estimated_date;
        $task->assign_to = $assigneeId;
        $task->save();

        // Si es custom, crear el registro en custom_tasks
        if ($isCustom) {
            CustomTasks::create([
                'id_task' => $task->id,
                'name'    => trim($request->custom_task_name),
            ]);
            // alternativa usando la relación:
            // $task->customTask()->create(['name' => trim($request->custom_task_name)]);
        }

        // Actualizar milestone status
        $milestone = Milestone::find($request->milestone_id);

        if (!$milestone) {
            return redirect()->back()->with('error', 'Encargo no encontrado.');
        }

        if (empty($milestone->title)) {
            return redirect()->back()->with('error', 'Error: El encargo no tiene título.');
        }

        $milestone->status = 2;
        $milestone->save();

        return redirect()->back()->with(['success' => __('Task Created Successfully!')]);
    }


    public function milestoneOrderUpdate(Request $request, $slug, $projectID)
    {
        \Log::info('info desde el order update');
        \Log::info($request->all());
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if (isset($currentWorkspace)) {
            if (isset($request->sort)) {
                foreach ($request->sort as $index => $milestoneID) {
                    $milestone = Milestone::find($milestoneID);
                    $milestone->order = $index;
                    $milestone->save();
                }
            }
            $milestone = Milestone::find($request->id);

            $project = Project::find($milestone->project_id);

            if ($request->new_status != $request->old_status) {
                $new_status = Stage::find($request->new_status);
                $old_status = Stage::find($request->old_status);
                $user = Auth::user();
                $milestone = Milestone::find($request->id);

                \Log::info('Milestone actualización - Antes:', $milestone->toArray());

                $milestone->status = $request->new_status;

                // Si hay un comentario para el cambio de status (de review a en curso)
                if ($request->has('status_change_comment') && !empty($request->status_change_comment)) {
                    $comment = $request->status_change_comment;
                    $timestamp = now()->format('Y-m-d H:i:s');
                    $userName = $user->name;

                    // Agregar el comentario al inicio de la descripción/summary
                    $prefix = "[$userName] (Review → In Progress):\n$comment\n\n";
                    $milestone->summary = $prefix . ($milestone->summary ?? '');
                }

                // Verificar que el título no esté vacío antes de guardar
                if (empty($milestone->title)) {
                    \Log::error('CRÍTICO: Intento de guardar milestone sin título. ID: ' . $milestone->id . ' Status: ' . $milestone->status);
                    $milestone->title = 'SIN TÍTULO'; // Fallback de emergencia
                }

                $milestone->save();
                \Log::info('Milestone actualización - Después:', $milestone->toArray());

                if ($milestone->status == 4) {
                    $milestone->finalization_date = date('Y-m-d');
                    $milestone->save();

                    $project = Project::find($milestone->project_id);
                    if (isset($project)) {
                        $project->updateProjectStatus();
                    }
                }

                // Si el cambio es de status 3 a 2, eliminar puntuaciones
                if ($request->old_status == 3 && $request->new_status == 2) {
                    try {
                        \Log::info('Eliminando puntuaciones para milestone: ' . $milestone->id);
                        \DB::table('evaluation_criteria_milestone')
                            ->where('milestone_id', $milestone->id)
                            ->delete();
                        \Log::info('Puntuaciones eliminadas correctamente');
                    } catch (\Exception $e) {
                        \Log::error('Error al eliminar puntuaciones: ' . $e->getMessage());
                    }
                }

                $project->updateProjectStatus();
                //Add log
                $status = Stage::find($milestone->status);

                ActivityLog::create([
                    'user_id' => \Auth::user()->id,
                    'user_type' => get_class(\Auth::user()),
                    'project_id' => $milestone->project_id,
                    'log_type' => 'has updated the milestone status to',
                    'remark' => json_encode(['milestoneStatus' => __($status->name)]),
                ]);

                $name = $user->name;
                $id = $user->id;

                return $milestone->toJson();
            }
        }
    }

    public function taskOrderUpdate(Request $request, $slug, $projectID)
    {
        // dd($request->all());

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $projectID)->first();
        $user1 = $currentWorkspace->id;
        $setting = Utility::getAdminPaymentSettings();
        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $task = Milestone::find($taskID);
                $task->order = $index;
                $task->save();
            }
        }

        if ($request->new_status != $request->old_status) {
            $new_status = Stage::find($request->new_status);
            $old_status = Stage::find($request->old_status);
            $user = Auth::user();
            $task = Milestone::find($request->id);
            $task->status = $request->new_status;
            $task->save();

            $name = $user->name;
            $id = $user->id;

            // ActivityLog::create(
            //     [
            //         'user_id' => $id,
            //         'user_type' => get_class($user),
            //         'project_id' => $projectID,
            //         'log_type' => 'Move',
            //         'remark' => json_encode(
            //             [
            //                 'title' => $task->title,
            //                 'old_status' => $old_status->name,
            //                 'new_status' => $new_status->name,
            //             ]
            //         ),
            //     ]
            // );

            // $settings = Utility::getPaymentSetting($user1);

            // $uArr = [
            //     // 'user_name' => $user->name,
            //     'project_name' => $project_name->name,
            //     'user_name' => Auth::user()->name,
            //     'task_title' => $task->title,
            //     'old_stage' => $old_status->name,
            //     'new_stage' => $new_status->name,
            //     'app_url' => env('APP_URL'),
            //     'app_name'  => $setting['app_name'],
            // ];

            // if (isset($settings['taskmove_notificaation']) && $settings['taskmove_notificaation'] == 1) {
            //     Utility::send_slack_msg('Task Stage Updated', $user1, $uArr);
            // }

            // if (isset($settings['telegram_taskmove_notificaation']) && $settings['telegram_taskmove_notificaation'] == 1) {
            //     Utility::send_telegram_msg('Task Stage Updated', $uArr, $user1);
            // }

            // //webhook
            // $module = 'Task Stage Updated';
            // // $webhook=  Utility::webhookSetting($module);
            // $webhook =  Utility::webhookSetting($module, $user1);

            // if ($webhook) {
            //     $parameter = json_encode($task);
            //     // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            //     $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            //     // if($status == true)
            //     // {
            //     //     return redirect()->back()->with('success', __('Task Stage successfully created!'));
            //     // }
            //     // else
            //     // {
            //     //     return redirect()->back()->with('error', __('Webhook call failed.'));
            //     // }
            // }

            return $task->toJson();
        }
    }

    public function taskEdit($slug, $projectID, $taskId)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        // if ($objUser->getGuard() == 'client') {
        //     $project = Project::select('projects.*')
        //         ->where('projects.workspace', '=', $currentWorkspace->id)
        //         ->where('projects.id', '=', $projectID)->first();
        //     $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')
        //         ->where('client_projects.client_id', '=', $objUser->id)
        //         ->where('projects.workspace', '=', $currentWorkspace->id)->get();
        // } else {
        $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)->first();
        $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)->get();
        // }
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $projectID)->get();
        $task = Task::find($taskId);
        $task->assign_to = explode(",", $task->assign_to);

        return view('projects.taskEdit', compact('currentWorkspace', 'project', 'projects', 'users', 'task'));
    }

    public function taskUpdate(Request $request, $slug, $projectID, $taskID)
    {

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $request->project_id)->first();

        if ($project) {
            $post = $request->all();
            $post['assign_to'] = implode(",", $request->assign_to);
            $task = Task::find($taskID);
            $task->update($post);

            return redirect()->back()->with('success', __('Task Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Task!"));
        }
    }

    public function taskDestroy($slug, $projectID, $taskID)
    {
        $task = Task::find($taskID);
        $project = Project::find($projectID);

        if (!$task) {
            return redirect()->back()->with('error', __("Task not found!"));
        }

        try {
            DB::transaction(function () use ($task) {
                Timesheet::where('task_id', $task->id)->delete();

                $task->delete();
                if (isset($project)) {
                    $project->updateProjectStatus();
                }
            });

            return redirect()->back()->with('success', __('Task Deleted Successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __("Error deleting task!"));
        }
    }

    public function showTask($slug, $taskID, $first_day, $seventh_day)
    {

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $task = Task::find($taskID);
        $project = Project::find($task->project_id);
        $task_name = TaskType::select('name')->where('id', '=', $task->type_id)->first();
        $milestone = Milestone::select('title')->where('id', '=', $task->milestone_id)->first();
        $assign_to = User::find($task->assign_to);

        $firstDay = Carbon::parse($first_day);
        $seventhDay = Carbon::parse($seventh_day);

        $startOfWeek = $firstDay->format('Y-m-d');
        $endOfWeek = $seventhDay->format('Y-m-d');

        // Consulta para calcular el total de horas en la semana actual para la tarea especificada
        $horasPorTarea = Timesheet::where('task_id', $taskID)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->select(DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(time))) as total_time"))
            ->first();

        $startOfWeekFormatted = $firstDay->isoFormat('ddd DD MMM');
        $endOfWeekFormatted = $seventhDay->isoFormat('ddd DD MMM');

        $taskDetail = [
            'workspace' => $currentWorkspace,
            'project' => $project,
            'task' => $task,
            'task_name' => $task_name->name ?? 'Sin nombre',
            'milestone' => $milestone->title ?? 'Sin título',
            'assign_to' => $assign_to ?? 'No asignado',
            'start_of_week' => $startOfWeekFormatted,
            'end_of_week' => $endOfWeekFormatted,
            'total_time_this_week' => $horasPorTarea->total_time ?? '00:00:00',
        ];

        return view('projects.taskShow', compact('taskDetail'));
    }

    public function taskShow($slug, $projectID, $taskID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $task = Task::find($taskID);
        $project  = Project::find($projectID);
        if (Auth::user() != null) {
            $objUser         = Auth::user();
        } else {
            // $objUser         = User::where('id', $project->created_by)->first();
            $objUser         = User::where('currant_workspace', $currentWorkspace)->first();
        }
        $clientID = '';
        if ($objUser->getGuard() == 'client') {
            $clientID = $objUser->id;
        }
        return view('projects.taskShow', compact('currentWorkspace', 'task', 'clientID'));
    }

    public function taskDrag(Request $request, $slug, $projectID, $taskID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $task = Task::find($taskID);
        $task->start_date = $request->start;
        $task->due_date = $request->end;
        $task->save();
    }

    public function commentStore(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        $task = Task::find($taskID);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $projectID)->first();
        $user1 = $currentWorkspace->id;
        $post = [];
        $post['task_id'] = $taskID;
        $post['comment'] = $request->comment;
        $setting = Utility::getAdminPaymentSettings();
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $comment = Comment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }
        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'comment.destroy',
                [
                    $currentWorkspace->slug,
                    $projectID,
                    $taskID,
                    $comment->id,
                ]
            );
        }

        $settings = Utility::getPaymentSetting($user1);

        $uArr = [
            // 'user_name' => $user->name,
            'project_name' => $project_name->name,
            'user_name' => Auth::user()->name,
            'task_title' => $task->title,
            'app_url' => env('APP_URL'),
            'app_name'  => $setting['app_name'],
        ];
        if (isset($settings['taskcom_notificaation']) && $settings['taskcom_notificaation'] == 1) {

            Utility::send_slack_msg('New Task Comment', $user1, $uArr);
        }

        if (isset($settings['telegram_taskcom_notificaation']) && $settings['telegram_taskcom_notificaation'] == 1) {
            Utility::send_telegram_msg('New Task Comment', $uArr, $user1);
        }

        //webhook
        $module = 'New Task Comment';
        // $webhook=  Utility::webhookSetting($module);
        $webhook =  Utility::webhookSetting($module, $user1);

        if ($webhook) {
            $parameter = json_encode($task);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            // if($status == true)
            // {
            //     return redirect()->back()->with('success', __('Task successfully created!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Webhook call failed.'));
            // }
        }

        return $comment->toJson();
    }

    public function commentDestroy(Request $request, $slug, $projectID, $taskID, $commentID)
    {
        $comment = Comment::find($commentID);
        $comment->delete();

        return "true";
    }



    public function commentStoreFile(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(['file' => 'required']);
        $dir = 'tasks/';
        $fileName = $taskID . time() . "_" . $request->file->getClientOriginalName();
        // $request->file->storeAs('tasks', $fileName);

        $path = Utility::upload_file($request, 'file', $fileName, $dir, []);
        if ($path['flag'] == 1) {
            // Utility::upload_file($request,'file',$fileName,$dir,[]);
            $file = $path['url'];
        } else {
            return redirect()->back()->with('error', __($path['msg']));
        }

        $post['task_id'] = $taskID;
        $post['file'] = $fileName;
        $post['name'] = $request->file->getClientOriginalName();
        $post['extension'] = "." . $request->file->getClientOriginalExtension();
        $post['file_size'] = round(($request->file->getMaxFilesize() / 1024) / 1024, 2) . ' MB';
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $TaskFile = TaskFile::create($post);
        $user = $TaskFile->user;
        $TaskFile->deleteUrl = '';
        if (empty($clientID)) {
            $TaskFile->deleteUrl = route(
                'comment.destroy.file',
                [
                    $currentWorkspace->slug,
                    $projectID,
                    $taskID,
                    $TaskFile->id,
                ]
            );
        }

        return $TaskFile->toJson();
    }
    public function checkTaskHours(Request $request, $slug, $milestone_id)
    {
        \Log::info('Function checkTaskHours');
        \Log::info($request->id);

        // Obtener todos los IDs de las tareas asociadas al milestone_id
        $taskIds = Task::where('milestone_id', $request->id)->pluck('id');

        // Registrar los IDs en el log
        \Log::info('Task IDs:', $taskIds->toArray());

        // Verificar si cada task_id tiene al menos una entrada en timesheets
        $tasksWithTimesheets = Timesheet::whereIn('task_id', $taskIds)
            ->pluck('task_id')
            ->unique(); // Obtener solo IDs únicos

        // Comprobar si todas las tareas tienen al menos una entrada en timesheets
        // Además, debe existir al menos una tarea para permitir el paso a review
        $hasTasks = $taskIds->isNotEmpty();
        $allExist = $hasTasks && $taskIds->diff($tasksWithTimesheets)->isEmpty();

        \Log::info('Tiene tareas asociadas: ' . ($hasTasks ? 'Sí' : 'No'));
        \Log::info('Todas las tareas tienen al menos una entrada en timesheets: ' . ($allExist ? 'Sí' : 'No'));

        return response()->json([
            'all_exist' => $allExist,
            'has_tasks' => $hasTasks,
        ]);
    }

    public function downloadCsv($project_id)
    {
        // Cargamos los timesheets con sus relaciones
        $timesheets = Timesheet::with(['task.milestone', 'task.project', 'getUser'])
            ->where('project_id', $project_id)
            ->get();

        $fileName = "timesheet_project_{$project_id}.csv";
        $handle = fopen('php://temp', 'r+');

        // Cabecera del CSV
        fputcsv($handle, ['Usuario', 'Dia', 'Encargo', 'Tarea', 'Horas']);

        foreach ($timesheets as $t) {
            fputcsv($handle, [
                $t->getUser->name ?? 'Unknown',                         // Usuario
                Carbon::parse($t->date)->format('Y-m-d'),               // Día
                $t->task->milestone->title ?? 'Sin encargo',            // Encargo
                $t->task->type->name ?? 'Sin tarea',                   // Tarea
                Carbon::parse($t->time)->format('H:i'),                 // Horas imputadas
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    public function commentDestroyFile(Request $request, $slug, $projectID, $taskID, $fileID)
    {
        $commentFile = TaskFile::find($fileID);

        if ($commentFile) {
            // $path = storage_path('tasks/' . $commentFile->file);
            $logo = Utility::get_file('tasks/');
            $path = $logo . $commentFile->file;
            if (file_exists($path)) {
                File::delete($path);
            }
            $commentFile->delete();

            return "true";
        } else {
            return "false";
        }
    }

    public function getSearchJson($slug, $search)
    {
        $user = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $objProject = Project::select(['projects.id', 'projects.name', 'projects.ref_mo'])
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where(function ($query) use ($search) {
                $query->where('projects.name', 'LIKE', "%" . $search . "%")
                    ->orWhere('projects.ref_mo', 'LIKE', "%" . $search . "%");
            })
            ->get();

        $arrProject = [];
        foreach ($objProject as $project) {
            $displayName = $project->name;
            if (!empty($project->ref_mo)) {
                $displayName .= " - " . $project->ref_mo;
            }

            $arrProject[] = [
                'text' => $displayName,
                'link' => route('projects.show', [$currentWorkspace->slug, $project->id]),
            ];
        }

        return response()->json(['Projects' => $arrProject]);
    }


    public function getMoJson($slug, $search = null)
    {
        $query = MasterObra::query()->select(['ref_mo', 'name']);

        if ($search) {
            // Optimización: buscar primero por ref_mo con coincidencia al inicio (más rápido)
            // Si es corto y comienza con número, probablemente está buscando por referencia
            if (strlen($search) <= 10 && is_numeric(substr($search, 0, 1))) {
                $query->where('ref_mo', 'LIKE', $search . "%")
                    ->orWhere('ref_mo', 'LIKE', "%" . $search . "%")
                    ->orWhere('name', 'LIKE', "%" . $search . "%");
            } else {
                $query->where(function ($query) use ($search) {
                    $query->where('ref_mo', 'LIKE', "%" . $search . "%")
                        ->orWhere('name', 'LIKE', "%" . $search . "%");
                });
            }
        }

        $objMo = $query->with(['clients' => function ($query) {
            $query->select('potential_clients.potential_customer_id', 'potential_clients.name', 'potential_clients.customer_id');
        }])->limit(50)->paginate(25);

        $arrMo = $objMo->toArray();

        return response()->json([
            'mo' => $arrMo,
        ]);
    }

    public function getClientJson($slug, $search = null)
    {

        $query = PotentialClient::query()->select(['potential_customer_id', 'name', 'customer_id']);
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('potential_customer_id', 'LIKE', "%" . $search . "%")
                    ->orWhere('name', 'LIKE', "%" . $search . "%");
            });
        }

        $objclient = $query->with(['obras' => function ($query) {
            $query->select('master_obras.ref_mo', 'master_obras.name');
        }])->paginate(25);

        $arrClients = $objclient->toArray();

        return response()->json([
            'clients' => $arrClients,
        ]);
    }
    public function getProjectsJson($slug, $search = null)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $query = Project::query()
            ->select(['id', 'name', 'ref_mo', 'type'])
            ->with(['typeRel:id,name'])
            ->where('workspace', '=', $currentWorkspace->id);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('ref_mo', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        $objProject = $query->paginate(25);

        try {
            $phaseOptions = $this->getEnumValues('milestone_phases', 'phases');
        } catch (\Throwable $e) {
            $phaseOptions = MilestonePhases::PHASES;
        }

        $objProject->getCollection()->transform(function ($project) use ($phaseOptions) {
            if (in_array((int) $project->type, [3, 5], true)) {
                $this->ensureProjectDefaultStages($project);
            }

            $project->is_phase_project = in_array((int) $project->type, [3, 5], true);
            $project->phases = $project->is_phase_project ? array_values($phaseOptions) : [];

            return $project;
        });

        $projectIds = collect($objProject->items())->pluck('id')->toArray();
        $stagesByProject = MilestoneStageProject::whereIn('project_id', $projectIds)
            ->orderBy('name', 'asc')
            ->get(['project_id', 'name'])
            ->groupBy('project_id')
            ->map(function ($stages) {
                return $stages->pluck('name')->values()->toArray();
            });

        $objProject->getCollection()->transform(function ($project) use ($stagesByProject) {
            $project->stages = $stagesByProject->get($project->id, []);

            return $project;
        });

        return response()->json([
            'projects' => $objProject,
        ]);
    }

    public function getSalesJson($slug, $search = null)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $query = User::query()->select(['id', 'name'])->where('currant_workspace', '=', $currentWorkspace->id);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%" . $search . "%");
            });
        }

        $objSales = $query->paginate(25);

        $arrSales = $objSales->toArray();

        return response()->json([
            'salesManagers' => $arrSales,
        ]);
    }


    private function getEnumValues($table, $column)
    {
        $type = DB::selectOne("
        SELECT COLUMN_TYPE 
        FROM information_schema.COLUMNS 
        WHERE TABLE_NAME = ? 
          AND COLUMN_NAME = ?
    ", [$table, $column]);

        preg_match("/^enum\((.*)\)$/", $type->COLUMN_TYPE, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn($v) => trim($v, "'"))
            ->toArray();
    }

    private function getDefaultMilestoneStages()
    {
        try {
            $stages = $this->getEnumValues('milestone_stages', 'stages');

            return collect($stages)
                ->map(fn($stage) => trim($stage))
                ->filter()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            return MilestoneStages::STAGES;
        }
    }

    private function ensureProjectDefaultStages($project)
    {
        if (!$project || !in_array((int) $project->type, [3, 5], true)) {
            return;
        }

        $exists = MilestoneStageProject::where('project_id', $project->id)->exists();
        if ($exists) {
            return;
        }

        foreach ($this->getDefaultMilestoneStages() as $stageName) {
            MilestoneStageProject::firstOrCreate([
                'project_id' => $project->id,
                'name' => $stageName,
            ]);
        }
    }

    public function milestone($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_type = ProjectType::select('id', 'name')->get();
        $users = User::orderBy('name', 'asc')->get();

        try {
            $phases = $this->getEnumValues('milestone_phases', 'phases');
        } catch (\Throwable $e) {
            $phases = MilestonePhases::PHASES;
        }
        $stagesProject = [];

        if ($projectID == -1) {
            $project_id = -1;
            $projects = Project::select('projects.*')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.status', '!=', 'Finished')
                ->get();

            return view('projects.milestone', compact('currentWorkspace', 'projects', 'project_id', 'project_type', 'users', 'phases'));
        } else {
            $project_id = $projectID;
            $project = Project::find($projectID);
            if ($project && in_array((int) $project->type, [3, 5], true)) {
                $this->ensureProjectDefaultStages($project);

                $stagesProject = MilestoneStageProject::where('project_id', $project->id)
                    ->orderBy('name', 'asc')
                    ->pluck('name')
                    ->toArray();
            }

            return view('projects.milestone', compact('currentWorkspace', 'project', 'project_id', 'project_type', 'users', 'phases', 'stagesProject'));
        }
    }

    public function stagesPopup($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::findOrFail($projectID);

        $this->ensureProjectDefaultStages($project);

        $stages = MilestoneStageProject::where('project_id', $project->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('projects.stages_popup', compact('currentWorkspace', 'project', 'stages'));
    }

    public function stagesStore($slug, $projectID, Request $request)
    {
        $project = Project::findOrFail($projectID);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('milestone_stages_project', 'name')->where(function ($query) use ($project) {
                    return $query->where('project_id', $project->id);
                }),
            ],
        ]);

        MilestoneStageProject::create([
            'project_id' => $project->id,
            'name' => trim($validated['name']),
        ]);

        return redirect()->back()->with('success', __('Stage created successfully.'));
    }

    public function stagesUpdate($slug, $projectID, $stageID, Request $request)
    {
        $project = Project::findOrFail($projectID);
        $stage = MilestoneStageProject::where('project_id', $project->id)->findOrFail($stageID);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('milestone_stages_project', 'name')
                    ->where(function ($query) use ($project) {
                        return $query->where('project_id', $project->id);
                    })
                    ->ignore($stage->id),
            ],
        ]);

        $stage->update([
            'name' => trim($validated['name']),
        ]);

        MilestoneStages::whereHas('milestone', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->where('stages', $stage->getOriginal('name'))->update([
            'stages' => trim($validated['name']),
        ]);

        return redirect()->back()->with('success', __('Stage updated successfully.'));
    }

    public function stagesDestroy($slug, $projectID, $stageID)
    {
        $project = Project::findOrFail($projectID);
        $stage = MilestoneStageProject::where('project_id', $project->id)->findOrFail($stageID);

        $isStageInUse = MilestoneStages::whereHas('milestone', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->where('stages', $stage->name)->exists();

        if ($isStageInUse) {
            return redirect()->back()->with('error', __('This stage is being used in one or more order forms.'));
        }

        $stage->delete();

        return redirect()->back()->with('success', __('Stage deleted successfully.'));
    }

    public function milestoneStore($slug, $projectID, Request $request)
    {
        if (is_numeric($request->project_id)) {
            $project = Project::find($request->project_id);
            if (!$project) {
                return response()->json(['error' => 'Proyecto no encontrado'], 404);
            }
        } else {
            $clipoId = ClientsMo::where('ref_mo', $request->ref_mo)->value('potential_customer_id') ?? '';

            $newRequest = Request::create('/fake-url', 'POST', [
                'name' => $request->project_id,
                'ref_mo' => $request->ref_mo,
                'isReload' => true,
                'project_type' => 1,
                'clipo' => $clipoId,
                'created_by' => Auth::user()->id,
                'start_date' => now()->format('Y-m-d'),
            ]);

            try {
                $response = $this->store($slug, $newRequest);
                $data = $response->getOriginalContent();

                if (isset($data['project_id'])) {
                    $project = $data['project_id'] ?? null;
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al crear el proyecto'], 500);
            }
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        // Validación de los campos requeridos
        $rules = [
            'title' => 'required',
            'assing_to' => 'required',
            'end_date' => 'required|date',
            'files' => 'nullable|array',
        ];

        // ✅ Si el proyecto es tipo 3 o 5, phase es obligatoria
        if (in_array((int) $project->type, [3, 5], true)) {
            $rules['phase'] = 'required|in:Planificación,Diseño,Implementación,Documentación,Validación funcional,Explotación comercial';
            // 👆 cambia por los valores reales del enum de milestone_phases.phases
        }

        if (in_array((int) $project->type, [3, 5], true)) {
            $this->ensureProjectDefaultStages($project);

            $availableStages = MilestoneStageProject::where('project_id', $project->id)
                ->pluck('name')
                ->toArray();

            $rules['stage'] = ['nullable', Rule::in($availableStages)];
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Validation failed for milestone creation', [
                'errors' => $validator->errors()->all(),
                'request' => $request->all(),
            ]);
            $messages = $validator->getMessageBag();

            // Si es AJAX, devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $messages->first()
                ], 422);
            }

            return redirect()->back()->with('error', $messages->first());
        }

        // Validar end_date: si es anterior a hoy, usar hoy
        $inputEndDate = Carbon::parse($request->end_date)->startOfDay();
        $today = Carbon::today();

        $finalEndDate = $inputEndDate->lessThan($today)
            ? $today->toDateString()
            : $inputEndDate->toDateString();

        // Crear el milestone
        $milestone = new Milestone();
        $milestone->project_id = $project->id;
        $milestone->title = $request->title;
        $milestone->assign_to = $request->assing_to;
        $milestone->start_date = date('Y-m-d');
        $milestone->company = $request->company ?? '';
        $milestone->contractor = $request->contractor ?? '';
        $milestone->contractorAdress = $request->contractorAdress ?? '';
        $milestone->jobsiteAdress = $request->jobsiteAdress ?? '';
        $milestone->milestone_assigned_to_user = $request->req_assing_to ?? '';
        $milestone->planned_end_date = $request->planned_end_date ?? '';
        $milestone->created_by = Auth::user()->id;
        $milestone->end_date = $finalEndDate; // ✅ Fecha corregida aquí
        $milestone->summary = $request->description ?? '';
        $milestone->priority = $request->priority === '' ? null : $request->priority;
        $milestone->save();

        // ✅ Guardar fase para proyectos tipo 3 o 5
        if (in_array((int) $project->type, [3, 5], true)) {
            \App\Models\MilestonePhases::updateOrCreate(
                ['id_milestone' => $milestone->id],
                ['phases' => $request->phase]
            );
        }

        if (in_array((int) $project->type, [3, 5], true) && !empty($request->stage)) {
            $selectedStageName = trim((string) $request->stage);
            $selectedStageId = MilestoneStageProject::where('project_id', $project->id)
                ->where('name', $selectedStageName)
                ->value('id');

            MilestoneStages::updateOrCreate(
                ['id_milestone' => $milestone->id],
                [
                    'stages' => $selectedStageName,
                    'milestone_stage_project_id' => $selectedStageId,
                ]
            );
        }

        if (isset($project)) {
            $project->updateProjectStatus();
        }

        // Subida de archivos
        if ($request->hasFile('files')) {
            $projectFolder = $this->sanitizePath($project->name);
            $milestoneFolder = $this->sanitizePath($milestone->title);
            $dir = 'project_files/' . $projectFolder . '/' . $milestoneFolder;

            if (!file_exists(storage_path($dir))) {
                mkdir(storage_path($dir), 0755, true);
            }

            // Obtener nombres de archivos existentes en este milestone para evitar duplicados
            $existingFileNames = MilestoneFile::where('milestone_id', $milestone->id)
                ->pluck('name')
                ->toArray();

            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $originalName = $this->cleanFileName($file->getClientOriginalName());

                    // Generar nombre único si ya existe
                    $uniqueDisplayName = $this->generateUniqueFileName($originalName, $existingFileNames);
                    // Agregar al array para evitar duplicados en el mismo lote
                    $existingFileNames[] = $uniqueDisplayName;

                    $fileName = $milestone->id . '_' . time() . '_' . $uniqueDisplayName;
                    $file->move(storage_path($dir), $fileName);

                    $filePath = storage_path($dir . '/' . $fileName);
                    $fileSize = file_exists($filePath)
                        ? round(filesize($filePath) / 1024, 2) . ' KB'
                        : '0 KB';

                    MilestoneFile::create([
                        'milestone_id' => $milestone->id,
                        'file' => $fileName,
                        'name' => $uniqueDisplayName,
                        'extension' => $file->getClientOriginalExtension(),
                        'file_size' => $fileSize,
                        'created_by' => Auth::id(),
                        'user_type' => Auth::user()->type,
                    ]);
                } else {
                    $errorMsg = 'Uno o más archivos no son válidos.';

                    // Si es AJAX, devolver JSON
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'error' => __($errorMsg)
                        ], 422);
                    }

                    return redirect()->back()->with('error', __($errorMsg));
                }
            }
        }

        // Log de actividad
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'user_type' => get_class(Auth::user()),
            'project_id' => $project->id,
            'log_type' => 'Create Milestone',
            'remark' => json_encode(['title' => $milestone->title]),
        ]);

        // Notificación (Slack)
        $setting = Utility::getAdminPaymentSettings();
        $uArr = [
            'project_name' => $project->name,
            'user_name' => Auth::user()->name,
            'milestone_title' => $milestone->title,
            'app_url' => env('APP_URL'),
            'app_name' => $setting['app_name'],
        ];

        if (isset($setting['milestone_notificaation']) && $setting['milestone_notificaation'] == 1) {
            Utility::send_slack_msg('New Milestone', $currentWorkspace->id, $uArr);
        }

        // Siempre devolver JSON si es una solicitud AJAX o si viene del modal
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'milestone_id' => $milestone->id,
                'project_id' => $project->id,
            ]);
        }

        return redirect()->back()->with('success', __('Milestone created successfully!'));
    }

    public function milestoneAssign($slug, $milestoneID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::find($milestoneID);
        $users = User::orderBy('name', 'asc')->get();
        $project_type = ProjectType::select('id', 'name')->get();


        return view('projects.milestone_assign', compact('currentWorkspace', 'milestone', 'users', 'project_type'));
    }

    public function milestoneWorkload($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $users = User::orderBy('name', 'asc')->get();

        // Determinar los project_ids a buscar según el projectID
        if ($projectID == -1) {
            // Paso 1: Obtener el ID del workspace
            $workspaceId = $currentWorkspace->id;

            // Paso 2: Obtener todos los proyectos del workspace
            $projects = Project::where('workspace', $workspaceId)
                ->select('id', 'name')
                ->get();

            $projectIds = $projects->pluck('id')->toArray();
        } else {
            // Si projectID es específico, solo buscar en ese proyecto
            $projectIds = [$projectID];
        }

        // Paso 3: Obtener milestones sin asignar de esos proyectos
        $milestonesSinAssignar = Milestone::whereIn('project_id', $projectIds)
            ->where(function ($query) {
                $query->whereNull('milestone_assigned_to_user')
                    ->orWhere('milestone_assigned_to_user', '');
            })
            ->with('project:id,name')
            ->get();

        // Paso 4: Obtener milestones asignados con status 1 o 2
        $milestonesAsignados = Milestone::whereIn('project_id', $projectIds)
            ->whereIn('status', [1, 2])
            ->whereNotNull('milestone_assigned_to_user')
            ->where('milestone_assigned_to_user', '!=', '')
            ->with('project:id,name')
            ->get();

        // Paso 5: Agrupar milestones por usuario y contar por status
        $milestonesAgrupados = [];
        foreach ($milestonesAsignados as $milestone) {
            $userId = $milestone->milestone_assigned_to_user;
            $status = $milestone->status;

            if (!isset($milestonesAgrupados[$userId])) {
                $user = User::find($userId);
                $milestonesAgrupados[$userId] = [
                    'user' => $user,
                    'status_1' => [],
                    'status_2' => [],
                    'count_1' => 0,
                    'count_2' => 0,
                ];
            }

            if ($status == 1) {
                $milestonesAgrupados[$userId]['status_1'][] = $milestone;
                $milestonesAgrupados[$userId]['count_1']++;
            } elseif ($status == 2) {
                $milestonesAgrupados[$userId]['status_2'][] = $milestone;
                $milestonesAgrupados[$userId]['count_2']++;
            }
        }

        \Log::info(['users' => $users]);
        \Log::info(['workspace' => $currentWorkspace]);
        \Log::info(['projectIds' => $projectIds]);
        \Log::info(['milestonesSinAssignar' => $milestonesSinAssignar]);
        \Log::info(['milestonesAgrupados' => $milestonesAgrupados]);
        return view('projects.milestone_workload', compact('currentWorkspace', 'users', 'milestonesSinAssignar', 'milestonesAgrupados'));
    }

    public function milestoneEdit($slug, $milestoneID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::find($milestoneID);

        // Cargar el proyecto para determinar si es tipo 3
        $project = null;
        $phases = [];
        $currentPhase = null;
        $stagesProject = [];
        $currentStage = null;

        if ($milestone) {
            $project = $milestone->project;

            // Si es proyecto tipo 3 o 5, cargar las phases disponibles y la phase actual del milestone
            if ($project && in_array((int) $project->type, [3, 5], true)) {
                try {
                    $phases = $this->getEnumValues('milestone_phases', 'phases');
                } catch (\Throwable $e) {
                    $phases = MilestonePhases::PHASES;
                }

                // Cargar la phase actual del milestone
                $currentPhase = MilestonePhases::where('id_milestone', $milestone->id)
                    ->orderByDesc('id')
                    ->value('phases');
            }

            if ($project && in_array((int) $project->type, [3, 5], true)) {
                $this->ensureProjectDefaultStages($project);

                $stagesProject = MilestoneStageProject::where('project_id', $project->id)
                    ->orderBy('name', 'asc')
                    ->pluck('name')
                    ->toArray();

                $currentStageRecord = MilestoneStages::where('id_milestone', $milestone->id)
                    ->orderByDesc('id')
                    ->first(['stages', 'milestone_stage_project_id']);

                if ($currentStageRecord) {
                    if (!empty($currentStageRecord->milestone_stage_project_id)) {
                        $currentStage = MilestoneStageProject::where('project_id', $project->id)
                            ->where('id', $currentStageRecord->milestone_stage_project_id)
                            ->value('name');
                    }

                    if (empty($currentStage) && !empty($currentStageRecord->stages)) {
                        $currentStage = MilestoneStageProject::where('project_id', $project->id)
                            ->where('name', trim((string) $currentStageRecord->stages))
                            ->value('name')
                            ?? trim((string) $currentStageRecord->stages);
                    }
                }
            }
        }

        return view('projects.milestoneEdit', compact('currentWorkspace', 'milestone', 'project', 'phases', 'currentPhase', 'stagesProject', 'currentStage'));
    }

    public function milestoneDestroyFile(Request $request)
    {
        $inputs = $request->input();
        \Log::info(['request' => $request->all(), 'inputs' => $inputs]);
        // Obtener el proyecto usando el ID
        $project = Project::findOrFail($inputs['idProject']);
        $projectName = $this->sanitizePath($project->name);

        // Obtener el milestone
        $milestone = Milestone::findOrFail($inputs['milestoneId']);
        $milestoneName = $this->sanitizePath($milestone->title);

        // Directorio donde se almacenan los archivos del milestone
        $milestoneFolder = 'project_files/' . $projectName . '/' . $milestoneName;

        // Buscar el archivo en la base de datos
        $fileEntry = MilestoneFile::where('milestone_id', $milestone->id)
            ->where('id', $inputs['fileID'])
            ->first();
        $fileName = $fileEntry->name;

        if (!$fileEntry) {
            return response()->json([
                'success' => false,
                'message' => __('File not found in database.')
            ], 404);
        }

        if (file_exists($fileEntry->file)) {
            \File::delete($fileEntry->file);
        }
        $fileEntry->delete();

        // Registrar la actividad
        ActivityLog::create([
            'user_id' => \Auth::user()->id,
            'user_type' => \Auth::user()->type,
            'project_id' => $inputs['idProject'],
            'log_type' => 'has delete a file',
            'remark' => json_encode(['file_name' => $fileName]),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('File Deleted Successfully.')
        ]);
    }

    public function getProjectNameByID($projectId)
    {
        $project = Project::find($projectId);
        return $project->name;
    }

    public function milestoneUpdate($slug, $milestoneID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user1 = $currentWorkspace->id;

        $setting = Utility::getAdminPaymentSettings();

        $request->validate([
            'end_date' => 'required|date',
        ]);

        $milestone = Milestone::find($milestoneID);
        if (!$milestone) {
            return redirect()->back()->with('error', 'Milestone not found');
        }

        // Validar end_date: si es anterior a hoy, usar hoy
        $inputEndDate = Carbon::parse($request->end_date)->startOfDay();
        $today = Carbon::today();

        $finalEndDate = $inputEndDate->lessThan($today)
            ? $today->toDateString()
            : $inputEndDate->toDateString();

        // Actualizar campos del milestone
        // ✅ IMPORTANTE: Solo actualizar title si no está vacío (previene pérdida de título desde formulario de asignación)
        \Log::info('milestoneUpdate - Antes de actualizar', [
            'milestone_id' => $milestone->id,
            'current_title' => $milestone->title,
            'request_title' => $request->title,
            'will_update_title' => !empty($request->title)
        ]);

        if (!empty($request->title)) {
            $milestone->title = $request->title;
        }
        $milestone->summary = $request->summary;
        // Solo actualizar milestone_assigned_to_user si viene con valor, de lo contrario mantener el actual
        if ($request->has('req_assing_to') && $request->req_assing_to !== '') {
            $milestone->milestone_assigned_to_user = $request->req_assing_to;
        }
        $milestone->end_date = $finalEndDate;
        $milestone->planned_end_date = $request->planned_end_date;
        $milestone->priority = $request->priority === '' ? null : $request->priority;

        \Log::info('milestoneUpdate - Antes de save', [
            'milestone_id' => $milestone->id,
            'title' => $milestone->title,
        ]);

        $milestone->save();

        \Log::info('milestoneUpdate - Después de save', [
            'milestone_id' => $milestone->id,
            'title' => $milestone->title,
        ]);

        // Guardar la phase si es proyecto tipo 3
        if ($request->has('phase') && !empty($request->phase)) {
            // Eliminar la phase anterior si existe
            MilestonePhases::where('id_milestone', $milestone->id)->delete();

            // Crear la nueva phase
            MilestonePhases::create([
                'id_milestone' => $milestone->id,
                'phases' => $request->phase,
            ]);
        }

        if ($request->has('stage')) {
            if (!empty($request->stage)) {
                $selectedStageName = trim((string) $request->stage);
                $selectedStageId = MilestoneStageProject::where('project_id', $milestone->project_id)
                    ->where('name', $selectedStageName)
                    ->value('id');

                MilestoneStages::updateOrCreate(
                    ['id_milestone' => $milestone->id],
                    [
                        'stages' => $selectedStageName,
                        'milestone_stage_project_id' => $selectedStageId,
                    ]
                );
            } else {
                MilestoneStages::where('id_milestone', $milestone->id)->delete();
            }
        }

        $project = Project::where('id', $milestone->project_id)->first();
        if (!$project) {
            return redirect()->back()->with('error', 'Project not found');
        }

        // Guardar nuevos archivos
        $uploadedFiles = [];
        $failedFiles = [];

        if ($request->hasFile('new_files')) {
            $projectFolder = $this->sanitizePath($project->name);
            $milestoneFolder = $this->sanitizePath($milestone->title);
            $dir = 'project_files/' . $projectFolder . '/' . $milestoneFolder;
            $MAX_FILE_SIZE = 52428800; // 50MB en bytes

            if (!file_exists(storage_path($dir))) {
                mkdir(storage_path($dir), 0755, true);
            }

            // Obtener nombres de archivos existentes en este milestone para evitar duplicados
            $existingFileNames = MilestoneFile::where('milestone_id', $milestone->id)
                ->pluck('name')
                ->toArray();

            foreach ($request->file('new_files') as $file) {
                if (!$file->isValid()) {
                    $failedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'reason' => 'Invalid file'
                    ];
                    continue;
                }

                // Validar tamaño por archivo (50MB)
                if ($file->getSize() > $MAX_FILE_SIZE) {
                    $failedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'reason' => 'File too big'
                    ];
                    continue;
                }

                $originalFileName = $file->getClientOriginalName();
                $originalFileName = $this->cleanFileName($originalFileName);

                // Generar nombre único si ya existe
                $uniqueDisplayName = $this->generateUniqueFileName($originalFileName, $existingFileNames);
                // Agregar al array para evitar duplicados en el mismo lote
                $existingFileNames[] = $uniqueDisplayName;

                $fileName = $milestone->id . '_' . time() . '_' . $uniqueDisplayName;
                $file->move(storage_path($dir), $fileName);

                $filePath = storage_path($dir . '/' . $fileName);
                $fileSize = file_exists($filePath)
                    ? round(filesize($filePath) / 1024, 2) . ' KB'
                    : '0 KB';

                MilestoneFile::create([
                    'milestone_id' => $milestone->id,
                    'file' => $fileName,
                    'name' => $uniqueDisplayName,
                    'extension' => $file->getClientOriginalExtension(),
                    'file_size' => $fileSize,
                    'created_by' => Auth::user()->id,
                    'user_type' => Auth::user()->type,
                ]);

                $uploadedFiles[] = $uniqueDisplayName;
            }
        }

        // Log de actividad
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'user_type' => get_class(Auth::user()),
            'project_id' => $project->id,
            'log_type' => 'has updated a milestone',
            'remark' => json_encode(['milestoneTitle' => $milestone->title]),
        ]);

        // Notificación Slack
        $settings = Utility::getPaymentSetting($user1);
        $uArr = [
            'project_name' => $project->name,
            'user_name' => Auth::user()->name,
            'milestone_title' => $milestone->title,
            'milestone_status' => $milestone->status,
            'app_url' => env('APP_URL'),
            'app_name'  => $setting['app_name'],
        ];

        if (isset($settings['milestonest_notificaation']) && $settings['milestonest_notificaation'] == 1) {
            Utility::send_slack_msg('Milestone Status Updated', $user1, $uArr);
        }

        // Siempre devolver JSON si es una solicitud AJAX
        if ($request->expectsJson() || $request->ajax()) {
            $uploadedCount = count($uploadedFiles);
            $failedCount = count($failedFiles);

            return response()->json([
                'success' => true,
                'uploaded_count' => $uploadedCount,
                'failed_count' => $failedCount,
                'uploaded_files' => $uploadedFiles,
                'failed_files' => $failedFiles,
                'message' => $uploadedCount > 0 && $failedCount > 0
                    ? __($uploadedCount . ' files uploaded, ' . $failedCount . ' rejected')
                    : ($uploadedCount > 0
                        ? __('All files uploaded successfully')
                        : ($failedCount > 0 ? __('All files were rejected') : __('Milestone updated')))
            ], 200);
        }

        return redirect()->back()->with('success', __('Milestone Updated Successfully!'));
    }


    public function milestoneDestroy($slug, $milestoneID)
    {
        try {
            DB::transaction(function () use ($milestoneID) {
                $milestone = Milestone::findOrFail($milestoneID);
                $project = Project::findOrFail($milestone->project_id);

                $milestone->tasks()->delete();

                $milestoneFolder = $this->sanitizePath($milestone->title);
                $projectFolder = $this->sanitizePath($project->name);
                $dir = "project_files/{$projectFolder}/{$milestoneFolder}";

                try {
                    if (Storage::exists($dir)) {
                        if (!Storage::deleteDirectory($dir)) {
                            return redirect()->back()->with('error', __('Error deleting milestone files.'));
                        }
                    }
                } catch (\Exception $e) {
                }

                MilestoneStages::where('id_milestone', $milestone->id)->delete();

                $milestone->delete();
                $project->updateProjectStatus();

                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $milestone->project_id,
                    'log_type' => 'has deleted a milestone',
                    'remark' => json_encode(['milestoneTitle' => $milestone->title]),
                ]);
            });

            return redirect()->back()->with('success', __('Milestone and associated tasks deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error deleting milestone: ') . $e->getMessage());
        }
    }


    public function milestoneShow($slug, $milestoneID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::with(['phase', 'stage.stageProject'])->find($milestoneID);
        $project = Project::find($milestone->project_id);
        $project_name = $project->name;
        $salesManager = User::find($milestone->assign_to);
        $assignedToUser = User::find($milestone->milestone_assigned_to_user);

        $delegation_name = Workspace::where('id', $project->workspace)->value('name');
        $milestoneFiles = MilestoneFile::where('milestone_id', '=', $milestone->id)
            ->select('id', 'name', 'file', 'extension')
            ->get();

        \Log::debug("MILESTONE", ['milestone' => $milestone]);
        return view('projects.milestoneShow', compact('currentWorkspace', 'milestone', 'salesManager', 'assignedToUser', 'project', 'milestoneFiles', 'delegation_name'));
    }

    public function subTaskStore(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        // dd($request->all());
        $post = [];
        $post['task_id'] = $taskID;
        $post['name'] = $request->name;
        $post['due_date'] = $request->due_date;
        $post['status'] = 0;

        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $subtask = SubTask::create($post);
        if ($subtask->user_type == 'Client') {
            $user = $subtask->client;
        } else {
            $user = $subtask->user;
        }
        $subtask->updateUrl = route(
            'subtask.update',
            [
                $slug,
                $projectID,
                $subtask->id,
            ]
        );
        $subtask->deleteUrl = route(
            'subtask.destroy',
            [
                $slug,
                $projectID,
                $subtask->id,
            ]
        );

        return $subtask->toJson();
    }

    public function subTaskUpdate($slug, $projectID, $subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->status = (int) !$subtask->status;
        $subtask->save();

        return $subtask->toJson();
    }

    public function subTaskDestroy($slug, $projectID, $subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->delete();

        return "true";
    }

    public function fileUpload($slug, $id, Request $request)
    {
        $project = Project::findOrFail($id);
        $request->validate([
            'file' => 'required'
        ]);

        $file = $request->file('file');
        // ✅ Mantener el nombre original del archivo (sin sanitizar)
        $file_name = $this->cleanFileName($file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();

        $existingNames = ProjectFile::where('project_id', $project->id)
            ->pluck('file_name')
            ->toArray();
        $uniqueFileName = $this->generateUniqueFileName($file_name, $existingNames);

        $newName = $project->id . "_" . md5(time()) . "_" . $uniqueFileName;

        $projectFolder = $this->sanitizePath($project->name);

        $dir = 'project_files/' . $projectFolder;

        $destinationPath = storage_path($dir);


        if (!file_exists($destinationPath)) {
            if (!mkdir($destinationPath, 0755, true)) {
                return response()->json(['error' => 'No se pudo crear la carpeta.'], 500);
            }
        }

        try {
            $file->move($destinationPath, $newName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al mover el archivo: ' . $e->getMessage()], 500);
        }

        $projectFile = ProjectFile::create([
            'project_id' => $project->id,
            'file_name'  => $uniqueFileName,
            'file_path'  => $newName,
            'extension'  => $extension,
        ]);

        // Registrar en el log de actividad
        ActivityLog::create([
            'user_id'    => \Auth::id(),
            'user_type'  => get_class(\Auth::user()),
            'project_id' => $project->id,
            'log_type'   => 'Upload File',
            'remark'     => json_encode(['file_name' => $uniqueFileName]),
        ]);

        // Preparar la respuesta con las rutas para descargar y eliminar el archivo
        $return = [
            'is_success' => true,
            'download'   => route('projects.file.download', [$slug, $project->id, $projectFile->id]),
            'delete'     => route('projects.file.delete', [$slug, $project->id, $projectFile->id])
        ];

        return response()->json($return);
    }


    public function milestonefileDownload(Request $request)
    {
        $inputs = $request->input();

        // Buscar el archivo en la base de datos
        $filePath = MilestoneFile::where('id', $inputs['fileId'])
            ->where('name', $inputs['fileName']) // Asegúrate de tener este campo en la BD
            ->value('file');

        $url = asset('storage/' . $filePath);
        \Log::info('Milestone file download URL: ' . $url);

        // Retornar la URL en formato JSON
        return response()->json([
            'success' => true,
            'file_url' => $url
        ]);
    }

    public function fileDownload($slug, $id, $file_id)
    {
        $project = Project::find($id);
        $file = ProjectFile::find($file_id);
        if ($file) {

            $logo = Utility::get_file('project_files/');
            $project_name = $this->sanitizePath($project->name);
            $settings = Utility::getAdminPaymentSettings();
            try {
                if ($settings['storage_setting'] == 'local') {
                    $file_path = storage_path('project_files/' . $project_name . '/' . $file->file_path);
                } else {
                    $file_path = $logo . $file->file_path;
                }

                // dd($file_path);
                $filename = $file->file_name;

                return \Response::download(
                    $file_path,
                    $filename,
                    [
                        'Content-Length: ' . filesize($file_path),
                    ]
                );
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __("File Not Exists."));
            }
        } else {
            return redirect()->back()->with('error', __('File is not exist.'));
        }
    }

    public function fileDelete($slug, $id, $file_id)
    {
        $project = Project::find($id);

        $file = ProjectFile::find($file_id);
        if ($file) {
            // $path = storage_path('project_files/' . $file->file_path);
            $logo = Utility::get_file('project_files/');
            $path = $logo . $file->file_path;
            if (file_exists($path)) {
                \File::delete($path);
            }
            $file->delete();

            return response()->json(['is_success' => true], 200);
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('File is not exist.'),
                ],
                200
            );
        }
    }
    // Timesheet
    public function timesheet($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();
        $project_id = '-1';

        if ($currentWorkspace) {

            $userTasks = Task::where('assign_to', $objUser->id)->get();
            if ($currentWorkspace->permissio == 'Owner') {
                $timesheets = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                    ->where('projects.status', '!=', 'Finished')
                    ->where('projects.workspace', '=', $currentWorkspace->id)->get();
            } else {
                $timesheets = Timesheet::select('timesheets.*')
                    ->join('projects', 'projects.id', '=', 'timesheets.project_id')
                    ->join('tasks', 'timesheets.task_id', '=', 'tasks.id')
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->where('projects.status', '!=', 'Finished')
                    ->whereRaw("find_in_set('" . $objUser->id . "',tasks.assign_to)")->get();
            }

            return view('projects.timesheet', compact('currentWorkspace', 'timesheets', 'project_id', 'userTasks'));
        } else {
            return redirect()->back()->with('error', __('Workspace Not Found.'));
        }
    }

    public function timesheetCreate($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $projects = Project::select('projects.*')
            ->where('projects.status', '!=', 'Finished')
            // ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            // ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)->get();

        $taskType = TaskType::select('id', 'name', 'project_type')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'project_type' => $task->project_type,
                'name' => __($task->name)
            ];
        });

        $milestones = Milestone::join('projects', 'milestones.project_id', '=', 'projects.id')
            ->where('projects.workspace', $objUser->currant_workspace)
            ->select('milestones.id', 'milestones.title', 'milestones.project_id')
            ->get();


        return view('projects.timesheetCreate', compact('currentWorkspace', 'projects', 'taskType', 'milestones'));
    }

    public function getTask($slug, $project_id = null)
    {

        if ($project_id) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $objUser = Auth::user();
            if ($currentWorkspace->permission == 'Owner') {
                $tasks = Task::where('project_id', '=', $project_id)->get();
            } else {
                $tasks = Task::where('project_id', '=', $project_id)->whereRaw("find_in_set('" . $objUser->id . "',assign_to)")->get();
            }

            return response()->json($tasks);
        }
    }

    public function timesheetStore($slug, Request $request)
    {
        \Log::info(['recibido en timesheetStore' => $request->all()]);
        $user = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $rules = [
            'project_id' => 'required',
            'milestone_id' => 'required',
            'task_id' => 'required',
            'date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $selectedDate = Carbon::parse($request->date)->toDateString();
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', __('Invalid date selected.'));
        }

        if ($this->isUserHolidayDate($user->id, $selectedDate)) {
            return redirect()->back()->withInput()->with('error', __('You cannot log hours on a holiday.'));
        }

        $request->merge(['date' => $selectedDate]);

        // Verificar que el proyecto exista
        $project = Project::find($request->project_id);

        if (!$project) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        // Verificar que la tarea exista y esté en el proyecto
        $task = Task::where('id', $request->task_id)
            ->where('project_id', $request->project_id)
            ->first();

        if (!$task) {
            return redirect()->back()->with('error', 'Tarea no encontrada o no pertenece al proyecto.');
        }

        // Verificar que el usuario tenga acceso a la tarea
        // Puede ser porque la tarea está asignada a él, o porque está en el proyecto (user_projects), o es admin
        $hasAccess = false;

        // 1. Si la tarea está asignada al usuario
        if ($task->assign_to == $user->id) {
            $hasAccess = true;
        }

        // 2. Si el usuario está en user_projects del proyecto
        if (!$hasAccess) {
            $userProject = UserProject::where('user_id', $user->id)
                ->where('project_id', $project->id)
                ->first();
            if ($userProject) {
                $hasAccess = true;
            }
        }

        // 3. Si el usuario es admin o es el creador del proyecto
        if (!$hasAccess && ($user->type == 'admin' || $user->type == 'owner' || $project->created_by == $user->id)) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'No tienes acceso a esta tarea.');
        }

        $timesheetEdit = Timesheet::where('project_id', $project->id)
            ->where('task_id', $task->id)
            ->whereDate('date', $request->date)
            ->where('created_by', $user->id)
            ->first();

        if ($timesheetEdit) {
            $timesheetEdit->time = sprintf('%02d:%02d:00', $request->time_hour, $request->time_minute);
            $timesheetEdit->save();
        } else {
            $timesheet = new Timesheet();
            $timesheet->project_id = $request->project_id;
            $timesheet->task_id = $task->id;
            $timesheet->date = $request->date;
            $timesheet->time = sprintf('%02d:%02d:00', $request->time_hour, $request->time_minute);
            $timesheet->created_by = $user->id;
            $timesheet->save();
        }

        $milestone = Milestone::find($task->milestone_id);

        if ($milestone) {
            if (is_null($milestone->task_start_date) || $request->date < $milestone->task_start_date) {
                $milestone->task_start_date = $request->date;
            }
            $milestone->status = 2;
            $milestone->save();
        }

        $this->employeesInProject(Auth::user()->id, $project->id);

        return redirect()->back()->with('success', __('Timesheet Updated Successfully!'));
    }

    public function timesheetTotalTime(Request $request)
    {
        \Log::info(['recibido en total time' => $request->all()]);

        $project_id    = $request->input('project_id');
        $task_id       = $request->input('task_id');
        $user_id       = $request->input('user_id');
        $selected_date = $request->input('selected_date'); // Usamos "date" para ser consistente con la vista

        // Validar que el proyecto y la tarea existan
        $project = Project::find($project_id);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }
        $task = Task::where('project_id', $project_id)
            ->where('id', $task_id)
            ->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        \Log::info(['selected_date' => $selected_date]);

        $tasktime = Timesheet::where('task_id', $task->id)
            ->where('created_by', $user_id)
            ->whereDate('date', $selected_date)
            ->pluck('time') // Devuelve un array plano con solo los valores de 'time'
            ->toArray();

        \Log::info(['tasktime corregido' => $tasktime]);


        // Por que no devuelve las horas?
        \Log::info(['tasktime' => $tasktime]);

        $totaltasktime = Utility::calculateTimesheetHours($tasktime);
        \Log::info(['totaltasktime' => $totaltasktime]);
        $totalhourstimes = explode(':', $totaltasktime);
        $totaltaskhour = $totalhourstimes[0] ?? '00';
        $totaltaskminute = $totalhourstimes[1] ?? '00';

        $timeTable = UserTimetable::where('user_id', $user_id)->first();
        $expectedHour = $this->getExpectedHoursByDate($timeTable, $selected_date);

        $workedHoursFormatted = ((int) $totaltaskhour) + (((int) $totaltaskminute) / 60);
        $dayColor = $this->resolveDayColor($workedHoursFormatted, $expectedHour);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        $user = Auth::user();
        $timesheetEdit = Timesheet::where('project_id', $task->project_id)
            ->where('task_id', $task->id)
            ->whereDate('date', $selected_date)
            ->where('created_by', $user->id)
            ->first();

        return response()->json([
            'totaltaskhour' => $totaltaskhour,
            'totaltaskminute' => $totaltaskminute,
            'dayColor'        => $dayColor,
            'is_edit' => $timesheetEdit ? true : false,
            'timesheet' => $timesheetEdit ? $timesheetEdit : null,
        ]);
    }

    public function checkHolidayDate($slug, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid date selected.'),
                'is_holiday' => false,
            ], 422);
        }

        $selectedDate = Carbon::parse($request->input('date'))->toDateString();
        $isHoliday = $this->isUserHolidayDate((int) Auth::id(), $selectedDate);

        return response()->json([
            'success' => true,
            'date' => $selectedDate,
            'is_holiday' => $isHoliday,
            'message' => $isHoliday
                ? __('You cannot log hours on a holiday.')
                : null,
        ]);
    }

    public function creatTimeshitFromOrderForms(Request $request, $slug, $project_id)
    {

        $fromTimesheet = false;
        $parseArray = [];
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project_id = $request->input('project_id');
        $task_id = $request->input('task_id');
        $selected_date = $request->input('date');
        $user_id = $request->input('user_id');

        $project = Project::find($project_id);



        if (!$project) {
            return redirect()->back()->with('error', 'Project not found');
        }

        $project_name = $project->name;

        $task = Task::where('project_id', $project_id)
            ->where('id', $task_id)
            ->first();

        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $timesheetEdit = Timesheet::where('project_id', $task->project_id)
            ->where('task_id', $task->id)
            ->whereDate('date', $selected_date)
            ->where('created_by', $user_id)
            ->first();

        $taskType = TaskType::where('project_type', $project->type)
            ->where('id', $task->type_id)
            ->first();

        $task_name = $taskType ? $taskType->name : 'Unknown';

        $today = Carbon::today()->toDateString();

        $tasktime = Timesheet::where('task_id', $task->id)
            ->where('created_by', $user_id)
            ->whereDate('date', $selected_date)->pluck('time')
            ->toArray();

        $milestone = Milestone::find($task->milestone_id);
        $milestone_name = $milestone->title ?? 'Sin hito';
        $milestone_id = $milestone->id ?? null;

        $totaltasktime = Utility::calculateTimesheetHours($tasktime);

        $totalhourstimes = explode(':', $totaltasktime);
        $totaltaskhour = $totalhourstimes[0] ?? '00';
        $totaltaskminute = $totalhourstimes[1] ?? '00';
        $taskCreationDate = $task->created_at->format('Y-m-d');

        // Obtener horario esperado según el día de la semana
        $timeTable = UserTimetable::where('user_id', $objUser->id)->first();
        $dayOfWeek = strtolower(date('l')); // Día en inglés (ej: "monday")

        $expectedHour = 0;
        if ($timeTable && isset($timeTable->$dayOfWeek)) {
            $expectedTime = explode(':', $timeTable->$dayOfWeek);
            $expectedHour = (int) $expectedTime[0]; // Solo horas
        }

        // Convertir horas trabajadas a decimal
        $workedHoursFormatted = $totaltaskhour + ($totaltaskminute / 60);
        $dayColor = '';
        // Determinar el color según la comparación
        if ($workedHoursFormatted == 0) {
            $dayColor = '#e06c71'; // Rojo (sin horas)
        } elseif ($workedHoursFormatted < $expectedHour) {
            $dayColor = '#fcf75e'; // Amarillo (horas parciales)
        } elseif ($workedHoursFormatted == $expectedHour) {
            $dayColor = '#89e186'; // Verde (horas completas)
        } elseif ($workedHoursFormatted > $expectedHour) {
            $dayColor = '#b2e2f2'; // Azul (horas extras)
        }

        $holidayDates = $this->getUserHolidayDates($objUser->id);
        $isHolidayDate = false;

        if (!empty($selected_date)) {
            try {
                $isHolidayDate = in_array(Carbon::parse($selected_date)->toDateString(), $holidayDates, true);
            } catch (\Throwable $e) {
                $isHolidayDate = false;
            }
        }

        $parseArray = [
            'project_id' => $project->id,
            'project_name' => $project_name,
            'task_id' => $task->id,
            'task_name' => $task_name,
            'milestone_id' => $milestone_id,
            'milestone_name' => __($milestone_name),
            'date' => $selected_date,
            'totaltaskhour' => $totaltaskhour,
            'totaltaskminute' => $totaltaskminute,
            'taskCreationDate' => $taskCreationDate,
        ];

        return view('projects.timesheet-create', compact('currentWorkspace', 'parseArray', 'fromTimesheet', 'dayColor', 'timeTable', 'timesheetEdit', 'holidayDates', 'isHolidayDate'));
    }

    public function timesheetUpdate($slug, $timesheetID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(
            [
                'task_id' => 'required',
                'date' => 'required',
                'time' => 'required',
            ]
        );

        $timesheet = Timesheet::find($timesheetID);
        $timesheet->project_id = $request->project_id;
        // $timesheet->task_id = $request->task_id;
        $timesheet->date = $request->date;
        $timesheet->time = $request->time;
        $timesheet->description = $request->description;
        $timesheet->save();

        return redirect()->back()->with('success', __('Timesheet Updated Successfully!'));
    }

    public function timesheetDestroy($slug, $timesheetID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $timesheet = Timesheet::find($timesheetID);
        $timesheet->delete();

        return redirect()->back()->with('success', __('Timesheet deleted Successfully!'));
    }

    public function clientPermission($slug, $project_id, $client_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id);
        $client = Client::find($client_id);
        $permissions = $client->getPermission($project_id);
        if (!$permissions) {
            $permissions = [];
        }

        return view('projects.client_permission', compact('currentWorkspace', 'project', 'client', 'permissions'));
    }

    public function clientPermissionStore($slug, $project_id, $client_id, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $clientProject = ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $project_id)->first();
        $clientProject->permission = json_encode($request->permissions);
        $clientProject->save();

        return redirect()->back()->with('success', __('Permission Updated Successfully!'));
    }

    public function bugReport($slug, $project_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $objUser = Auth::user();
        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where(
                    'projects.workspace',
                    '=',
                    $currentWorkspace->id
                )->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where(
                    'projects.workspace',
                    '=',
                    $currentWorkspace->id
                )->where('projects.id', '=', $project_id)->first();
        }

        $stages = $statusClass = [];
        $permissions = Auth::user()->getPermission($project_id);

        if (
            $project && (isset($permissions) && in_array('show bug report', $permissions))
            || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
        ) {
            $stages = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

            foreach ($stages as &$status) {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bug = BugReport::with('comments', 'user')->where('project_id', '=', $project_id);
                if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    if (isset($objUser) && $objUser) {
                        $bug->where('assign_to', '=', $objUser->id);
                    }
                }
                $bug->orderBy('order');

                $status['bugs'] = $bug->where('status', '=', $status->id)->get();
            }
            return view('projects.bug_report', compact('currentWorkspace', 'project', 'stages', 'statusClass'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugReportCreate($slug, $project_id)
    {
        $objUser = \Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $project_id)->first();
        }
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project_id)->get();

        return view('projects.bug_report_create', compact('currentWorkspace', 'project', 'users', 'arrStatus'));
    }

    public function bugReportStore(Request $request, $slug, $project_id)
    {
        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $project_id)->first();
        }

        if ($project) {
            $post = $request->all();
            $post['project_id'] = $project_id;
            $bug = BugReport::create($post);

            ActivityLog::create(
                [
                    'user_id' => $objUser->id,
                    'user_type' => get_class($objUser),
                    'project_id' => $project_id,
                    'log_type' => 'Create Bug',
                    'remark' => json_encode(['title' => $bug->title]),
                ]
            );
            Utility::sendNotification('bug_assign', $currentWorkspace, $request->assign_to, $bug);

            return redirect()->back()->with('success', __('Bug Create Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Add Bug!"));
        }
    }

    public function AddSingleNotification(Request $request)
    {
        \Log::info('ANTES DEL VALIDATE', $request->all());

        $request->validate([
            'workspace_id' => 'required|integer',
            'msg'          => 'required|string',
        ]);

        \Log::info('DESPUÉS DEL VALIDATE');
        $milestoneId = $request->milestone_id ?? null;

        if ($request->milestoneAssignedTo != -2) {
            // Crear notificación para el usuario indicado en milestoneAssignedTo
            $notification = new Notification();
            $notification->workspace_id = $request->workspace_id;
            $notification->user_id      = $request->milestoneAssignedTo;
            $notification->type         = $request->ntipe;
            $notification->data         = $request->msg;
            $notification->save();

            $usersNotified = 1;

            if ($request->ntipe == '4') {
                // Para asignaciones: comparar emails del asignado y solicitante
                // Si son diferentes, enviar ambos; si son iguales, no enviar ninguno
                $this->handleAssignmentNotification(
                    $request->milestoneRequestedBy,
                    $request->milestoneAssignedTo,
                    $request->msg,
                    $milestoneId,
                    $request->workspace_id
                );
            } else if ($request->ntipe == '5') {
                // Para pending review: enviar al creador solo si es diferente del asignado
                $this->handlePendingReviewNotification(
                    $request->msg,
                    $milestoneId,
                    $request->workspace_id
                );
            }
        } else {
            // Se obtiene la lista de user_id asociados al workspace desde la tabla user_workspaces
            $userIds = \DB::table('user_workspaces')
                ->where('workspace_id', $request->workspace_id)
                ->pluck('user_id');

            // Se recorre cada user_id y se crea una notificación para cada usuario
            foreach ($userIds as $userId) {
                $notification = new Notification();
                $notification->workspace_id = $request->workspace_id;
                $notification->user_id      = $userId;
                $notification->type         = $request->ntipe;
                $notification->data         = $request->msg;
                $notification->save();
            }
            $usersNotified = count($userIds);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Notificación agregada correctamente para ' . $usersNotified . ' usuario(s).',
        ]);
    }

    private function handleAssignmentNotification($requesterId, $assignedUserId, $message, $milestoneId, $workspaceId)
    {
        // Obtener emails
        $requesterEmail = \DB::table('users')->where('id', $requesterId)->value('email');
        $assignedEmail = \DB::table('users')->where('id', $assignedUserId)->value('email');

        \Log::info("Comparando emails - Requester: {$requesterEmail}, Assigned: {$assignedEmail}");

        // Si los emails son iguales, no enviar nada
        if ($requesterEmail === $assignedEmail) {
            \Log::info("Los emails son iguales. No se envía ningún correo.");
            return;
        }

        // Obtener milestone para datos
        $milestone = Milestone::find($milestoneId);
        $workspace = Workspace::find($workspaceId);

        if (!$milestone || !$workspace) {
            \Log::error("Milestone o Workspace no encontrado");
            return;
        }

        // Enviar email al usuario asignado (templateAssignedToUser)
        $this->sendNotificationEmail(
            $assignedEmail,
            4,
            $message,
            $milestone->priority,
            $milestone->status,
            $workspace->slug,
            $workspace->name
        );

        // Enviar email adicional al solicitante (templateAssignedToUserForRquester)
        $this->sendAditionalMailToReqBy($requesterId, $message, $assignedUserId, $milestoneId, $workspaceId);
    }

    private function handlePendingReviewNotification($message, $milestoneId, $workspaceId)
    {
        // Obtener milestone
        $milestone = Milestone::find($milestoneId);

        if (!$milestone) {
            \Log::error("Milestone con ID {$milestoneId} no encontrado");
            return;
        }

        \Log::info("Comparando valores BD - assign_to: {$milestone->assign_to}, milestone_assigned_to_user: {$milestone->milestone_assigned_to_user}");

        // Si assign_to y milestone_assigned_to_user son iguales, no enviar
        if ($milestone->assign_to == $milestone->milestone_assigned_to_user) {
            \Log::info("assign_to y milestone_assigned_to_user son iguales. No se envía correo.");
            return;
        }

        // Obtener email del creador (assign_to)
        $creatorEmail = \DB::table('users')->where('id', $milestone->assign_to)->value('email');

        if (!$creatorEmail) {
            \Log::error("No se encontró email para el usuario assign_to: {$milestone->assign_to}");
            return;
        }

        // Obtener workspace
        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            \Log::error("Workspace con ID {$workspaceId} no encontrado");
            return;
        }

        \Log::info("Enviando email de pending review a: {$creatorEmail}");

        // Enviar email al creador
        $this->sendNotificationEmail(
            $creatorEmail,
            5,
            $message,
            $milestone->priority,
            $milestone->status,
            $workspace->slug,
            $workspace->name
        );
    }

    public function sendNotificationEmail($toEmail, $notificationType, $message, $priority, $status, $slug, $workspace)

    {
        \Log::info('Enviando correo a: ' . $toEmail . ' con tipo de notificación: ' . $notificationType . ' y mensaje: ' . $message);


        if ($notificationType == '2') {
            preg_match('/^(.*?) en (.*)$/', $message, $matches);

            if (count($matches) === 3) {
                $encargo = $matches[1];
                $proyecto = $matches[2];
            } else {
                // Si no se encuentra el patrón, asignar null
                $encargo = $proyecto = null;
            }
            \Log::info('Datos extraídos para el correo de creación de milestone:' . $notificationType . ' - Encargo: ' . $encargo . ', Proyecto: ' . $proyecto .  ', Prioridad: ' . $priority . ', Estado: ' . $status . ', Slug: ' . $slug . ', Workspace: ' . $workspace);

            $htmlContent = View::make('emailTemplates.templateMilestone', [
                'notificationType' => $notificationType,
                'message' => $message,
                'encargo' => $encargo,
                'proyecto' => $proyecto,
                'priority' => $priority,
                'status' => $status,
                'slug' => $slug,
                'workspace' => $workspace,
            ])->render();
        } else if ($notificationType == '5') {

            preg_match('/^(.*?) en el proyecto (.*)$/', $message, $matches);

            if (count($matches) === 3) {
                $encargo = $matches[1];
                $proyecto = $matches[2];
            } else {
                // Si no se encuentra el patrón, asignar null
                $encargo = $proyecto = null;
            }

            \Log::info('Datos extraídos para el correo del pending review:' . $notificationType . ' - Encargo: ' . $encargo . ', Proyecto: ' . $proyecto .  ', Prioridad: ' . $priority . ', Estado: ' . $status . ', Slug: ' . $slug . ', Workspace: ' . $workspace);

            $htmlContent = View::make('emailTemplates.templatePendingReview', [
                'notificationType' => $notificationType,
                'message' => $message,
                'encargo' => $encargo,
                'proyecto' => $proyecto,
                'priority' => $priority,
                'status' => $status,
                'slug' => $slug,
                'workspace' => $workspace,
            ])->render();
        } else if ($notificationType == '4') {
            // Extraer los datos desde el mensaje
            preg_match(
                '/^(.*?) en ([^<]+)[\s\S]*?La fecha de entrega prevista es\s+(\d{2}-\d{2}-\d{4})/s',
                $message,
                $matches
            );
            if (count($matches) === 4) {
                $encargo  = trim($matches[1]); // ✅ encargo
                $proyecto = trim($matches[2]);
                $fecha    = trim($matches[3]);
            } else {
                // Manejo de error si no se encuentra el patrón
                $encargo = $proyecto = $fecha = null;
            }

            \Log::info('Datos extraídos para el correo:' . $notificationType . ' - Encargo: ' . $encargo . ', Proyecto: ' . $proyecto . ', Fecha: ' . $fecha . ', Prioridad: ' . $priority . ', Estado: ' . $status . ', Slug: ' . $slug . ', Workspace: ' . $workspace);
            $htmlContent = View::make('emailTemplates.templateAssignedToUser', [
                'notificationType' => $notificationType,
                'message' => $message,
                'encargo' => $encargo,
                'proyecto' => $proyecto,
                'fecha' => $fecha,
                'priority' => $priority,
                'status' => $status,
                'slug' => $slug,
                'workspace' => $workspace,
            ])->render();
        } else {
            return;
        }


        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
        $email->setSubject('¡Tienes novedades en project Alsina!');
        $email->addTo($toEmail);

        // Contenido HTML
        $email->addContent("text/html", $htmlContent);

        // Opcional: también agrega versión texto plano (puedes generar una versión simple o extraer texto de la vista)
        // $email->addContent("text/plain", "Tienes una nueva notificación: {$notificationType}\nMensaje: {$message}");

        $sendgrid = new \SendGrid(config('services.sendgrid.api_key'));

        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid Response Status: ' . $response->statusCode());
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo: ' . $e->getMessage());
        }
    }

    public function sendAditionalMailToReqBy($requesterId, $message, $employeeId, $milestoneId, $workspaceId)
    {
        // Obtener emails
        $requesterEmail = \DB::table('users')->where('id', $requesterId)->value('email');
        $employeeEmail = \DB::table('users')->where('id', $employeeId)->value('email');
        $employeeName = \DB::table('users')->where('id', $employeeId)->value('name');

        \Log::info("Enviando correo adicional al solicitante. Requester: {$requesterEmail}, Employee: {$employeeEmail}");

        // Si los emails son iguales, no enviar
        if ($requesterEmail === $employeeEmail) {
            \Log::info("Los emails del solicitante y del asignado son iguales. No se envía correo adicional.");
            return;
        }

        // Extraer los datos desde el mensaje
        preg_match('/^(.*?) en (.*?)\. La fecha de entrega prevista es (\d{2}-\d{2}-\d{4})$/', $message, $matches);

        if (count($matches) === 4) {
            $encargo = $matches[1];
            $proyecto = $matches[2];
            $fecha = $matches[3];
        } else {
            // Manejo de error si no se encuentra el patrón
            $encargo = $proyecto = $fecha = null;
        }

        // Obtener el milestone
        $milestone = Milestone::find($milestoneId);

        if (!$milestone) {
            \Log::error("Milestone con ID {$milestoneId} no encontrado");
            return;
        }

        // Obtener workspace
        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            \Log::error("Workspace con ID {$workspaceId} no encontrado");
            return;
        }

        $htmlContent = View::make('emailTemplates.templateAssignedToUserForRquester', [
            'message' => $message,
            'encargo' => $encargo,
            'proyecto' => $proyecto,
            'fecha' => $fecha,
            'empleado' => $employeeName,
            'priority' => $milestone->priority,
            'status' => $milestone->status,
            'slug' => $workspace->slug,
            'workspace' => $workspace->name
        ])->render();

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
        $email->setSubject('¡Tienes novedades en project Alsina!');
        $email->addTo($requesterEmail);

        // Contenido HTML
        $email->addContent("text/html", $htmlContent);
        $sendgrid = new \SendGrid(config('services.sendgrid.api_key'));

        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid Response Status para correo adicional: ' . $response->statusCode());
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo adicional: ' . $e->getMessage());
        }
    }



    public function bugReportOrderUpdate(Request $request, $slug, $project_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $bug = BugReport::find($taskID);
                $bug->order = $index;
                $bug->save();
            }
        }
        if ($request->new_status != $request->old_status) {
            $new_status = BugStage::find($request->new_status);
            $old_status = BugStage::find($request->old_status);
            $user = Auth::user();
            $bug = BugReport::find($request->id);
            $bug->status = $request->new_status;
            $bug->save();

            $name = $user->name;
            $id = $user->id;

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'user_type' => get_class($user),
                    'project_id' => $project_id,
                    'log_type' => 'Move Bug',
                    'remark' => json_encode(
                        [
                            'title' => $bug->title,
                            'old_status' => $old_status->name,
                            'new_status' => $new_status->name,
                        ]
                    ),
                ]
            );

            return $bug->toJson();
        }
    }

    public function bugReportEdit($slug, $project_id, $bug_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select(
                'projects.*'
            )->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $project_id)->first();
        }
        $users = User::select('users.*')
            ->join('user_projects', 'user_projects.user_id', '=', 'users.id')
            ->where('project_id', '=', $project_id)->get();

        $bug = BugReport::find($bug_id);
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();

        return view('projects.bug_report_edit', compact('currentWorkspace', 'project', 'users', 'bug', 'arrStatus'));
    }

    public function bugReportUpdate(Request $request, $slug, $project_id, $bug_id)
    {
        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'user_projects.project_id', '=', 'projects.id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $project_id)->first();
        }
        if ($project) {
            $post = $request->all();
            $bug = BugReport::find($bug_id);
            $bug->update($post);

            return redirect()->back()->with('success', __('Bug Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Bug!"));
        }
    }

    public function bugReportDestroy($slug, $project_id, $bug_id)
    {
        $objUser = Auth::user();
        $bug = BugReport::where('id', $bug_id)->delete();

        return redirect()->back()->with('success', __('Bug Deleted Successfully!'));
    }

    public function bugReportShow($slug, $project_id, $bug_id)
    {
        $project = Project::find($project_id);

        if (Auth::user() != null) {
            $objUser         = Auth::user();
        } else {
            $objUser         = User::where('id', $project->created_by)->first();
        }


        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $bug = BugReport::find($bug_id);

        $clientID = '';
        if ($objUser->getGuard() == 'client') {
            $clientID = $objUser->id;
        }

        return view('projects.bug_report_show', compact('currentWorkspace', 'bug', 'clientID'));
    }

    public function bugCommentStore(Request $request, $slug, $project_id, $bugID, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $post = [];
        $post['bug_id'] = $bugID;
        $post['comment'] = $request->comment;
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $comment = BugComment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }
        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'bug.comment.destroy',
                [
                    $currentWorkspace->slug,
                    $project_id,
                    $bugID,
                    $comment->id,
                ]
            );
        }

        return $comment->toJson();
    }

    public function bugCommentDestroy(Request $request, $slug, $project_id, $bug_id, $comment_id)
    {
        $comment = BugComment::find($comment_id);
        $comment->delete();

        return "true";
    }

    public function bugStoreFile(Request $request, $slug, $project_id, $bug_id, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(['file' => 'required|mimes:zip,rar,jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:204800']);
        $fileName = $bug_id . time() . "_" . $request->file->getClientOriginalName();
        $request->file->storeAs('tasks', $fileName);
        $post['bug_id']    = $bug_id;
        $post['file']      = $fileName;
        $post['name']      = $request->file->getClientOriginalName();
        $post['extension'] = "." . $request->file->getClientOriginalExtension();
        $post['file_size'] = round(($request->file->getMaxFilesize() / 1024) / 1024, 2) . ' MB';
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type']  = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type']  = 'User';
        }
        $TaskFile            = BugFile::create($post);
        $user                = $TaskFile->user;
        $TaskFile->deleteUrl = '';

        if (empty($clientID)) {
            $TaskFile->deleteUrl = route(
                'bug.comment.destroy.file',
                [
                    $currentWorkspace->slug,
                    $project_id,
                    $bug_id,
                    $TaskFile->id,
                ]
            );
        }

        return $TaskFile->toJson();
    }

    public function bugDestroyFile(Request $request, $slug, $project_id, $bug_id, $file_id)
    {
        $commentFile = BugFile::find($file_id);
        if ($commentFile) {
            // $path = storage_path('tasks/' . $commentFile->file);
            $logo = Utility::get_file('tasks/');
            $path =  $logo . $commentFile->file;
            if (file_exists($path)) {
                \File::delete($path);
            }
            $commentFile->delete();

            return "true";
        } else {
            return "false";
        }
    }

    public function allTasks($slug)
    {
        // $userObj = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        //  if ($userObj->getGuard() == 'client') {
        //      $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
        //      ->where('client_projects.client_id', '=', $userObj->id)
        //      ->where('projects.workspace', '=', $currentWorkspace->id)->get();
        //  }
        //  else {
        // $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
        //     ->where('user_projects.user_id', '=', $userObj->id)
        //     ->where('projects.workspace', '=', $currentWorkspace->id)->get();
        //  }
        $projects = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace->id)->get();

        $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();
        $users = User::select('users.*')
            ->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
            ->where('user_workspaces.workspace_id', '=', $currentWorkspace->id)->get();


        return view('projects.tasks', compact('currentWorkspace', 'projects', 'users', 'stages'));
    }


    public function projectsTimesheet(Request $request, $slug, $project_id = 0)
    {
        $project = Project::find($project_id);
        $project_name = $project->name ?? '';
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        return view('projects.timesheet', compact('currentWorkspace', 'project_id', 'project_name'));
    }

    public function ajax_tasks($slug, Request $request)
    {
        $userObj = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        // if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member') {
        // $tasks = Task::select(
        //     [
        //         'tasks.*',
        //         'stages.name as stage',
        //         'stages.complete',
        //     ])
        // ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
        //     ->join("projects", "projects.id", "=", "user_projects.project_id")
        //     ->join("stages", "stages.id", "=", "tasks.status")
        //     ->where("user_id", "=", $userObj->id)
        //     ->where('projects.workspace', '=', $currentWorkspace->id);
        // } else {
        $tasks = Task::select(
            [
                'tasks.*',
                'stages.name as stage',
                'stages.complete',
            ]
        )
            ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
            ->join("projects", "projects.id", "=", "user_projects.project_id")
            ->join("stages", "stages.id", "=", "tasks.status")
            //   ->where("user_id", "=", $userObj->id) //Si quito esto se me duplica las tareas
            ->where('projects.workspace', '=', $currentWorkspace->id);
        // }
        if ($request->project) {
            $tasks->where('tasks.project_id', '=', $request->project);
        }
        if ($request->assign_to) {
            $tasks->whereRaw("find_in_set('" . $request->assign_to . "',assign_to)");
        }
        if ($request->due_date_order) {
            if ($request->due_date_order == 'today') {

                $tasks->whereDate('due_date', Carbon::today());
            } else if ($request->due_date_order == 'expired') {

                $tasks->whereDate('due_date', '<', Carbon::today());
            } else if ($request->due_date_order == 'in_7_days') {

                $tasks->where(['due_date' => Carbon::parse()->between(Carbon::now(), Carbon::now()->addDays(7))]);
            } else {

                $sort = explode(',', $request->due_date_order);

                $tasks->orderBy($sort[0], $sort[1]);
            }
        }
        if ($request->start_date && $request->end_date) {
            $tasks->whereBetween(
                'tasks.due_date',
                [
                    $request->start_date,
                    $request->end_date,
                ]
            );
        }
        $tasks = $tasks->with('project')->get();
        $data = [];
        foreach ($tasks as $task) {
            $tmp = [];
            $tmp['title'] = '<a href="' . route(
                'projects.task.board',
                [
                    $currentWorkspace->slug,
                    $task->project_id,
                ]
            ) . '" class="text-body">' . $task->title . '</a>';
            $tmp['project_name'] = $task->project->name;
            $tmp['milestone'] = ($milestone = $task->milestone()) ? $milestone->title : '';

            $due_date = '<span class="text-' . ($task->due_date < date('Y-m-d') ? 'danger' : 'success') . '">'
                . date('Y-m-d', strtotime($task->due_date)) . '</span> ';
            $tmp['due_date'] = $due_date;

            // if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member') {
            $tmp['user_name'] = "";
            foreach ($task->users() as $user) {
                if (isset($user) && $user) {
                    $tmp['user_name'] .= '<span class="badge bg-secondary p-2 px-3 rounded">' . $user->name . '</span> ';
                }
            }
            // }

            if ($task->complete == 1) {
                $tmp['status'] = '<span class="status_badge badge bg-success p-2 px-3 rounded">' . __($task->stage) . '</span>';
            } else {
                $tmp['status'] = '<span class="status_badge badge bg-primary p-2 px-3 rounded">' . __($task->stage) . '</span>';
            }

            if ($currentWorkspace->permission == 'Owner' ||  Auth::user()->type == 'user') {
                $tmp['action'] = '
                <a href="#" class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"  
                data-toggle="popover"  title="' . __('Edit Task')
                    . '"  data-ajax-popup="true" data-size="lg" data-title="' . __('Edit Task') . '" data-url="' . route(
                        'tasks.edit',
                        [
                            $currentWorkspace->slug,
                            $task->project_id,
                            $task->id,
                        ]
                    ) . '"><i class="ti ti-pencil"></i></a>
                <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center 
                bs-pass-para" data-toggle="popover" title="'
                    . __('Delete')
                    . '" data-confirm="' . __('Are You Sure?')
                    . '" data-confirm-yes="delete-form-' . $task->id . '">
                    <i class="ti ti-trash"></i></a>
                <form id="delete-form-' . $task->id . '" action="' . route(
                        'tasks.destroy',
                        [
                            $currentWorkspace->slug,
                            $task->project_id,
                            $task->id,
                        ]
                    ) . '" method="POST" style="display: none;">
                                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                                            <input type="hidden" name="_method" value="DELETE">
                                        </form>';
            }
            $data[] = array_values($tmp);
        }
        return response()->json(['data' => $data], 200);
    }

    public function gantt($slug, $projectID, $duration = 'Week')
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $is_client = '';

        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
            $is_client = 'client.';
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
        }
        $tasks = [];
        $permissions = Auth::user()->getPermission($projectID);

        if (
            $project && (isset($permissions) && in_array('show gantt', $permissions))
            || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
        ) {
            if ($objUser->getGuard() == 'client' || $currentWorkspace->permission == 'Owner') {
                $tasksobj = Task::where('project_id', '=', $project->id)->orderBy('start_date')->get();
            } else {
                $tasksobj = Task::where('project_id', '=', $project->id)
                    ->where('assign_to', '=', $objUser->id)->orderBy('start_date')->get();
            }
            foreach ($tasksobj as $task) {
                $tmp = [];
                $tmp['id'] = 'task_' . $task->id;
                $tmp['name'] = $task->title;
                $tmp['start'] = $task->start_date;
                $tmp['end'] = $task->due_date;
                $tmp['custom_class'] = strtolower($task->priority);
                $tmp['progress'] = $task->subTaskPercentage();
                $tmp['extra'] = [
                    'priority' => __($task->priority),
                    'comments' => count($task->comments),
                    'duration' => Date::parse($task->start_date)->format('d M Y H:i A')
                        . ' - ' . Date::parse($task->due_date)->format('d M Y H:i A'),
                ];
                $tasks[] = $tmp;
            }
        }

        return view('projects.gantt', compact('currentWorkspace', 'project', 'tasks', 'duration', 'is_client'));
    }

    public function ganttPost($slug, $projectID, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
        }
        if ($project) {
            if ($objUser->getGuard() == 'client' || $currentWorkspace->permission == 'Owner') {
                $id = trim($request->task_id, 'task_');
                $task = Task::find($id);
                $task->start_date = $request->start;
                $task->due_date = $request->end;
                $task->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'message' => __("Time Updated"),
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'message' => __("You can't change Date!"),
                    ],
                    400
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __("Something is wrong."),
                ],
                400
            );
        }
    }

    public function filterTimesheetTableView(Request $request, $slug)
    {
        $tasks = [];
        $week = $request->week;
        $project_id = $request->project_id;
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();
        $showAllWorkspaces = $request->get('all') === 'true';

        $user_id = $objUser->id;

        if ($request->has('week')) {

            if ($project_id == -1) {
                //--------------------- Los timesheets de mis proyectos  -------------------//

                $timesheets = Task::select(
                    'tasks.*',
                    'tasks.milestone_id as milestone_id',
                    'tasks.id as task_id',
                    'milestones.title as milestone_name',
                    'projects.id as project_id',
                    'projects.name as project_name',
                    'projects.ref_delegation as ref_delegation'
                )
                    ->join('timesheets', 'timesheets.task_id', '=', 'tasks.id')
                    ->join('milestones', 'tasks.milestone_id', '=', 'milestones.id')
                    ->join('projects', 'milestones.project_id', '=', 'projects.id')
                    ->where('tasks.assign_to', '=', $user_id);

                // Filtrar por workspace actual o todos los workspaces
                if (!$showAllWorkspaces) {
                    $timesheets->where('projects.workspace', '=', $currentWorkspace->id);
                }
            } else {
                //--------------------- Los timesheets de todos en un proyecto  -------------------//
                $timesheets = Task::select(
                    'tasks.*',
                    'tasks.milestone_id as milestone_id',
                    'tasks.id as task_id',
                    'milestones.title as milestone_name',
                    'projects.id as project_id',
                    'projects.name as project_name',
                    'projects.ref_delegation as ref_delegation'
                )
                    ->join('timesheets', 'timesheets.task_id', '=', 'tasks.id')
                    ->join('milestones', 'tasks.milestone_id', '=', 'milestones.id')
                    ->join('projects', 'milestones.project_id', '=', 'projects.id')
                    ->where('projects.workspace', '=', $currentWorkspace->id);
            }

            $days = Utility::getFirstSeventhWeekDay($week);
            $first_day = $days['first_day'];
            $seventh_day = $days['seventh_day'];


            $onewWeekDate = $first_day->format('Y-m-d') . ' - ' . $seventh_day->format('Y-m-d');
            $selectedDate = $first_day->format('Y-m-d') . ' - ' . $seventh_day->format('Y-m-d');

            $timesheets = $timesheets->whereDate('tasks.start_Date', '>=', $first_day->format('Y-m-d'))
                ->whereDate('tasks.estimated_date', '<=', $seventh_day->format('Y-m-d'));


            if ($project_id == '-1') {
                $timesheets = $timesheets->get()->groupBy([
                    'project_id',
                    'milestone_id',
                    'task_id'
                ])->toArray();
            } else {

                $timesheets = $timesheets->where('projects.id', $project_id)->get()->groupBy([
                    'milestone_id',
                    'user_id',
                    'task_id'
                ])->toArray();
            }
            $results = Project::getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets, $days, $project_id, false, $showAllWorkspaces);
            $returnHTML = $results['htmlContent'];         // HTML generado
            $totalrecords = $results['totalrecords']; // Total de registros
            if ($project_id != '-1') {
                $projects = Project::select('projects.*')
                    ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                    ->where('user_projects.user_id', '=', $user_id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)->get();

                $tasks = Task::where('project_id', '=', $project_id)
                    ->where('assign_to')->pluck('type_id', 'id');
            }

            return response()->json([
                'success' => true,
                'selectedDate' => $selectedDate,
                'tasks' => $tasks,
                'onewWeekDate' => $onewWeekDate,
                'days' => $days,
                'html' => $returnHTML,
                'totalrecords' => $totalrecords
            ]);
        }
    }

    public function appendTimesheetTaskHTML(Request $request, $slug)
    {
        $project_id = $request->has('project_id') ? $request->project_id : null;
        $task_id = $request->has('task_id') ? $request->task_id : null;
        $selected_dates = $request->has('selected_dates') ? $request->selected_dates : null;

        $returnHTML = '';

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project = Project::find($project_id);

        if ($project) {
            $task = Task::find($task_id);

            if ($task && $selected_dates) {
                $twoDates = explode(' - ', $selected_dates);

                $first_day = $twoDates[0];
                $seventh_day = $twoDates[1];

                $period = CarbonPeriod::create($first_day, $seventh_day);

                $typesName = TaskType::all();
                $name = $typesName[$task->type_id]->name;

                $returnHTML .= '<tr><td><span class="task-name ml-3">' . $name . '</span></td>';

                foreach ($period as $key => $dateobj) {
                    $returnHTML .= '<td><div role="button" class="form-control border-dark wid-120" data-ajax-timesheet-popup="true" 
                    data-type="create" data-task-id="' . $task->id . '" data-date="' . $dateobj->format('Y-m-d') . '" 
                    data-url="' . route(
                        'project.timesheet.create',
                        [
                            'slug' => $currentWorkspace->slug,
                            'project_id' => $project_id,
                        ]
                    ) . '">00:00</div></td>';
                }

                $returnHTML .= '<td><div  role="button"class="total form-control border-dark wid-120">00:00</div></td></tr>';
            }
        }
        return response()->json(
            [
                'success' => true,
                'html' => $returnHTML,
            ]
        );
    }

    public function projectTimesheetCreate(Request $request, $slug, $project_id)
    {
        $fromTimesheet = true;
        $parseArray = [];
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project_id = $request->input('project_id');
        $task_id = $request->input('task_id');
        $selected_date = $request->input('date');
        $user_id = $request->input('user_id');
        $project = Project::find($project_id);


        if (!$project) {
            return redirect()->back()->with('error', 'Project not found');
        }

        $project_name = $project->name;

        $task = Task::where('project_id', $project_id)
            ->where('id', $task_id)
            ->first();

        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $taskType = TaskType::where('project_type', $project->type)
            ->where('id', $task->type_id)
            ->first();

        $task_name = $taskType ? $taskType->name : 'Unknown';

        $milestone = Milestone::find($task->milestone_id);
        $milestone_name = $milestone->title ?? 'Sin hito';
        $milestone_id = $milestone->id ?? null;

        // Obtener todos los registros de Timesheet del día seleccionado
        $tasktime = Timesheet::where('created_by', $user_id)
            ->whereDate('date', $selected_date)
            ->pluck('time')
            ->toArray();

        // Si hay registros, sumarlos, de lo contrario, devolver '00:00'
        $totaltasktime = Utility::calculateTimesheetHours($tasktime);

        // Separar horas y minutos
        list($totaltaskhour, $totaltaskminute) = explode(':', $totaltasktime);
        // Obtener horario esperado según el día de la semana
        $timeTable = UserTimetable::where('user_id', $objUser->id)->first();
        $dayOfWeek = strtolower(date('l')); // Día en inglés (ej: "monday")

        $expectedHour = 0;
        if ($timeTable && isset($timeTable->$dayOfWeek)) {
            $expectedTime = explode(':', $timeTable->$dayOfWeek);
            $expectedHour = (int) $expectedTime[0]; // Solo horas
        }

        // Convertir horas trabajadas a decimal
        $workedHoursFormatted = $totaltaskhour + ($totaltaskminute / 60);
        $dayColor = '';
        // Determinar el color según la comparación
        if ($workedHoursFormatted == 0) {
            $dayColor = '#e06c71'; // Rojo (sin horas)
        } elseif ($workedHoursFormatted < $expectedHour) {
            $dayColor = '#fcf75e'; // Amarillo (horas parciales)
        } elseif ($workedHoursFormatted == $expectedHour) {
            $dayColor = '#89e186'; // Verde (horas completas)
        } elseif ($workedHoursFormatted > $expectedHour) {
            $dayColor = '#b2e2f2'; // Azul (horas extras)
        }
        $taskCreationDate = $task->created_at->format('Y-m-d');
        $parseArray = [
            'project_id' => $project->id,
            'project_name' => $project_name,
            'task_id' => $task->id,
            'task_name' => $task_name,
            'milestone_id' => $milestone_id,
            'milestone_name' => __($milestone_name),
            'date' => $selected_date,
            'totaltaskhour' => $totaltaskhour,
            'totaltaskminute' => $totaltaskminute,
            'taskCreationDate' => $taskCreationDate,
        ];

        return view('projects.timesheet-create', compact('currentWorkspace', 'parseArray', 'fromTimesheet', 'dayColor', 'timeTable'));
    }

    public function projectTimesheetStore(Request $request, $slug, $project_id)
    {
        $objUser = Auth::user();
        $project = Project::find($request->project_id);

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($project) {
            $request->validate(
                [
                    'date' => 'required',
                    'time_hour' => 'required',
                    'time_minute' => 'required',
                ]
            );

            $hour = $request->time_hour;
            $minute = $request->time_minute;

            $time = ($hour != '' ? ($hour < 10 ? '0' + $hour : $hour) : '00') . ':' . ($minute != '' ? ($minute < 10 ? '0' + $minute : $minute) : '00');

            $timesheet = new Timesheet();
            $timesheet->project_id = $request->project_id;
            $timesheet->task_id = $request->task_id;
            $timesheet->date = $request->date;
            $timesheet->time = $time;
            // $timesheet->description = $request->description;
            $timesheet->created_by = $objUser->id;
            $timesheet->save();

            return redirect()->back()->with('success', __('Timesheet Created Successfully!'));
        }
    }

    public function projectTimesheetEdit(Request $request, $slug, $timesheet_id, $project_id)
    {

        $objUser = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id);
        $task = Task::find($request->task_id);
        $milestone = Milestone::find($task->milestone_id);

        $task_id = $request->has('task_id') ? $request->task_id : null;
        $user_id = $request->has('date') ? $request->user_id : null;
        $created_by = $user_id != null ? $user_id : $objUser->id;
        $selected_date = $request->has('date') ? $request->date : null;
        $project_view = '';

        if ($request->has('project_view')) {
            $project_view = $request->project_view;
        }

        $projects = Project::select('projects.*')
            ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where(
                'user_projects.user_id',
                '=',
                $objUser->id
            )->where('projects.workspace', '=', $currentWorkspace->id);

        $timesheet = Timesheet::find($timesheet_id);

        if ($timesheet) {
            $project = $projects->where('projects.id', '=', $project_id)->pluck('projects.name', 'projects.id')->all();

            if (!empty($project) && count($project) > 0) {

                $project_id = key($project);
                $project_name = $project[$project_id];

                $task = Task::where(
                    [
                        'project_id' => $project_id,
                        'id' => $task_id,
                    ]
                )->pluck('type_id', 'id')->all();


                $task_id = key($task);
                $taskType = TaskType::select('name')->where('id', $task)->first();
                $task_name = $taskType->name;

                $tasktime = Timesheet::where('id', $timesheet_id)
                    ->where('created_by', $created_by)
                    ->whereDate('date', $selected_date)->pluck('time')->toArray();

                $totaltasktime = Utility::calculateTimesheetHours($tasktime);

                $totalhourstimes = explode(':', $totaltasktime);

                $totaltaskhour = $totalhourstimes[0];
                $totaltaskminute = $totalhourstimes[1];

                $time = explode(':', $timesheet->time);

                $timeTable = UserTimetable::where('user_id', $objUser->id)->first();
                $targetDate = $selected_date ?: ($timesheet->date ?? null);
                $expectedHour = $this->getExpectedHoursByDate($timeTable, $targetDate);

                $workedHoursFormatted = ((int) $totaltaskhour) + (((int) $totaltaskminute) / 60);
                $dayColor = $this->resolveDayColor($workedHoursFormatted, $expectedHour);
                $parseArray = [
                    'project_id' => $project_id,
                    'project_name' => $project_name,
                    'milestone_name' => $milestone->title,
                    'milestone_id' => $milestone->id,
                    'timesheet_id' => $timesheet_id,
                    'task_id' => $task_id,
                    'task_name' => $task_name,
                    'time_hour' => $time[0] < 10 ? $time[0] : $time[0],
                    'time_minute' => $time[1] < 10 ? $time[1] : $time[1],
                    'totaltaskhour' => $totaltaskhour,
                    'totaltaskminute' => $totaltaskminute,
                ];
                $user = Auth::user();
                $timesheetEdit = Timesheet::find($timesheet_id);

                return view('projects.timesheet-edit', compact('timesheet', 'currentWorkspace', 'parseArray', 'project_id', 'dayColor', 'timeTable', 'timesheetEdit', 'expectedHour'));
            }
        }
    }

    public function projectTimesheetUpdate(Request $request, $slug, $timesheet_id, $project_id)
    {
        \Log::info(['projectTimesheetUpdate' => $request->all()]);
        $project = Project::find($request->project_id);

        \Log::info(['projectTimesheetUpdate' => $project->id]);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($project) {
            $request->validate(
                [
                    'date' => 'required',
                    'time_hour' => 'required',
                    'time_minute' => 'required',
                ]
            );

            $hour = $request->time_hour;
            $minute = $request->time_minute;

            $time = ($hour != '' ? ($hour < 10 ? '0' + $hour : $hour) : '00') . ':' . ($minute != '' ? ($minute < 10 ? '0' + $minute : $minute) : '00');

            $timesheet = Timesheet::find($timesheet_id);
            $timesheet->project_id = $request->project_id;
            $timesheet->task_id = $request->task_id;
            $timesheet->date = $request->date;
            $timesheet->time = $time;
            $timesheet->save();

            return redirect()->back()->with('success', __('Timesheet Updated Successfully!'));
        }
    }
    public function members($slug, $id)
    {

        $project = Project::with('users')->find($id);
        $members = $project->users;
        $data = [];
        foreach ($members as $key => $member) {
            $data[$key]['id'] = $member->id;
            $data[$key]['name'] = $member->name;
        }
        return $data;
    }


    public function copyproject($slug, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')
            ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $id)->first();

        return view('projects.copy', compact('currentWorkspace', 'project'));
    }

    public function copyprojectstore(Request $request, $slug, $id)
    {
        $project                          = Project::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $duplicate                          = new Project();
        $duplicate['name']                  = $project->name;
        $duplicate['status']                = $project->status;
        $duplicate['description']           = $project->description;
        $duplicate['start_date']            = $project->start_date;
        $duplicate['end_date']              = $project->end_date;
        $duplicate['budget']                = $project->budget;
        $duplicate['workspace']             = $currentWorkspace->id;
        $duplicate['created_by']            = $project->created_by;
        $duplicate['is_active']             = $project->is_active;
        $duplicate->save();


        if (isset($request->user) && in_array("user", $request->user)) {
            $users = UserProject::where('project_id', $project->id)->get();
            foreach ($users as $user) {
                $users = new UserProject();
                $users['user_id'] = $user->user_id;
                $users['project_id'] = $duplicate->id;
                $users['is_active'] = $user->is_active;
                $users['permission'] = $user->permission;
                $users->save();
            }
        } else {
            $objUser = Auth::user();
            $users              = new UserProject();
            $users['user_id']   = $objUser->id;
            $users['project_id'] = $duplicate->id;
            $users->save();
        }

        if (isset($request->client) && in_array("client", $request->client)) {
            $clients = ClientProject::where('project_id', $project->id)->get();
            foreach ($clients as $client) {
                $clients = new ClientProject();
                $clients['client_id']   = $client->client_id;
                $clients['project_id']  = $duplicate->id;
                $clients['is_active']   = $client->is_active;
                $clients['permission']  = $client->permission;
                $clients->save();
            }
        }

        if (isset($request->task) && in_array("task", $request->task)) {
            $tasks = Task::where('project_id', $project->id)->get();
            foreach ($tasks as $task) {
                $project_task                   = new Task();
                $project_task['title']          = $task->title;
                $project_task['priority']       = $task->priority;
                $project_task['description']    = $task->description;
                $project_task['start_date']     = $task->start_date;
                $project_task['due_date']       = $task->due_date;
                $project_task['assign_to']      = $task->assign_to;
                $project_task['project_id']     = $duplicate->id;
                $project_task['milestone_id']   = $task->milestone_id;
                $project_task['status']         = $task->status;
                $project_task['order']          = $task->order;
                $project_task->save();

                if (in_array("sub_task", $request->task)) {
                    $sub_tasks = SubTask::where('task_id', $task->id)->get();
                    foreach ($sub_tasks as $sub_task) {
                        $subtask                = new SubTask();
                        $subtask['name']        = $sub_task->name;
                        $subtask['due_date']    = $sub_task->due_date;
                        $subtask['task_id']     = $project_task->id;
                        $subtask['user_type']   = $sub_task->user_type;
                        $subtask['created_by']  = $sub_task->created_by;
                        $subtask['status']      = $sub_task->status;
                        $subtask->save();
                    }
                }
                if (in_array("task_comment", $request->task)) {
                    $task_comments = Comment::where('task_id', $task->id)->get();
                    foreach ($task_comments as $task_comment) {
                        $comment                = new Comment();
                        $comment['comment']     = $task_comment->comment;
                        $comment['created_by']  = $task_comment->created_by;
                        $comment['task_id']     = $project_task->id;
                        $comment['user_type']   = $task_comment->user_type;
                        $comment->save();
                    }
                }
                if (in_array("task_files", $request->task)) {
                    $task_files = TaskFile::where('task_id', $task->id)->get();
                    foreach ($task_files as $task_file) {
                        $file               = new TaskFile();
                        $file['file']       = $task_file->file;
                        $file['name']       = $task_file->name;
                        $file['extension']  = $task_file->extension;
                        $file['file_size']  = $task_file->file_size;
                        $file['created_by'] = $task_file->created_by;
                        $file['task_id']    = $project_task->id;
                        $file['user_type']  = $task_file->user_type;
                        $file->save();
                    }
                }
            }
        }

        if (isset($request->bug) && in_array("bug", $request->bug)) {
            $bugs = BugReport::where('project_id', $project->id)->get();
            foreach ($bugs as $bug) {
                $project_bug                   = new BugReport();
                $project_bug['title']          = $bug->title;
                $project_bug['priority']       = $bug->priority;
                $project_bug['description']    = $bug->description;
                $project_bug['assign_to']      = $bug->assign_to;
                $project_bug['project_id']     = $duplicate->id;
                $project_bug['status']         = $bug->status;
                $project_bug['order']          = $bug->order;
                $project_bug->save();

                if (in_array("bug_comment", $request->bug)) {
                    $bug_comments = BugComment::where('bug_id', $bug->id)->get();
                    foreach ($bug_comments as $bug_comment) {
                        $bugcomment                 = new BugComment();
                        $bugcomment['comment']      = $bug_comment->comment;
                        $bugcomment['created_by']   = $bug_comment->created_by;
                        $bugcomment['bug_id']       = $project_bug->id;
                        $bugcomment['user_type']    = $bug_comment->user_type;
                        $bugcomment->save();
                    }
                }
                if (in_array("bug_files", $request->bug)) {
                    $bug_files = BugFile::where('bug_id', $bug->id)->get();
                    foreach ($bug_files as $bug_file) {
                        $bugfile               = new BugFile();
                        $bugfile['file']       = $bug_file->file;
                        $bugfile['name']       = $bug_file->name;
                        $bugfile['extension']  = $bug_file->extension;
                        $bugfile['file_size']  = $bug_file->file_size;
                        $bugfile['created_by'] = $bug_file->created_by;
                        $bugfile['bug_id']     = $project_bug->id;
                        $bugfile['user_type']  = $bug_file->user_type;
                        $bugfile->save();
                    }
                }
            }
        }
        if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
            $milestones = Milestone::where('project_id', $project->id)->get();
            foreach ($milestones as $milestone) {
                $post                   = new Milestone();
                $post['project_id']     = $duplicate->id;
                $post['title']          = $milestone->title;
                $post['status']         = $milestone->status;
                $post['cost']           = $milestone->cost;
                $post['summary']        = $milestone->summary;
                $post['progress']       = $milestone->progress;
                $post['start_date']     = $milestone->start_date;
                $post['end_date']       = $milestone->end_date;
                $post->save();
            }
        }
        if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
            $project_files = ProjectFile::where('project_id', $project->id)->get();
            foreach ($project_files as $project_file) {
                $ProjectFile                = new ProjectFile();
                $ProjectFile['project_id']  = $duplicate->id;
                $ProjectFile['file_name']   = $project_file->file_name;
                $ProjectFile['file_path']   = $project_file->file_path;
                $ProjectFile->save();
            }
        }
        if (isset($request->activity) && in_array('activity', $request->activity)) {
            $where_in_array = [];
            if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
                array_push($where_in_array, "Create Milestone");
            }
            if (isset($request->task) && in_array("task", $request->task)) {
                array_push($where_in_array, "Create Task", "Move");
            }
            if (isset($request->bug) && in_array("bug", $request->bug)) {
                array_push($where_in_array, "Create Bug", "Move Bug");
            }
            if (isset($request->client) && in_array("client", $request->client)) {
                array_push($where_in_array, "Share with Client");
            }
            if (isset($request->user) && in_array("user", $request->user)) {
                array_push($where_in_array, "Invite User");
            }
            if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
                array_push($where_in_array, "Upload File");
            }
            if (count($where_in_array) > 0) {
                $activities = ActivityLog::where('project_id', $project->id)->whereIn('log_type', $where_in_array)->get();
                foreach ($activities as $activity) {
                    $activitylog                = new ActivityLog();
                    $activitylog['user_id']     = $activity->user_id;
                    $activitylog['user_type']   = $activity->user_type;
                    $activitylog['project_id']  = $duplicate->id;
                    $activitylog['log_type']    = $activity->log_type;
                    $activitylog['remark']      = $activity->remark;
                    $activitylog->save();
                }
            }
        }
        return redirect()->back()->with('success', 'Project Created Successfully');
    }


    // Project copy links functions


    // public function projectPassCheck(Request $request, $slug  , $id )
    // {


    //     $id=\Illuminate\Support\Facades\Crypt::decrypt($id);
    //     $project = Project::find($id);
    //     if ( ($request->password == base64_decode($project->password))) {
    //         $ps_status = base64_encode('true');
    //         return redirect()->route('projects.link',[$id,$ps_status,$slug]);
    //     }
    //     else{
    //         $ps_status = base64_encode('false');
    //         return redirect()->route('projects.link',[$id,$ps_status,$slug ]);
    //     }
    // }

    public function projectlink(Request $request, $slug, $projectID, $lang = '')
    {
        $setting = Utility::getAdminPaymentSettings();
        $projectID = \Illuminate\Support\Facades\Crypt::decrypt($projectID);
        $project = Project::find($projectID);

        if (Auth::user() != null) {
            $objUser         = Auth::user();
        } else {
            $objUser         = User::where('id', $project->created_by)->first();
        }
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $lang = !empty($lang) ? $lang : (!empty($currentWorkspace->lang) ? $currentWorkspace->lang : $setting['default_lang']);
        \App::setLocale($lang);


        $data = [];
        $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
        $data['member']  = isset($request->member) ? 'on' : 'off';
        $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
        $data['activity']  = isset($request->activity) ? 'on' : 'off';
        $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
        $data['task']  = isset($request->task) ? 'on' : 'off';
        $data['tracker_details']  = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';




        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')
                ->join('client_projects', 'projects.id', '=', 'client_projects.project_id')
                ->where('client_projects.client_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where('projects.id', '=', $projectID)->first();
        }

        if ($project) {

            //chartdata
            $chartData = $this->getProjectChart(
                [
                    'workspace_id' => $currentWorkspace->id,
                    'project_id' => $projectID,
                    'duration' => 'week',
                ]
            );
            if (date('Y-m-d') == $project->end_date || date('Y-m-d') >= $project->end_date) {
                $daysleft = 0;
            } else {
                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);
            }


            //treckers
            $treckers = TimeTracker::where('project_id', $projectID)->get();


            //taskboard

            $stages_task = $statusClass_task = [];

            // $permissions = $objUser->getPermission($projectID);

            if (
                isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'
                || isset($currentWorkspace) && $currentWorkspace->permission == 'Member'
            ) {
                $stages_task = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                foreach ($stages_task as &$status) {
                    $statusClass_task[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $task = Task::where('project_id', '=', $projectID);
                    // if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    //     if (isset($objUser) && $objUser) {
                    //         $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                    //     }
                    // }
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
            }


            //Bug Report
            $stages_bug = $statusClass_bug = [];
            $permissions = $objUser->getPermission($projectID);

            if (
                $project && (isset($permissions) && in_array('show bug report', $permissions))
                || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
            ) {
                $stages_bug = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                foreach ($stages_bug as &$status) {
                    $statusClass_bug[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $bug = BugReport::where('project_id', '=', $projectID);
                    // if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    //     if (isset($objUser) && $objUser) {
                    //         $bug->where('assign_to', '=', $objUser->id);
                    //     }
                    // }
                    $bug->orderBy('order');

                    $status['bugs'] = $bug->where('status', '=', $status->id)->get();
                }
            }
            $data = $request->session()->all();
            if (\Session::get('copy_pass_true' . $projectID) == $project->password . '-' . $projectID) {

                return view('projects.copylink', compact(
                    'currentWorkspace',
                    'data',
                    'project',
                    'chartData',
                    'daysleft',
                    'treckers',
                    'stages_task',
                    'stages_bug',
                    'statusClass_task',
                    'statusClass_bug',
                    'lang'
                ));
            } else {

                if (
                    isset(json_decode($project->copylinksetting)->password_protected)
                    && json_decode($project->copylinksetting)->password_protected == 'off'
                ) {
                    return view('projects.copylink', compact(
                        'currentWorkspace',
                        'data',
                        'project',
                        'chartData',
                        'daysleft',
                        'treckers',
                        'stages_task',
                        'stages_bug',
                        'statusClass_task',
                        'statusClass_bug',
                        'lang'
                    ));
                } elseif (
                    isset(json_decode($project->copylinksetting)->password_protected)
                    && json_decode($project->copylinksetting)->password_protected == 'on'
                    && $request->password == base64_decode($project->password)
                ) {

                    \Session::put('copy_pass_true' . $projectID, $project->password . '-' . $projectID);

                    return view('projects.copylink', compact(
                        'currentWorkspace',
                        'data',
                        'project',
                        'chartData',
                        'daysleft',
                        'treckers',
                        'stages_task',
                        'stages_bug',
                        'statusClass_task',
                        'statusClass_bug',
                        'lang'
                    ));
                } else {
                    return view('projects.copylink_password', compact('projectID', 'slug', 'currentWorkspace'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function copylink_setting_create($slug, $projectID)
    {
        $project = Project::find($projectID);
        if (Auth::user() != null) {
            $objUser         = Auth::user();
        } else {
            $objUser         = User::where('id', $project->created_by)->first();
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')
            ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)->first();

        $result = json_decode($project->copylinksetting);


        return view('projects.copylink_setting', compact('currentWorkspace', 'project', 'projectID', 'slug', 'result'));
    }

    public function copylinksetting(Request $request, $id, $slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();
        // $id = Crypt::decryptString($id);

        $data = [];
        $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
        $data['member']  = isset($request->member) ? 'on' : 'off';
        $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
        $data['client']  = isset($request->client) ? 'on' : 'off';
        $data['progress']  = isset($request->progress) ? 'on' : 'off';
        $data['activity']  = isset($request->activity) ? 'on' : 'off';
        $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
        $data['task']  = isset($request->task) ? 'on' : 'off';
        $data['tracker_details']  = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';

        $project = Project::select('projects.*')
            ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
            ->where('user_projects.user_id', '=', $objUser->id)
            ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $id)->first();


        if (isset($request->password_protected) && $request->password_protected == 'on') {
            $project->password = base64_encode($request->password);
        } else {
            $project->password = null;
        }


        $project->copylinksetting = (count($data) > 0) ? json_encode($data) : null;
        $project->save();
        return redirect()->back()->with('success', __('Copy Link Setting Save Successfully!'));
    }
}
