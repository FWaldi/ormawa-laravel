<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);
        
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('name')->get();
        return view('activities.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organization_id' => 'required|exists:organizations,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled', 'completed'])],
        ]);

        $activityData = $validated;
        $activityData['created_by'] = Auth::id();

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('activities', 'public');
                $images[] = $path;
            }
            $activityData['images'] = $images;
        }

        Activity::create($activityData);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully!');
    }

    public function show(Activity $activity)
    {
        $activity->load(['organization', 'creator']);
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $organizations = Organization::orderBy('name')->get();
        
        // Check if user can edit this activity
        if (Auth::id() !== $activity->created_by && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('activities.edit', compact('activity', 'organizations'));
    }

    public function update(Request $request, Activity $activity)
    {
        // Check if user can edit this activity
        if (Auth::id() !== $activity->created_by && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organization_id' => 'required|exists:organizations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled', 'completed'])],
        ]);

        $activityData = $validated;

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $images = $activity->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('activities', 'public');
                $images[] = $path;
            }
            $activityData['images'] = $images;
        }

        $activity->update($activityData);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity updated successfully!');
    }

    public function destroy(Activity $activity)
    {
        // Check if user can delete this activity
        if (Auth::id() !== $activity->created_by && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated images
        if ($activity->images) {
            foreach ($activity->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully!');
    }

    public function updateStatus(Request $request, Activity $activity)
    {
        // Check if user can update status
        if (Auth::id() !== $activity->created_by && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled', 'completed'])],
        ]);

        $activity->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Activity status updated successfully!');
    }

    public function removeImage(Request $request, Activity $activity)
    {
        // Check if user can edit this activity
        if (Auth::id() !== $activity->created_by && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'image_index' => 'required|integer|min:0',
        ]);

        $images = $activity->images ?? [];
        $index = $validated['image_index'];

        if (isset($images[$index])) {
            // Delete the file
            Storage::disk('public')->delete($images[$index]);
            
            // Remove from array
            array_splice($images, $index, 1);
            
            // Update activity
            $activity->update(['images' => $images]);
        }

        return redirect()->back()
            ->with('success', 'Image removed successfully!');
    }

    public function calendar()
    {
        $activities = Activity::with(['organization'])
            ->where('status', 'published')
            ->orderBy('start_date')
            ->get();
        
        return view('activities.calendar', compact('activities'));
    }
}