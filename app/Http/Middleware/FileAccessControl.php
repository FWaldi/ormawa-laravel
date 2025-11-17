<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use Symfony\Component\HttpFoundation\Response;

class FileAccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $filename = $request->route('filename');
        $disk = $request->route('disk');

        // Find the file record
        $file = File::where('filename', $filename)
                   ->where('disk', $disk)
                   ->first();

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Check if user has access to this file
        if (!$this->hasFileAccess($file)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Add file to request for later use
        $request->merge(['file_record' => $file]);

        return $next($request);
    }

    /**
     * Check if user has access to the file
     *
     * @param File $file
     * @return bool
     */
    private function hasFileAccess(File $file): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin can access anything
        if ($user->is_admin) {
            return true;
        }

        // Users can access their own files
        if ($file->uploaded_by === $user->id) {
            return true;
        }

        // Check based on file context
        switch ($file->context) {
            case 'organization':
                return $this->hasOrganizationAccess($file, $user);
            
            case 'activity':
                return $this->hasActivityAccess($file, $user);
            
            case 'news':
            case 'announcement':
                // All authenticated users can access news/announcements
                return true;
            
            default:
                return false;
        }
    }

    /**
     * Check if user has access to organization files
     *
     * @param File $file
     * @param User $user
     * @return bool
     */
    private function hasOrganizationAccess(File $file, $user): bool
    {
        if (!$file->context_id) {
            return false;
        }

        // Check if user is member of the organization
        return $user->organizations()
                   ->where('organizations.id', $file->context_id)
                   ->exists();
    }

    /**
     * Check if user has access to activity files
     *
     * @param File $file
     * @param User $user
     * @return bool
     */
    private function hasActivityAccess(File $file, $user): bool
    {
        if (!$file->context_id) {
            return false;
        }

        // Check if user is member of the organization that owns the activity
        return $user->organizations()
                   ->whereHas('activities', function ($query) use ($file) {
                       $query->where('activities.id', $file->context_id);
                   })
                   ->exists();
    }
}