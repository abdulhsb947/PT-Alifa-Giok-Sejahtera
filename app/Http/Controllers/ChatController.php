<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Notification;
use App\Models\AdminNotification;

class ChatController extends Controller
{
    public function send(Request $request, $orderId)
    {
        $request->validate([
            'message' => 'required'
        ]);

        try {

            $order = Order::findOrFail($orderId);

            DB::table('messages')->insert([
                'order_id'   => (int) $orderId,
                'sender'     => auth()->user()->role,
                'message'    => $request->message,
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            /*
            |--------------------------------------------------------------------------
            | NOTIFIKASI
            |--------------------------------------------------------------------------
            */

            if (auth()->user()->role == 'customer') {

                AdminNotification::create([
                    'title'   => 'Pesan Baru',
                    'message' => auth()->user()->name .
                        ' mengirim pesan pada order ' .
                        $order->order_code,
                    'url'     => '/admin/orders/' . $orderId,
                    'is_read' => 0
                ]);

            } else {

                Notification::create([
                    'user_id' => $order->user_id,
                    'title'   => 'Pesan Baru',
                    'message' => 'Admin mengirim pesan pada order ' .
                        $order->order_code,
                    'type'    => 'chat',
                    'is_read' => 0,
                    'url'     => url('/customer/orders/' . $orderId)
                ]);
            }

            return back()->with(
                'success',
                'Pesan berhasil dikirim'
            );

        } catch (\Exception $e) {

            dd($e->getMessage());
        }
    }
}