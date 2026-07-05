<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    // ======================
    // LIST NOTIFIKASI
    // ======================
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    // ======================
    // MARK AS READ + REDIRECT
    // ======================
    public function read($id)
{
    $notif = Notification::findOrFail($id);

    if ($notif->user_id != auth()->id()) {
        abort(403);
    }

    $notif->update([
        'is_read' => 1
    ]);

    $url = $notif->url
        ? preg_replace('/[\r\n]+/', '', trim($notif->url))
        : null;

    return $url
        ? redirect()->to($url)
        : back();
}

    // ======================
    // HAPUS NOTIFIKASI
    // ======================
    public function delete($id)
    {
        $notif = Notification::findOrFail($id);

        if ($notif->user_id != auth()->id()) {
            abort(403);
        }

        $notif->delete();

        return response()->json([
        'success' => true
    ]);
    }
}
