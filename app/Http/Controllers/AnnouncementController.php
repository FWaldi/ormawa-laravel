<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        try {
            $announcements = Announcement::with('creator')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            // Fallback to empty collection if database is not available
            $announcements = collect();
        }

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_pinned' => 'boolean',
        ]);

        $announcement = new Announcement();
        $announcement->title = $validated['title'];
        $announcement->content = HtmlSanitizer::sanitize($validated['content']);
        $announcement->category = $validated['category'] ?? null;
        $announcement->is_pinned = $request->boolean('is_pinned', false);
        $announcement->created_by = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
            $announcement->image = $imagePath;
        }

        $announcement->save();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        $announcement->load('creator');
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_pinned' => 'boolean',
        ]);

        $announcement->title = $validated['title'];
        $announcement->content = HtmlSanitizer::sanitize($validated['content']);
        $announcement->category = $validated['category'] ?? null;
        $announcement->is_pinned = $request->boolean('is_pinned', false);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image) {
                $oldImagePath = storage_path('app/public/' . $announcement->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imagePath = $request->file('image')->store('announcements', 'public');
            $announcement->image = $imagePath;
        }

        $announcement->save();

        return redirect()
            ->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage (soft delete).
     */
    public function destroy(Announcement $announcement)
    {
        // Soft delete the announcement (keeps record for recovery)
        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Restore a soft deleted announcement.
     */
    public function restore($id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        $announcement->restore();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement restored successfully.');
    }

    /**
     * Permanently delete a soft deleted announcement.
     */
    public function forceDelete($id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        
        // Delete image if exists
        if ($announcement->image) {
            $imagePath = storage_path('app/public/' . $announcement->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $announcement->forceDelete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement permanently deleted.');
    }

    /**
     * Display trashed announcements.
     */
    public function trashed()
    {
        $announcements = Announcement::onlyTrashed()
            ->with('creator')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('announcements.trashed', compact('announcements'));
    }
}