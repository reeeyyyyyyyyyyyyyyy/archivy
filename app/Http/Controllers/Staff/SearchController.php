<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\SearchController as BaseSearchController;
use Illuminate\Http\Request;

class SearchController extends BaseSearchController
{
    /**
     * Override the getFilterUsers method for staff
     */
    protected function getFilterUsers()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->hasRole('staff')) {
            // Staff can only see staff and intern users
            return \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        } elseif ($user->hasRole('intern')) {
            // Intern can only see staff and intern users
            return \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        }

        return \App\Models\User::orderBy('name')->get();
    }
}
