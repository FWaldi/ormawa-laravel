<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StorageService;

class News extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'image',
        'organization_id',
        'is_published',
        'published_at',
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
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the organization that owns the news.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get user who created news.
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

        // Otherwise, treat it as a filename in news disk
        $storageService = app(StorageService::class);
        return $storageService->getFileUrl($this->image, 'news');
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
     * Delete image file when news is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($news) {
            if ($news->image) {
                $storageService = app(StorageService::class);
                $filename = $news->image_path;
                if ($filename) {
                    $storageService->deleteFile($filename, 'news');
                }
            }
        });
    }
}
}