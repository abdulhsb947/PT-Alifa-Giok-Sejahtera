<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Notification;
use App\Models\Order;
use App\Services\GenerateSignedPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    public function upload(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
            'admin_notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $path = $request->file('file')->store('agreements', 'public');
        $oldPath = null;

        DB::beginTransaction();

        try {
            $agreement = Agreement::where('order_id', $id)->lockForUpdate()->first();
            $version = $agreement ? ((int) $agreement->version + 1) : 1;

            $payload = [
                'order_id' => $id,
                'file' => $path,
                'version' => $version,
                'admin_notes' => $request->admin_notes,
                'status' => 'menunggu_persetujuan_pelanggan',
                'customer_notes' => null,
                'customer_signed_at' => null,
                'signature_file' => null,
                'final_file' => null,
                'signature_page' => 1,
                'signature_x' => null,
                'signature_y' => null,
                'signature_width' => 150,
                'signature_height' => 70,
                'admin_signed_at' => now(),
            ];

            if ($agreement) {
                $oldPath = $agreement->file;

                $agreement->update($payload);
            } else {
                $agreement = Agreement::create($payload);
            }

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Perjanjian Baru',
                'message' => 'Admin telah mengunggah perjanjian versi ' .
                    $version .
                    ' untuk pesanan ' .
                    $order->order_code .
                    '. Silakan tinjau dan berikan persetujuan.',
                'type' => 'agreement',
                'is_read' => 0,
                'url' => url('/customer/orders/' . $order->id),
            ]);

            $order->update([
                'status' => 'menunggu_persetujuan_pelanggan',
            ]);

            DB::commit();

            if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            return back()->with('error', 'Perjanjian gagal disimpan: ' . $e->getMessage());
        }

        return back()->with('success', 'Perjanjian berhasil dikirim.');
    }

    public function downloadFinal($id)
    {
        $agreement = Agreement::where('order_id', $id)->firstOrFail();

        if (!$agreement->final_file) {
            abort(404);
        }

        return response()->download(storage_path('app/public/' . $agreement->final_file));
    }

    public function placeSignature($id)
    {
        $agreement = Agreement::findOrFail($id);

        if (!$agreement->signature_file) {
            return back()->with('error', 'Customer belum melakukan tanda tangan.');
        }

        return view('admin.agreements.place-signature', compact('agreement'));
    }

    public function saveSignature(Request $request, $id)
{
    
    $agreement = Agreement::findOrFail($id);

    $request->validate([
        'page'   => 'required|integer|min:1',
        'x'      => 'required|numeric',
        'y'      => 'required|numeric',
        'width'  => 'required|numeric|min:1',
        'height' => 'required|numeric|min:1',
    ]);

    $agreement->update([
        'signature_page'   => $request->page,
        'signature_x'      => $request->x,
        'signature_y'      => $request->y,
        'signature_width'  => $request->width,
        'signature_height' => $request->height,
    ]);

    return response()->json([
        'success' => true
    ]);
}

    public function generateFinalPdf(
    Request $request,
    $agreement,
    GenerateSignedPdfService $pdfService
)
{
    $agreement = Agreement::with('order')->findOrFail($agreement);

    try {
        if ($request->filled(['page', 'x', 'y', 'width', 'height'])) {
            $validated = $request->validate([
                'page'   => 'required|integer|min:1',
                'x'      => 'required|numeric',
                'y'      => 'required|numeric',
                'width'  => 'required|numeric|min:1',
                'height' => 'required|numeric|min:1',
            ]);

            $agreement->update([
                'signature_page'   => $validated['page'],
                'signature_x'      => $validated['x'],
                'signature_y'      => $validated['y'],
                'signature_width'  => $validated['width'],
                'signature_height' => $validated['height'],
            ]);

            $agreement->refresh();
        }

        $finalPath = $pdfService->generate($agreement);

        $agreement->update([
            'status' => 'perjanjian_disetujui',
        ]);

        $agreement->order->update([
            'status' => 'perjanjian_disetujui',
        ]);

        return redirect('/admin/orders/' . $agreement->order_id)
            ->with('success', 'PDF final berhasil dibuat.');

    } catch (\Throwable $e) {

        dd(
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

    }
}
}
