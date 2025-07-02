<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Tampilkan daftar log aktivitas.
     */
    public function index(Request $request)
    {
        // Filter (optional): by user, action, module, date
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(15);
        $users = User::pluck('name', 'id'); // Buat filter user di view

        return view('admin.logs.index', compact('logs', 'users'));
    }
}
