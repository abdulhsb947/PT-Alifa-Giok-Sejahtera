<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('product')
            ->latest()
            ->get();

        // statistik
        $inProgress = $maintenances->where('status', 'proses')->count();
        $completed = $maintenances->where('status', 'selesai')->count();

        return view('direktur.maintenance', compact('maintenances', 'inProgress', 'completed'));
    }
}
