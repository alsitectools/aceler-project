<?php

namespace App\Http\Controllers;

use App\Models\ProblemReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * «Reportar un problema»: cualquier usuario reporta un fallo. Los usuarios con users.is_support
 * los ven en su perfil (sección «Informes de errores») y una banderita en su foto de perfil les
 * avisa de los nuevos. No se usan las notificaciones de la campana.
 */
class ProblemReportController extends Controller
{
    private const SCREENSHOT_DIR = 'problem_reports';

    public function store(Request $request)
    {
        $data = $request->validate([
            // Por ahora solo fallos; la columna admite otros tipos para más adelante
            'type' => 'nullable|in:problem',
            'description' => 'required|string|min:5|max:2000',
            'expected' => 'nullable|string|max:2000',
            'page_url' => 'nullable|string|max:500',
            'page_title' => 'nullable|string|max:255',
            'viewport' => 'nullable|string|max:30',
            'js_errors' => 'nullable|string|max:4000',
            'screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = Auth::user();

        $report = ProblemReport::create([
            'user_id' => $user->id,
            'workspace_id' => $user->currant_workspace ?: null,
            'type' => 'problem',
            'description' => trim($data['description']),
            'expected' => !empty($data['expected']) ? trim($data['expected']) : null,
            'page_url' => $data['page_url'] ?? null,
            'page_title' => $data['page_title'] ?? null,
            'user_agent' => Str::limit((string) $request->userAgent(), 495, ''),
            'viewport' => $data['viewport'] ?? null,
            'js_errors' => $data['js_errors'] ?? null,
            'status' => 'received',
        ]);

        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $path = $file->storeAs(self::SCREENSHOT_DIR, $report->id . '.' . $file->extension(), 'local');
            $report->update(['screenshot_path' => $path]);
        }

        $this->mailSupport($report, $user);

        return response()->json([
            'success' => true,
            'message' => __('Thanks! We have received the problem and will look into it.'),
        ]);
    }

    /** Lo que ha enviado el usuario, con su estado (formulario → «Mis reportes») */
    public function mine()
    {
        $reports = ProblemReport::where('user_id', Auth::id())
            ->orderByDesc('id')->limit(20)->get()
            ->map(fn(ProblemReport $r) => [
                'id' => $r->id,
                'date' => $r->created_at->format('d/m/Y'),
                'description' => Str::limit($r->description, 140),
                'status' => $r->status,
                'status_label' => $r->statusLabel(),
                'admin_note' => $r->admin_note,
            ]);

        return response()->json(['reports' => $reports]);
    }

    /** Los informes están en el perfil (sección «Informes de errores») */
    public function index()
    {
        $this->authorizeSupport();

        return redirect()->to(route('users.my.account') . '#reports');
    }

    /** Cambiar el estado y/o la respuesta (solo is_support); quien lo envió lo ve en «Mis reportes» */
    public function update(Request $request, $id)
    {
        $this->authorizeSupport();

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', ProblemReport::STATUSES),
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $report = ProblemReport::findOrFail((int) $id);
        $statusChanged = $report->status !== $data['status'];

        $report->fill([
            'status' => $data['status'],
            'admin_note' => isset($data['admin_note']) && trim($data['admin_note']) !== '' ? trim($data['admin_note']) : null,
            'handled_by' => Auth::id(),
        ]);
        if ($statusChanged) {
            $report->status_changed_at = now();
        }
        $report->save();

        return response()->json([
            'success' => true,
            'status' => $report->status,
            'status_label' => $report->statusLabel(),
            'new_count' => ProblemReport::where('status', 'received')->count(),
        ]);
    }

    /** Captura adjunta: la ve soporte o quien la envió */
    public function screenshot($id)
    {
        $report = ProblemReport::findOrFail((int) $id);
        $user = Auth::user();
        if (!$user->is_support && (int) $report->user_id !== (int) $user->id) {
            abort(403);
        }
        if (!$report->screenshot_path || !Storage::disk('local')->exists($report->screenshot_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($report->screenshot_path), [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    private function authorizeSupport(): void
    {
        if (!Auth::user() || !Auth::user()->is_support) {
            abort(403, __('Only the support team can see the reports.'));
        }
    }

    /** Correo a soporte, solo si está activado (config/problem_reports.php, PROBLEM_REPORT_MAIL) */
    private function mailSupport(ProblemReport $report, $sender): void
    {
        if (!config('problem_reports.mail')) {
            return;
        }

        $emails = User::where('is_support', 1)->pluck('email')->filter()->all();
        if (!$emails) {
            return;
        }

        try {
            $body = __('Problem') . " #{$report->id}\n\n"
                . "{$sender->name} <{$sender->email}>\n"
                . ($report->page_url ? __('Page') . ": {$report->page_url}\n" : '')
                . "\n{$report->description}\n"
                . ($report->expected ? "\n" . __('What did you expect to happen?') . "\n{$report->expected}\n" : '')
                . "\n" . route('users.my.account') . '#reports';

            Mail::raw($body, function ($message) use ($emails, $report) {
                $message->to($emails)->subject('[aCeler] ' . __('Problem') . " #{$report->id}");
            });
        } catch (\Throwable $e) {
            // El reporte ya está guardado y se ve en el perfil: un fallo de correo no lo pierde
            Log::warning('Reportar un problema: no se pudo enviar el correo', ['report' => $report->id, 'error' => $e->getMessage()]);
        }
    }
}
