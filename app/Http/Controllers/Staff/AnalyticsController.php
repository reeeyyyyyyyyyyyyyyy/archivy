<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Admin\AnalyticsController as BaseAnalyticsController;
use Illuminate\Http\Request;

class AnalyticsController extends BaseAnalyticsController
{
    /**
     * Override to add staff-specific filtering
     */
    protected function getBaseQuery()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $query = \App\Models\Archive::query();

        if ($user->hasRole('staff')) {
            // Staff can only see their own archives and intern archives
            $staffUserId = \Illuminate\Support\Facades\Auth::id();
            $internUserIds = \App\Models\User::role('intern')->pluck('id')->toArray();
            $allowedUserIds = array_merge([$staffUserId], $internUserIds);

            $query->whereIn('created_by', $allowedUserIds);
        }

        return $query;
    }
}
