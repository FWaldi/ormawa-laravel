<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index(Request $request)
    {
        try {
            $organizations = Organization::orderBy('name')->get();
        } catch (\Exception $e) {
            // Fallback to empty collection if database is not available
            $organizations = collect();
        }

        $currentDate = $request->get('date', now()->format('Y-m-d'));
        $view = $request->get('view', 'month'); // month, week, day

        return view('calendar.index', compact('organizations', 'currentDate', 'view'));
    }

    /**
     * Get calendar data for AJAX requests.
     */
    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'organization_id' => 'nullable|exists:organizations,id',
            'event_type' => 'nullable|in:activity,announcement,all',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($request->get('start'));
        $endDate = Carbon::parse($request->get('end'));
        $organizationId = $request->get('organization_id');
        $eventType = $request->get('event_type', 'all');
        $search = $request->get('search');

        // Create cache key based on request parameters
        $cacheKey = 'calendar_data_' . md5(serialize([
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'organization_id' => $organizationId,
            'event_type' => $eventType,
            'search' => $search,
        ]));

        // Return cached data if available (cache for 10 minutes)
        if ($search === null) { // Only cache non-search queries
            $cachedEvents = Cache::get($cacheKey);
            if ($cachedEvents !== null) {
                return response()->json($cachedEvents);
            }
        }

        $events = [];

        // Get activities
        if ($eventType === 'all' || $eventType === 'activity') {
            $activitiesQuery = Activity::with(['organization', 'creator'])
                ->where('status', 'published')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($query) use ($startDate, $endDate) {
                              $query->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                          });
                });

            // Apply organization filter
            if ($organizationId) {
                $activitiesQuery->where('organization_id', $organizationId);
            }

            // Apply search filter
            if ($search) {
                $activitiesQuery->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('location', 'like', "%{$search}%");
                });
            }

            $activities = $activitiesQuery->get();

            foreach ($activities as $activity) {
                $events[] = [
                    'id' => 'activity_' . $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'start' => $activity->start_date->toISOString(),
                    'end' => $activity->end_date->toISOString(),
                    'type' => 'activity',
                    'color' => '#3B82F6', // Blue for activities
                    'textColor' => '#FFFFFF',
                    'extendedProps' => [
                        'location' => $activity->location,
                        'organization' => $activity->organization->name,
                        'creator' => $activity->creator->name,
                        'url' => route('activities.show', $activity),
                        'images' => $activity->image_urls,
                    ],
                ];
            }
        }

        // Get announcements
        if ($eventType === 'all' || $eventType === 'announcement') {
            $announcementsQuery = Announcement::with(['creator'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('updated_at', [$startDate, $endDate]);
                });

            // Apply search filter
            if ($search) {
                $announcementsQuery->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%")
                          ->orWhere('category', 'like', "%{$search}%");
                });
            }

            // Note: Announcements don't have organization_id in the current schema
            // If needed, we could add organization relationship to announcements

            $announcements = $announcementsQuery->get();

            foreach ($announcements as $announcement) {
                $events[] = [
                    'id' => 'announcement_' . $announcement->id,
                    'title' => $announcement->title,
                    'description' => $announcement->content,
                    'start' => $announcement->created_at->toISOString(),
                    'end' => $announcement->created_at->addHours(1)->toISOString(), // Default 1 hour duration
                    'type' => 'announcement',
                    'color' => '#10B981', // Green for announcements
                    'textColor' => '#FFFFFF',
                    'extendedProps' => [
                        'category' => $announcement->category,
                        'creator' => $announcement->creator->name,
                        'url' => route('announcements.show', $announcement),
                        'image' => $announcement->image_url,
                        'is_pinned' => $announcement->is_pinned,
                    ],
                ];
            }
        }

        // Sort events by start date
        usort($events, function ($a, $b) {
            return strtotime($a['start']) - strtotime($b['start']);
        });

        // Cache the results for non-search queries
        if ($search === null) {
            Cache::put($cacheKey, $events, 600); // 10 minutes
        }

        return response()->json($events);
    }

    /**
     * Filter events by date range.
     */
    public function filterByDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'organization_id' => 'nullable|exists:organizations,id',
            'event_type' => 'nullable|in:activity,announcement,all',
            'search' => 'nullable|string|max:255',
        ]);

        // Reuse the data method with date parameters
        return $this->data($request);
    }

    /**
     * Get events for a specific month.
     */
    public function monthEvents(Request $request, $year = null, $month = null): JsonResponse
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $request->merge([
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ]);

        return $this->data($request);
    }

    /**
     * Get events for a specific week.
     */
    public function weekEvents(Request $request, $year = null, $week = null): JsonResponse
    {
        $year = $year ?? now()->year;
        $week = $week ?? now()->weekOfYear;
        
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = Carbon::now()->setISODate($year, $week)->endOfWeek();

        $request->merge([
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ]);

        return $this->data($request);
    }

    /**
     * Get events for a specific day.
     */
    public function dayEvents(Request $request, $year = null, $month = null, $day = null): JsonResponse
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        $day = $day ?? now()->day;
        
        $startDate = Carbon::create($year, $month, $day)->startOfDay();
        $endDate = Carbon::create($year, $month, $day)->endOfDay();

        $request->merge([
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ]);

        return $this->data($request);
    }
}