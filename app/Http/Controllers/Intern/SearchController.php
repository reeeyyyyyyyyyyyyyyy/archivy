<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\SearchController as BaseSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends BaseSearchController
{
    /**
     * Override the getViewPath method to use intern views
     */
    protected function getViewPath(string $viewName): string
    {
        return 'intern.' . $viewName;
    }

    /**
     * Override the getFilterUsers method for intern
     */
    protected function getFilterUsers()
    {
        $user = Auth::user();

        if ($user->roles->contains('name', 'intern')) {
            // Intern can only see staff and intern users
            return \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['staff', 'intern']);
            })->orderBy('name')->get();
        }

        return \App\Models\User::orderBy('name')->get();
    }
}
