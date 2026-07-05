<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Agreement;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Storage;


class AgreementController extends Controller
{


    public function approveAgreement(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required'
        ]);

        $agreement = Agreement::where(
            'order_id',
            $id
        )->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Simpan Signature PNG
    |--------------------------------------------------------------------------
    */

        $image = str_replace(
            'data:image/png;base64,',
            '',
            $request->signature
        );

        $image = str_replace(
            ' ',
            '+',
            $image
        );

        $fileName = 'sign_' . time() . '.png';

        $signaturePath =
            'signatures/' . $fileName;

        Storage::disk('public')->put(
            $signaturePath,
            base64_decode($image)
        );

        /*
    |--------------------------------------------------------------------------
    | Update Agreement
    |--------------------------------------------------------------------------
    */

        $agreement->update([

            'signature_file' => $signaturePath,

            'customer_signed_at' => now(),

            'customer_notes' => $request->customer_notes,

            // PDF final belum dibuat
            'final_file' => null,

            // Menunggu Admin menentukan posisi
            'status' => 'menunggu_penempatan_ttd'

        ]);

        Order::where('id', $id)->update([
            'status' => 'menunggu_penempatan_ttd'
        ]);

        AdminNotification::create([
            'title' => 'Tanda Tangan Customer',

            'message' =>
            'Customer telah menandatangani perjanjian #' .
                $id .
                '. Silakan atur posisi tanda tangan dan generate PDF final.',
            'url' => '/admin/orders/' . $id,
            'is_read' => 0
        ]);

        return back()->with(
            'success',
            'Perjanjian berhasil ditandatangani'
        );
    }

    public function rejectAgreement(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required'
        ]);

        $agreement = Agreement::where('order_id', $id)
            ->firstOrFail();

        $agreement->update([
            'customer_notes' => $request->notes,
            'status' => 'perjanjian_ditolak'
        ]);

        Order::where('id', $id)->update([
            'status' => 'perjanjian_ditolak'
        ]);

        AdminNotification::create([
            'title' => 'Perjanjian Ditolak',
            'message' => 'Customer menolak perjanjian untuk order #' . $id,
            'url' => '/admin/orders/' . $id,
            'is_read' => 0
        ]);

        return back()->with(
            'success',
            'Perjanjian berhasil ditolak'
        );
    }

    
}
