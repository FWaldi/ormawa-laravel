<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\StorageService;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'image',
        'is_pinned',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Get user who created announcement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get image URL with proper storage path
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // If it's a relative path starting with /storage, it's from public disk
        if (str_starts_with($this->image, '/storage/')) {
            return config('app.url') . $this->image;
        }

        // Otherwise, treat it as a filename in announcements disk
        $storageService = app(StorageService::class);
        return $storageService->getFileUrl($this->image, 'announcements');
    }

    /**
     * Get image path for storage operations
     *
     * @return string|null
     */
    public function getImagePathAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If it's a full URL or public storage path, extract filename
        if (filter_var($this->image, FILTER_VALIDATE_URL) || str_starts_with($this->image, '/storage/')) {
            return basename($this->image);
        }

        return $this->image;
    }

    /**
     * Delete image file when announcement is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($announcement) {
            if ($announcement->image) {
                $storageService = app(StorageService::class);
                $filename = $announcement->image_path;
                if ($filename) {
                    $storageService->deleteFile($filename, 'announcements');
                }
            }
        });
    }
}