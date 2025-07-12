<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('module')) {
            $query->where('module', 'like', '%' . $request->module . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::pluck('nama', 'id');
        return view('kepala.logs.index', compact('logs', 'users'));
    }
} 