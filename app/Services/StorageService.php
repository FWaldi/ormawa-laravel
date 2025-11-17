<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StorageService
{
    /**
     * Allowed MIME types for different contexts
     */
    private static $allowedMimeTypes = [
        'images' => [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp'
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ]
    ];

    /**
     * Maximum file sizes in bytes for different contexts
     */
    private static $maxFileSizes = [
        'images' => 5242880, // 5MB
        'documents' => 10485760, // 10MB
        'default' => 2097152 // 2MB
    ];

    /**
     * Upload a file with security validation
     *
     * @param UploadedFile $file
     * @param string $disk
     * @param string $context
     * @param int|null $contextId
     * @return array|null
     */
    public function uploadFile(UploadedFile $file, string $disk, string $context, ?int $contextId = null): ?array
    {
        try {
            // Determine file type category
            $fileCategory = $this->getFileCategory($file);
            
            // Validate file
            if (!$this->validateFile($file, $fileCategory)) {
                return null;
            }

            // Generate secure filename with context
            $filename = $this->generateSecureFilename($file, $context, $contextId);
            
            // Create directory structure if context_id is provided
            $directory = $contextId ? "{$context}/{$contextId}" : $context;
            
            // Store file securely
            $path = $file->storeAs($directory, $filename, $disk);
            
            if (!$path) {
                Log::error('Failed to store file', [
                    'file' => $file->getClientOriginalName(),
                    'disk' => $disk,
                    'directory' => $directory
                ]);
                return null;
            }

            // Perform additional security checks
            if (!$this->performSecurityChecks($path, $disk)) {
                // Clean up if security checks fail
                Storage::disk($disk)->delete($path);
                return null;
            }

            // Store file metadata
            $metadata = $this->storeFileMetadata($file, $path, $disk, $context, $contextId);

            return [
                'path' => $path,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'category' => $fileCategory,
                'disk' => $disk,
                'url' => $this->getFileUrl($filename, $disk),
                'metadata' => $metadata
            ];

        } catch (\Exception $e) {
            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'disk' => $disk,
                'context' => $context,
                'context_id' => $contextId
            ]);
            return null;
        }
    }

    /**
     * Delete a file
     *
     * @param string $filename
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $filename, string $disk): bool
    {
        try {
            // Find the file in the disk
            $files = Storage::disk($disk)->allFiles();
            $filePath = null;

            foreach ($files as $file) {
                if (basename($file) === $filename) {
                    $filePath = $file;
                    break;
                }
            }

            if (!$filePath) {
                Log::warning('File not found for deletion', [
                    'filename' => $filename,
                    'disk' => $disk
                ]);
                return false;
            }

            $success = Storage::disk($disk)->delete($filePath);

            if ($success) {
                // Remove file metadata
                $this->removeFileMetadata($filename, $disk);
                
                Log::info('File deleted successfully', [
                    'filename' => $filename,
                    'disk' => $disk,
                    'path' => $filePath
                ]);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('Error deleting file', [
                'filename' => $filename,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get file URL with access control
     *
     * @param string $filename
     * @param string $disk
     * @return string|null
     */
    public function getFileUrl(string $filename, string $disk): ?string
    {
        try {
            // Find the file in the disk
            $files = Storage::disk($disk)->allFiles();
            $filePath = null;

            foreach ($files as $file) {
                if (basename($file) === $filename) {
                    $filePath = $file;
                    break;
                }
            }

            if (!$filePath) {
                return null;
            }

            // For private disks, generate temporary URL
            if (config("filesystems.disks.{$disk}.visibility") === 'private') {
                return $this->generateTemporaryUrl($filePath, $disk);
            }

            // For public disks, return direct URL
            return Storage::disk($disk)->url($filePath);

        } catch (\Exception $e) {
            Log::error('Error generating file URL', [
                'filename' => $filename,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get file information
     *
     * @param string $filename
     * @param string $disk
     * @return array|null
     */
    public function getFileInfo(string $filename, string $disk): ?array
    {
        try {
            $files = Storage::disk($disk)->allFiles();
            $filePath = null;

            foreach ($files as $file) {
                if (basename($file) === $filename) {
                    $filePath = $file;
                    break;
                }
            }

            if (!$filePath || !Storage::disk($disk)->exists($filePath)) {
                return null;
            }

            $metadata = $this->getFileMetadata($filename, $disk);

            return [
                'filename' => $filename,
                'path' => $filePath,
                'size' => Storage::disk($disk)->size($filePath),
                'last_modified' => Storage::disk($disk)->lastModified($filePath),
                'mime_type' => $metadata['mime_type'] ?? null,
                'original_name' => $metadata['original_name'] ?? null,
                'url' => $this->getFileUrl($filename, $disk)
            ];

        } catch (\Exception $e) {
            Log::error('Error getting file info', [
                'filename' => $filename,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Clean up orphaned files
     *
     * @param string $disk
     * @param int $daysOld
     * @return int Number of files cleaned up
     */
    public function cleanupOrphanedFiles(string $disk, int $daysOld = 30): int
    {
        try {
            $cutoffTime = Carbon::now()->subDays($daysOld)->timestamp;
            $cleanedCount = 0;

            $files = Storage::disk($disk)->allFiles();

            foreach ($files as $file) {
                $lastModified = Storage::disk($disk)->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    $filename = basename($file);
                    
                    // Check if file is referenced in metadata
                    if (!$this->isFileReferenced($filename, $disk)) {
                        Storage::disk($disk)->delete($file);
                        $this->removeFileMetadata($filename, $disk);
                        $cleanedCount++;
                        
                        Log::info('Orphaned file cleaned up', [
                            'file' => $file,
                            'disk' => $disk,
                            'days_old' => $daysOld
                        ]);
                    }
                }
            }

            Log::info('Orphaned file cleanup completed', [
                'disk' => $disk,
                'files_cleaned' => $cleanedCount,
                'days_old' => $daysOld
            ]);

            return $cleanedCount;

        } catch (\Exception $e) {
            Log::error('Error during orphaned file cleanup', [
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get disk usage statistics
     *
     * @param string $disk
     * @return array
     */
    public function getDiskUsage(string $disk): array
    {
        try {
            $totalSize = 0;
            $fileCount = 0;
            $filesByType = [];

            $files = Storage::disk($disk)->allFiles();

            foreach ($files as $file) {
                $size = Storage::disk($disk)->size($file);
                $totalSize += $size;
                $fileCount++;

                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filesByType[$extension] = ($filesByType[$extension] ?? 0) + 1;
            }

            return [
                'disk' => $disk,
                'total_size' => $totalSize,
                'total_size_human' => $this->formatBytes($totalSize),
                'file_count' => $fileCount,
                'files_by_type' => $filesByType
            ];

        } catch (\Exception $e) {
            Log::error('Error getting disk usage', [
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return [
                'disk' => $disk,
                'total_size' => 0,
                'total_size_human' => '0 B',
                'file_count' => 0,
                'files_by_type' => []
            ];
        }
    }

    /**
     * Validate file against security rules
     *
     * @param UploadedFile $file
     * @param string $category
     * @return bool
     */
    private function validateFile(UploadedFile $file, string $category): bool
    {
        // Check file size
        $maxSize = self::$maxFileSizes[$category] ?? self::$maxFileSizes['default'];
        if ($file->getSize() > $maxSize) {
            Log::warning('File upload rejected: size exceeded', [
                'size' => $file->getSize(),
                'max' => $maxSize,
                'file' => $file->getClientOriginalName(),
                'category' => $category
            ]);
            return false;
        }

        // Check MIME type
        $allowedMimes = array_merge(
            self::$allowedMimeTypes['images'] ?? [],
            self::$allowedMimeTypes['documents'] ?? []
        );

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            Log::warning('File upload rejected: invalid MIME type', [
                'mime' => $file->getMimeType(),
                'file' => $file->getClientOriginalName()
            ]);
            return false;
        }

        // Additional validation: check file extension matches MIME type
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedExtensions = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/jpg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'application/vnd.ms-excel' => ['xls'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'application/vnd.ms-powerpoint' => ['ppt'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
            'text/plain' => ['txt'],
            'text/csv' => ['csv']
        ];

        if (!isset($expectedExtensions[$file->getMimeType()]) || 
            !in_array($extension, $expectedExtensions[$file->getMimeType()])) {
            Log::warning('File upload rejected: extension mismatch', [
                'mime' => $file->getMimeType(),
                'extension' => $extension,
                'file' => $file->getClientOriginalName()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get file category (images or documents)
     *
     * @param UploadedFile $file
     * @return string
     */
    private function getFileCategory(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, self::$allowedMimeTypes['images'])) {
            return 'images';
        }

        if (in_array($mimeType, self::$allowedMimeTypes['documents'])) {
            return 'documents';
        }

        return 'default';
    }

    /**
     * Generate secure filename
     *
     * @param UploadedFile $file
     * @param string $context
     * @param int|null $contextId
     * @return string
     */
    private function generateSecureFilename(UploadedFile $file, string $context, ?int $contextId): string
    {
        $timestamp = time();
        $random = Str::random(16);
        $contextPart = $contextId ? "{$context}_{$contextId}" : $context;
        
        return "{$contextPart}_{$timestamp}_{$random}." . $file->getClientOriginalExtension();
    }

    /**
     * Perform additional security checks
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    private function performSecurityChecks(string $path, string $disk): bool
    {
        $fullPath = Storage::disk($disk)->path($path);
        
        // Check if file exists and is readable
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::error('Uploaded file not accessible', ['path' => $path]);
            return false;
        }

        // Get file info to verify it's a valid file
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMimeType = finfo_file($fileInfo, $fullPath);
        finfo_close($fileInfo);

        // Verify detected MIME type matches expected
        $allowedMimes = array_merge(
            self::$allowedMimeTypes['images'] ?? [],
            self::$allowedMimeTypes['documents'] ?? []
        );

        if (!in_array($detectedMimeType, $allowedMimes)) {
            Log::error('File MIME type verification failed', [
                'path' => $path,
                'detected_mime' => $detectedMimeType
            ]);
            return false;
        }

        // Basic virus scan (check for embedded malicious content)
        if ($this->containsMaliciousContent($fullPath)) {
            Log::error('Malicious content detected in uploaded file', ['path' => $path]);
            return false;
        }

        return true;
    }

    /**
     * Basic check for malicious content
     *
     * @param string $filePath
     * @return bool
     */
    private function containsMaliciousContent(string $filePath): bool
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return true; // Treat as suspicious if can't open
        }

        $content = fread($handle, 8192); // Read first 8KB
        fclose($handle);

        // Check for common malicious patterns
        $maliciousPatterns = [
            '<?php',
            '<script',
            'javascript:',
            'vbscript:',
            'data:text/html',
            'eval(',
            'base64_decode',
            'exec(',
            'system(',
            'shell_exec(',
            'passthru('
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store file metadata
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $disk
     * @param string $context
     * @param int|null $contextId
     * @return array
     */
    private function storeFileMetadata(UploadedFile $file, string $path, string $disk, string $context, ?int $contextId): array
    {
        $metadata = [
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => $disk,
            'context' => $context,
            'context_id' => $contextId,
            'uploaded_at' => now()->toISOString(),
            'path' => $path
        ];

        // Store metadata in a simple JSON file (in production, consider database)
        $metadataFile = storage_path("app/metadata/{$disk}_metadata.json");
        $metadataDir = dirname($metadataFile);
        
        if (!is_dir($metadataDir)) {
            mkdir($metadataDir, 0755, true);
        }

        $existingMetadata = [];
        if (file_exists($metadataFile)) {
            $existingMetadata = json_decode(file_get_contents($metadataFile), true) ?? [];
        }

        $existingMetadata[$metadata['filename']] = $metadata;
        file_put_contents($metadataFile, json_encode($existingMetadata, JSON_PRETTY_PRINT));

        return $metadata;
    }

    /**
     * Get file metadata
     *
     * @param string $filename
     * @param string $disk
     * @return array|null
     */
    private function getFileMetadata(string $filename, string $disk): ?array
    {
        $metadataFile = storage_path("app/metadata/{$disk}_metadata.json");
        
        if (!file_exists($metadataFile)) {
            return null;
        }

        $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];
        return $metadata[$filename] ?? null;
    }

    /**
     * Remove file metadata
     *
     * @param string $filename
     * @param string $disk
     * @return void
     */
    private function removeFileMetadata(string $filename, string $disk): void
    {
        $metadataFile = storage_path("app/metadata/{$disk}_metadata.json");
        
        if (!file_exists($metadataFile)) {
            return;
        }

        $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];
        unset($metadata[$filename]);
        file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    }

    /**
     * Check if file is referenced in metadata
     *
     * @param string $filename
     * @param string $disk
     * @return bool
     */
    private function isFileReferenced(string $filename, string $disk): bool
    {
        $metadata = $this->getFileMetadata($filename, $disk);
        return $metadata !== null;
    }

    /**
     * Generate temporary URL for private files
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    private function generateTemporaryUrl(string $path, string $disk): string
    {
        // For now, return a route-based URL
        // In production, you might want to use signed URLs
        return route('files.show', ['disk' => $disk, 'path' => $path]);
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}