<?php
namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $module, $description)
    {
        ActivityLog::create([
            'user_id'    => Auth::id() ?? 0,
            'action'     => $action,
            'module'     => $module,
            'description'=> $description,
        ]);
    }
}
