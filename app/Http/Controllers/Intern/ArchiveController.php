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

        // Show all parent archives from all roles for intern learning
        // No filtering by role - intern can see all parent archives

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

        /**
     * Preview export for intern (no actual export)
     */
    public function export(Request $request)
    {
        // For intern, just show preview without actual export
        $status = $request->input('status', 'all');
        $yearFrom = $request->input('year_from');
        $yearTo = $request->input('year_to');

        // Get user's archives for preview with relationships
        $query = \App\Models\Archive::with(['category', 'classification', 'createdByUser'])
            ->where('created_by', Auth::id());

        if ($status && $status !== 'all') {
            $query->where('status', ucfirst($status));
        }

        if ($yearFrom) {
            $query->whereYear('kurun_waktu_start', '>=', $yearFrom);
        }

        if ($yearTo) {
            $query->whereYear('kurun_waktu_start', '<=', $yearTo);
        }

        $archives = $query->orderBy('created_at', 'desc')->get();

        // Get status title
        $statusTitle = match($status) {
            'aktif', 'Aktif' => 'Aktif',
            'inaktif', 'Inaktif' => 'Inaktif',
            'permanen', 'Permanen' => 'Permanen',
            'musnah', 'Musnah' => 'Usul Musnah',
            'all', null, '' => 'Semua Status',
            default => 'Semua Status'
        };

        return view($this->getViewPath('archives.export-preview'), compact('archives', 'status', 'statusTitle', 'yearFrom', 'yearTo'));
    }

    /**
     * Show export form for intern
     */
    public function exportForm($status = 'all')
    {
        $statusTitle = match($status) {
            'all' => 'Semua Status',
            'aktif' => 'Arsip Aktif',
            'inaktif' => 'Arsip Inaktif',
            'permanen' => 'Arsip Permanen',
            'musnah' => 'Arsip Musnah',
            default => 'Semua Status'
        };

        // Get user's archives count for preview
        $query = \App\Models\Archive::where('created_by', Auth::id());
        if ($status && $status !== 'all') {
            $query->where('status', ucfirst($status));
        }
        $totalRecords = $query->count();

        return view($this->getViewPath('archives.export'), compact('status', 'statusTitle', 'totalRecords'));
    }

    /**
     * Show export menu for intern
     */
    public function exportMenu()
    {
        $statuses = [
            'all' => 'Semua Status',
            'aktif' => 'Arsip Aktif',
            'inaktif' => 'Arsip Inaktif',
            'permanen' => 'Arsip Permanen',
            'musnah' => 'Arsip Musnah'
        ];

        $archiveCounts = [];
        foreach ($statuses as $key => $label) {
            $query = \App\Models\Archive::where('created_by', Auth::id());
            if ($key !== 'all') {
                $query->where('status', ucfirst($key));
            }
            $archiveCounts[$key] = $query->count();
        }

        return view($this->getViewPath('archives.export-menu'), compact('statuses', 'archiveCounts'));
    }
}
