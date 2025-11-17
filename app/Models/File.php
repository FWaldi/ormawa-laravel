<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'disk',
        'context',
        'context_id',
        'uploaded_by',
    ];

    /**
     * Get the user who uploaded the file.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the organization if this file belongs to an organization.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'context_id')
                    ->where('context', 'organization');
    }

    /**
     * Get the activity if this file belongs to an activity.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'context_id')
                    ->where('context', 'activity');
    }

    /**
     * Get the news if this file belongs to news.
     */
    public function news()
    {
        return $this->belongsTo(News::class, 'context_id')
                    ->where('context', 'news');
    }

    /**
     * Get the announcement if this file belongs to an announcement.
     */
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'context_id')
                    ->where('context', 'announcement');
    }

    /**
     * Get the file URL.
     */
    public function getUrlAttribute()
    {
        $storageService = app(\App\Services\StorageService::class);
        return $storageService->getFileUrl($this->filename, $this->disk);
    }

    /**
     * Get the human-readable file size.
     */
    public function getHumanSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file is an image.
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the file is a document.
     */
    public function isDocument()
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ]);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Scope a query to only include files for a specific context.
     */
    public function scopeForContext($query, $context, $contextId = null)
    {
        $query->where('context', $context);
        
        if ($contextId !== null) {
            $query->where('context_id', $contextId);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include image files.
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope a query to only include document files.
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ]);
    }
}