<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;

class AdminNotificationController extends Controller
{
    public function read($id)
    {
        $notif = AdminNotification::findOrFail($id);

        $notif->update([
            'is_read' => 1
        ]);

        $url = $notif->url
            ? preg_replace('/[\r\n]+/', '', trim($notif->url))
            : '/admin';

        return redirect($url);
    }

    public function destroy($id)
    {
        $notif = AdminNotification::findOrFail($id);

        $notif->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
