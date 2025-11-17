<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Handle file upload for organizations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadOrganizationFile(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'organizations', 'organization');
    }

    /**
     * Handle file upload for activities
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadActivityFile(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'activities', 'activity');
    }

    /**
     * Handle file upload for news
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadNewsFile(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'news', 'news');
    }

    /**
     * Handle file upload for announcements
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAnnouncementFile(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'announcements', 'announcement');
    }

    /**
     * Generic upload handler
     *
     * @param Request $request
     * @param string $disk
     * @param string $context
     * @return JsonResponse
     */
    protected function handleUpload(Request $request, string $disk, string $context): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:5120', // Max 5MB
                'context_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $contextId = $request->input('context_id');

            // Check permissions based on context
            if (!$this->checkUploadPermissions($context, $contextId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to upload files for this context'
                ], 403);
            }

            // Perform the upload
            $result = $this->storageService->uploadFile($file, $disk, $context, $contextId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed'
                ], 500);
            }

            Log::info('File uploaded successfully', [
                'context' => $context,
                'context_id' => $contextId,
                'filename' => $result['filename'],
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('File upload error', [
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during file upload'
            ], 500);
        }
    }

    /**
     * Delete a file
     *
     * @param Request $request
     * @param string $disk
     * @return JsonResponse
     */
    public function deleteFile(Request $request, string $disk): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'filename' => 'required|string',
                'context' => 'required|string|in:organization,activity,news,announcement',
                'context_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $filename = $request->input('filename');
            $context = $request->input('context');
            $contextId = $request->input('context_id');

            // Check permissions
            if (!$this->checkDeletePermissions($context, $contextId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this file'
                ], 403);
            }

            $success = $this->storageService->deleteFile($filename, $disk);

            if ($success) {
                Log::info('File deleted successfully', [
                    'disk' => $disk,
                    'filename' => $filename,
                    'context' => $context,
                    'context_id' => $contextId,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File deletion failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('File deletion error', [
                'disk' => $disk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during file deletion'
            ], 500);
        }
    }

    /**
     * Get file URL with access control
     *
     * @param Request $request
     * @param string $disk
     * @return JsonResponse
     */
    public function getFileUrl(Request $request, string $disk): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'filename' => 'required|string',
                'context' => 'required|string|in:organization,activity,news,announcement',
                'context_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $filename = $request->input('filename');
            $context = $request->input('context');
            $contextId = $request->input('context_id');

            // Check access permissions
            if (!$this->checkAccessPermissions($context, $contextId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this file'
                ], 403);
            }

            $url = $this->storageService->getFileUrl($filename, $disk);

            if ($url) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => $url,
                        'filename' => $filename
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('File access error', [
                'disk' => $disk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while accessing the file'
            ], 500);
        }
    }

    /**
     * Check upload permissions
     *
     * @param string $context
     * @param int|null $contextId
     * @return bool
     */
    protected function checkUploadPermissions(string $context, ?int $contextId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin can upload anywhere
        if ($user->is_admin) {
            return true;
        }

        switch ($context) {
            case 'organization':
                // Check if user is admin of the organization
                if ($contextId) {
                    return $user->organizations()
                        ->where('organizations.id', $contextId)
                        ->where('user_organization.role', 'admin')
                        ->exists();
                }
                // Or if user is any organization admin
                return $user->organizations()->where('user_organization.role', 'admin')->exists();

            case 'activity':
                // Check if user is admin of the organization that owns the activity
                if ($contextId) {
                    return $user->organizations()
                        ->whereHas('activities', function ($query) use ($contextId) {
                            $query->where('activities.id', $contextId);
                        })
                        ->where('user_organization.role', 'admin')
                        ->exists();
                }
                return false;

            case 'news':
            case 'announcement':
                // Admins and organization admins can upload news/announcements
                return $user->organizations()->where('user_organization.role', 'admin')->exists();

            default:
                return false;
        }
    }

    /**
     * Check delete permissions
     *
     * @param string $context
     * @param int|null $contextId
     * @return bool
     */
    protected function checkDeletePermissions(string $context, ?int $contextId): bool
    {
        // Delete permissions are the same as upload permissions
        return $this->checkUploadPermissions($context, $contextId);
    }

    /**
     * Check access permissions
     *
     * @param string $context
     * @param int|null $contextId
     * @return bool
     */
    protected function checkAccessPermissions(string $context, ?int $contextId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin can access anything
        if ($user->is_admin) {
            return true;
        }

        switch ($context) {
            case 'organization':
                // Organization members can access organization files
                if ($contextId) {
                    return $user->organizations()
                        ->where('organizations.id', $contextId)
                        ->exists();
                }
                return $user->organizations()->exists();

            case 'activity':
                // Organization members can access activity files
                if ($contextId) {
                    return $user->organizations()
                        ->whereHas('activities', function ($query) use ($contextId) {
                            $query->where('activities.id', $contextId);
                        })
                        ->exists();
                }
                return false;

            case 'news':
            case 'announcement':
                // All authenticated users can access news/announcements
                return true;

            default:
                return false;
        }
    }

    /**
     * Serve file securely with access control
     *
     * @param string $disk
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function serveFile(string $disk, string $path)
    {
        try {
            $filename = basename($path);
            
            // Get file metadata to determine context
            $metadata = $this->storageService->getFileInfo($filename, $disk);
            
            if (!$metadata) {
                abort(404, 'File not found');
            }

            // Extract context from metadata or path
            $context = $this->extractContextFromPath($path);
            $contextId = $this->extractContextIdFromPath($path);

            // Check access permissions
            if (!$this->checkAccessPermissions($context, $contextId)) {
                abort(403, 'Access denied');
            }

            // Check if file exists
            if (!Storage::disk($disk)->exists($path)) {
                abort(404, 'File not found');
            }

            $file = Storage::disk($disk)->get($path);
            $mimeType = $metadata['mime_type'] ?? 'application/octet-stream';

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'private, max-age=3600')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error serving file', [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Error serving file');
        }
    }

    /**
     * Extract context from file path
     *
     * @param string $path
     * @return string
     */
    private function extractContextFromPath(string $path): string
    {
        $parts = explode('/', $path);
        
        if (count($parts) >= 1) {
            $firstPart = $parts[0];
            
            // Map directory names to context
            $contextMap = [
                'organizations' => 'organization',
                'activities' => 'activity',
                'news' => 'news',
                'announcements' => 'announcement'
            ];

            return $contextMap[$firstPart] ?? 'unknown';
        }

        return 'unknown';
    }

    /**
     * Extract context ID from file path
     *
     * @param string $path
     * @return int|null
     */
    private function extractContextIdFromPath(string $path): ?int
    {
        $parts = explode('/', $path);
        
        if (count($parts) >= 2 && is_numeric($parts[1])) {
            return (int) $parts[1];
        }

        return null;
    }
}