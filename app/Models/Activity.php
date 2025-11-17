<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StorageService;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'organization_id',
        'start_date',
        'end_date',
        'location',
        'images',
        'status',
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
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'images' => 'array',
            'status' => 'string',
        ];
    }

    /**
     * Get the human-readable status.
     */
    public function getStatusAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Set the status attribute.
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtoupper($value);
    }

    /**
     * Get the organization that owns the activity.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who created the activity.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get image URLs with proper storage paths
     *
     * @return array
     */
    public function getImageUrlsAttribute(): array
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        $storageService = app(StorageService::class);
        $urls = [];

        foreach ($this->images as $image) {
            if (is_string($image)) {
                // If it's already a full URL, use it
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    $urls[] = $image;
                } 
                // If it's a relative path starting with /storage, it's from public disk
                elseif (str_starts_with($image, '/storage/')) {
                    $urls[] = config('app.url') . $image;
                } 
                // Otherwise, treat it as a filename in activities disk
                else {
                    $url = $storageService->getFileUrl($image, 'activities');
                    if ($url) {
                        $urls[] = $url;
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * Get image filenames for storage operations
     *
     * @return array
     */
    public function getImageFilenamesAttribute(): array
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        $filenames = [];

        foreach ($this->images as $image) {
            if (is_string($image)) {
                // If it's a full URL or public storage path, extract filename
                if (filter_var($image, FILTER_VALIDATE_URL) || str_starts_with($image, '/storage/')) {
                    $filenames[] = basename($image);
                } else {
                    $filenames[] = $image;
                }
            }
        }

        return $filenames;
    }

    /**
     * Add an image to the activity
     *
     * @param string $filename
     * @return void
     */
    public function addImage(string $filename): void
    {
        $images = $this->images ?? [];
        $images[] = $filename;
        $this->images = $images;
        $this->save();
    }

    /**
     * Remove an image from the activity
     *
     * @param string $filename
     * @return bool
     */
    public function removeImage(string $filename): bool
    {
        if (!$this->images || !is_array($this->images)) {
            return false;
        }

        $key = array_search($filename, $this->images);
        if ($key !== false) {
            unset($this->images[$key]);
            $this->images = array_values($this->images); // Re-index array
            $this->save();

            // Delete the actual file
            $storageService = app(StorageService::class);
            return $storageService->deleteFile($filename, 'activities');
        }

        return false;
    }

    /**
     * Delete all image files when activity is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($activity) {
            if ($activity->images && is_array($activity->images)) {
                $storageService = app(StorageService::class);
                foreach ($activity->image_filenames as $filename) {
                    $storageService->deleteFile($filename, 'activities');
                }
            }
        });
    }
}