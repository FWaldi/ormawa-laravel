<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed MIME types for image uploads
     */
    private static $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Allowed MIME types for document uploads
     */
    private static $allowedDocumentMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv'
    ];

    /**
     * Maximum file sizes in bytes
     */
    private static $maxFileSizes = [
        'images' => 5242880, // 5MB
        'documents' => 10485760, // 10MB
        'default' => 2097152 // 2MB
    ];

    /**
     * Securely upload and validate file
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $type
     * @return array|null
     */
    public static function secureUpload(UploadedFile $file, string $directory = 'news', string $type = 'images'): ?array
    {
        try {
            // Validate file
            if (!self::validateFile($file, $type)) {
                return null;
            }

            // Generate secure filename
            $filename = self::generateSecureFilename($file);
            
            // Store file securely
            $path = $file->storeAs($directory, $filename, 'public');
            
            if (!$path) {
                Log::error('Failed to store file', ['file' => $file->getClientOriginalName()]);
                return null;
            }

            // Perform additional security checks
            if (!self::performSecurityChecks($path)) {
                // Clean up if security checks fail
                Storage::disk('public')->delete($path);
                return null;
            }

            return [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Validate file against security rules
     *
     * @param UploadedFile $file
     * @param string $type
     * @return bool
     */
    private static function validateFile(UploadedFile $file, string $type = 'images'): bool
    {
        // Determine allowed MIME types based on file type
        $allowedMimes = self::$allowedMimeTypes;
        if ($type === 'documents') {
            $allowedMimes = array_merge($allowedMimes, self::$allowedDocumentMimeTypes);
        }

        // Check file size
        $maxSize = self::$maxFileSizes[$type] ?? self::$maxFileSizes['default'];
        if ($file->getSize() > $maxSize) {
            Log::warning('File upload rejected: size exceeded', [
                'size' => $file->getSize(),
                'max' => $maxSize,
                'type' => $type,
                'file' => $file->getClientOriginalName()
            ]);
            return false;
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            Log::warning('File upload rejected: invalid MIME type', [
                'mime' => $file->getMimeType(),
                'type' => $type,
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
                'type' => $type,
                'file' => $file->getClientOriginalName()
            ]);
            return false;
        }

        // Additional security: check for double extensions
        if (count(explode('.', $file->getClientOriginalName())) > 2) {
            Log::warning('File upload rejected: multiple extensions', [
                'filename' => $file->getClientOriginalName(),
                'type' => $type
            ]);
            return false;
        }

        return true;
    }

    /**
     * Generate secure filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private static function generateSecureFilename(UploadedFile $file): string
    {
        return time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Perform additional security checks on uploaded file
     *
     * @param string $path
     * @return bool
     */
    private static function performSecurityChecks(string $path): bool
    {
        $fullPath = Storage::disk('public')->path($path);
        
        // Check if file exists and is readable
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::error('Uploaded file not accessible', ['path' => $path]);
            return false;
        }

        // Get file info to verify it's actually an image
        $imageInfo = @getimagesize($fullPath);
        if ($imageInfo === false) {
            Log::error('File is not a valid image', ['path' => $path]);
            return false;
        }

        // Verify image signature
        if (!self::verifyImageSignature($fullPath, $imageInfo[2])) {
            Log::error('Invalid image signature', ['path' => $path]);
            return false;
        }

        // Basic virus scan (check for embedded PHP/JavaScript)
        if (self::containsMaliciousContent($fullPath)) {
            Log::error('Malicious content detected in uploaded file', ['path' => $path]);
            return false;
        }

        return true;
    }

    /**
     * Verify image signature to prevent file type spoofing
     *
     * @param string $filePath
     * @param int $imageType
     * @return bool
     */
    private static function verifyImageSignature(string $filePath, int $imageType): bool
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 8);
        fclose($handle);

        $signatures = [
            IMAGETYPE_JPEG => [0xFF, 0xD8, 0xFF],
            IMAGETYPE_PNG => [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A],
            IMAGETYPE_GIF => [0x47, 0x49, 0x46, 0x38],
            IMAGETYPE_WEBP => [0x52, 0x49, 0x46, 0x46]
        ];

        if (!isset($signatures[$imageType])) {
            return false;
        }

        $expectedSignature = $signatures[$imageType];
        for ($i = 0; $i < count($expectedSignature); $i++) {
            if (!isset($header[$i]) || ord($header[$i]) !== $expectedSignature[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Basic check for malicious content in image files
     *
     * @param string $filePath
     * @return bool
     */
    private static function containsMaliciousContent(string $filePath): bool
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
            'passthru(',
            'file_get_contents(',
            'file_put_contents(',
            'fopen(',
            'fwrite(',
            'curl_exec(',
            'assert(',
            'create_function(',
            'preg_replace.*\/e',
            'call_user_func(',
            'call_user_func_array(',
            '$_GET',
            '$_POST',
            '$_REQUEST',
            '$_COOKIE',
            '$_FILES'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Securely delete a file
     *
     * @param string $path
     * @return bool
     */
    public static function secureDelete(string $path): bool
    {
        try {
            return Storage::disk('public')->delete($path);
        } catch (\Exception $e) {
            Log::error('Error deleting file', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}