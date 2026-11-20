<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

// Avisos de la campana: abrir uno (queda leído y lleva a su encargo) y marcar todos como leídos.
// Borrar uno o todos sigue en UserController (delete_notification / delete_all_notification).
class NotificationController extends Controller
{
    public function open($id)
    {
        $notification = Notification::where('id', (int) $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return redirect()->back()->with('error', __('Notification not found!'));
        }

        // Se marcan como leídos también los repetidos que la campana pinta juntos
        $ids = collect(explode(',', (string) request('ids')))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->push($notification->id)
            ->unique();
        Notification::where('user_id', Auth::id())->whereIn('id', $ids)->update(['is_read' => 1]);

        $url = $notification->targetUrl();
        if (!$url) {
            return redirect()->back()->with('error', __('The order form of this notification no longer exists.'));
        }

        return redirect()->to($url);
    }

    public function readAll()
    {
        Notification::where('user_id', Auth::id())->where('is_read', 0)->update(['is_read' => 1]);

        return response()->json(['is_success' => true]);
    }
}
