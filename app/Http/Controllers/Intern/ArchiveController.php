<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\ArchiveController as BaseArchiveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends BaseArchiveController
{
    /**
     * Override the getViewPath method to use intern views
     */
    protected function getViewPath(string $viewName): string
    {
        return 'intern.' . $viewName;
    }

    /**
     * Override the canCreateArchive method for intern
     */
    protected function canCreateArchive(): bool
    {
        $user = Auth::user();
        return $user->roles->contains('name', 'intern');
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

    /**
     * Override parentArchives method to use intern view path
     */
    public function parentArchives(Request $request)
    {
        $query = \App\Models\Archive::with(['category', 'classification', 'createdByUser', 'updatedByUser'])
            ->where('is_parent', true)
            ->orderBy('kurun_waktu_start', 'desc');

        // Apply intern-specific filtering
        $user = Auth::user();
        if ($user->roles->contains('name', 'intern')) {
            $internUserId = Auth::id();
            $staffUserIds = \App\Models\User::role('staff')->pluck('id')->toArray();
            $allowedUserIds = array_merge([$internUserId], $staffUserIds);

            $query->whereIn('created_by', $allowedUserIds);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                    ->orWhere('index_number', 'like', "%{$searchTerm}%")
                    ->orWhere('lampiran_surat', 'like', "%{$searchTerm}%")
                    ->orWhereHas('category', function ($q) use ($searchTerm) {
                        $q->where('nama_kategori', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('classification', function ($q) use ($searchTerm) {
                        $q->where('nama_klasifikasi', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('nama_kategori', $request->category_filter);
            });
        }

        $archives = $query->paginate(25);

        $title = 'Arsip Induk (Per Masalah)';
        $showAddButton = $this->canCreateArchive();
        $showActionButtons = true; // Show action buttons for parent archives

        return view($this->getViewPath('archives.parent-archives'), compact('archives', 'title', 'showAddButton', 'showActionButtons'));
    }
}
