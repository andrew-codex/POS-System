<?php
namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public function logActivity($action, $details = null)
    {
        ActivityLog::create([
            'action'       => $action,
            'details'      => is_array($details) ? json_encode($details) : $details,
            'user_id'      => auth()->id(),
        ]);
    }
}
