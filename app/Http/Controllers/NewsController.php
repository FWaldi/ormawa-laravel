<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Organization;
use App\Services\HtmlSanitizer;
use App\Services\FileUploadService;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index(Request $request)
    {
        try {
            $query = News::with(['organization', 'creator'])
                ->where('is_published', true)
                ->orderBy('published_at', 'desc');

            // Filter by organization if specified
            if ($request->has('organization_id') && $request->organization_id) {
                $query->where('organization_id', $request->organization_id);
            }

            // Search functionality
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('content', 'like', "%{$searchTerm}%");
                });
            }

            $news = $query->paginate(12);
            $organizations = Organization::orderBy('name')->get();
        } catch (\Exception $e) {
            // Fallback to empty collections if database is not available
            $news = collect();
            $organizations = collect();
        }

        return view('news.index', compact('news', 'organizations'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create()
    {
        $user = Auth::user();
        $organizations = [];

        // Get organizations where user is admin or member
        if ($user->is_admin) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizations = $user->organizations()->orderBy('name')->get();
        }

        return view('news.create', compact('organizations'));
    }

    /**
     * Store a newly created news article.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|min:10|max:50000',
            'organization_id' => 'required|exists:organizations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
        ]);

        $user = Auth::user();
        
        // Check if user has permission to create news for this organization
        if (!$user->is_admin && !$user->organizations()->where('organizations.id', $request->organization_id)->exists()) {
            abort(403, 'You do not have permission to create news for this organization.');
        }

        $newsData = [
            'title' => $request->title,
            'content' => HtmlSanitizer::clean($request->content),
            'organization_id' => $request->organization_id,
            'created_by' => $user->id,
            'is_published' => $request->boolean('is_published', false),
        ];

        if ($newsData['is_published']) {
            $newsData['published_at'] = now();
        }

        // Handle image upload with enhanced security
        if ($request->hasFile('image')) {
            $uploadResult = FileUploadService::secureUpload($request->file('image'), 'news');
            if (!$uploadResult) {
                return back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please ensure it is a valid image file.']);
            }
            $newsData['image'] = $uploadResult['path'];
        }

        $news = News::create($newsData);

        return redirect()
            ->route('news.show', $news)
            ->with('success', 'News article created successfully!');
    }

    /**
     * Display the specified news article.
     */
    public function show(News $news)
    {
        // Only show published news unless user is admin or creator
        if (!$news->is_published && !Auth::user()?->is_admin && Auth::id() !== $news->created_by) {
            abort(404);
        }

        $news->load(['organization', 'creator']);

        // Get related news from same organization
        $relatedNews = News::where('organization_id', $news->organization_id)
            ->where('id', '!=', $news->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news)
    {
        $user = Auth::user();
        
        // Check permissions: admin or creator
        if (!$user->is_admin && $user->id !== $news->created_by) {
            abort(403, 'You do not have permission to edit this news article.');
        }

        $organizations = [];
        if ($user->is_admin) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizations = $user->organizations()->orderBy('name')->get();
        }

        return view('news.edit', compact('news', 'organizations'));
    }

    /**
     * Update the specified news article.
     */
    public function update(Request $request, News $news)
    {
        $user = Auth::user();
        
        // Check permissions: admin or creator
        if (!$user->is_admin && $user->id !== $news->created_by) {
            abort(403, 'You do not have permission to edit this news article.');
        }

        $request->validate([
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|min:10|max:50000',
            'organization_id' => 'required|exists:organizations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
            'remove_image' => 'boolean',
        ]);

        // Check organization permission
        if (!$user->is_admin && !$user->organizations()->where('organizations.id', $request->organization_id)->exists()) {
            abort(403, 'You do not have permission to assign news to this organization.');
        }

        $newsData = [
            'title' => $request->title,
            'content' => HtmlSanitizer::clean($request->content),
            'organization_id' => $request->organization_id,
            'is_published' => $request->boolean('is_published', false),
        ];

        // Handle publish status change
        if ($newsData['is_published'] && !$news->is_published) {
            $newsData['published_at'] = now();
        } elseif (!$newsData['is_published']) {
            $newsData['published_at'] = null;
        }

        // Handle image upload with enhanced security
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image) {
                FileUploadService::secureDelete($news->image);
            }

            $uploadResult = FileUploadService::secureUpload($request->file('image'), 'news');
            if (!$uploadResult) {
                return back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please ensure it is a valid image file.']);
            }
            $newsData['image'] = $uploadResult['path'];
        } elseif ($request->boolean('remove_image') && $news->image) {
            FileUploadService::secureDelete($news->image);
            $newsData['image'] = null;
        }

        $news->update($newsData);

        return redirect()
            ->route('news.show', $news)
            ->with('success', 'News article updated successfully!');
    }

    /**
     * Remove the specified news article.
     */
    public function destroy(News $news)
    {
        $user = Auth::user();
        
        // Check permissions: admin or creator
        if (!$user->is_admin && $user->id !== $news->created_by) {
            abort(403, 'You do not have permission to delete this news article.');
        }

        // Delete image if exists
        if ($news->image) {
            FileUploadService::secureDelete($news->image);
        }

        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'News article deleted successfully!');
    }

    /**
     * Display news for a specific organization.
     */
    public function organizationNews(Organization $organization)
    {
        $news = News::with(['creator'])
            ->where('organization_id', $organization->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('news.organization', compact('news', 'organization'));
    }

    /**
     * Remove image from news article.
     */
    public function removeImage(News $news)
    {
        $user = Auth::user();
        
        // Check permissions: admin or creator
        if (!$user->is_admin && $user->id !== $news->created_by) {
            abort(403, 'You do not have permission to edit this news article.');
        }

        if ($news->image) {
            FileUploadService::secureDelete($news->image);
            $news->update(['image' => null]);
        }

        return back()->with('success', 'Image removed successfully!');
    }
}