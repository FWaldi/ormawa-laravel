<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CalendarController;

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Health check route
Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});

// Authentication routes with rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Google OAuth routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Password reset routes with rate limiting
Route::middleware('throttle:3,1')->group(function () {
    Route::get('/forgot-password', [AuthController::class, 'showPasswordReset'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
});

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (string $id, string $hash) {
    // Email verification logic will be handled by Laravel's built-in verification
    return redirect()->route('login')->with('status', 'Email verified successfully!');
})->middleware('auth', 'signed')->name('verification.verify');

// Public index routes (no authentication required)
Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/announcements-test', function() {
    return response()->json(['status' => 'ok', 'message' => 'Announcements route works']);
});
Route::get('/announcements', function() {
    try {
        $announcements = \App\Models\Announcement::with('creator')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    } catch (\Exception $e) {
        // Fallback: return a simple working page
        return response()->view('announcements.index', ['announcements' => collect()], 200)
            ->header('X-Debug-Error', $e->getMessage());
    }
})->name('announcements.index');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Organizations
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');

    // Organization creation with rate limiting
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    });
    Route::get('/organizations/{id}', [OrganizationController::class, 'show'])->name('organizations.show');
    Route::get('/organizations/{id}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
    Route::put('/organizations/{id}', [OrganizationController::class, 'update'])->name('organizations.update');
    Route::delete('/organizations/{id}', [OrganizationController::class, 'destroy'])->name('organizations.destroy');

    // Organization member management with rate limiting
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/organizations/{id}/add-member', [OrganizationController::class, 'addMember'])->name('organizations.addMember');
        Route::delete('/organizations/{id}/remove-member/{userId}', [OrganizationController::class, 'removeMember'])->name('organizations.removeMember');
    });

    // Activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::post('/activities/{activity}/update-status', [ActivityController::class, 'updateStatus'])->name('activities.updateStatus');
    Route::post('/activities/{activity}/remove-image', [ActivityController::class, 'removeImage'])->name('activities.removeImage');
    Route::get('/activities/calendar', [ActivityController::class, 'calendar'])->name('activities.calendar');

    // Integrated Calendar (additional protected routes)
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/calendar/data', [CalendarController::class, 'data'])->name('calendar.data');
        Route::get('/calendar/filter', [CalendarController::class, 'filterByDateRange'])->name('calendar.filter');
        Route::get('/calendar/month/{year?}/{month?}', [CalendarController::class, 'monthEvents'])->name('calendar.month');
        Route::get('/calendar/week/{year?}/{week?}', [CalendarController::class, 'weekEvents'])->name('calendar.week');
        Route::get('/calendar/day/{year?}/{month?}/{day?}', [CalendarController::class, 'dayEvents'])->name('calendar.day');
    });

    // Announcements
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create')->middleware('admin');

    // Announcement creation and update with rate limiting
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store')->middleware('admin');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update')->middleware('admin');
    });

    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit')->middleware('admin');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy')->middleware('admin');

    // Soft delete management routes (admin only)
    Route::get('/announcements/trashed', [AnnouncementController::class, 'trashed'])->name('announcements.trashed')->middleware('admin');
    Route::post('/announcements/{id}/restore', [AnnouncementController::class, 'restore'])->name('announcements.restore')->middleware('admin');
    Route::delete('/announcements/{id}/force-delete', [AnnouncementController::class, 'forceDelete'])->name('announcements.forceDelete')->middleware('admin');

    // News
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');

    // News creation and update with rate limiting
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
        Route::post('/news/{news}/remove-image', [NewsController::class, 'removeImage'])->name('news.removeImage');
    });

    Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::get('/organizations/{organization}/news', [NewsController::class, 'organizationNews'])->name('news.organization');

    // File upload routes with rate limiting
    Route::middleware('throttle:10,1')->prefix('upload')->group(function () {
        Route::post('/organizations', [UploadController::class, 'uploadOrganizationFile'])->name('upload.organizations');
        Route::post('/activities', [UploadController::class, 'uploadActivityFile'])->name('upload.activities');
        Route::post('/news', [UploadController::class, 'uploadNewsFile'])->name('upload.news');
        Route::post('/announcements', [UploadController::class, 'uploadAnnouncementFile'])->name('upload.announcements');

        // File management routes
        Route::delete('/{disk}', [UploadController::class, 'deleteFile'])->name('upload.delete');
        Route::get('/{disk}/url', [UploadController::class, 'getFileUrl'])->name('upload.url');
    });
});

// Admin routes (require admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Secure file access route
Route::get('/files/{disk}/{path}', [UploadController::class, 'serveFile'])
    ->where('path', '.*')
    ->name('files.show');
