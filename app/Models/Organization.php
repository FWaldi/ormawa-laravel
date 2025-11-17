<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'logo',
        'contact',
        'social_media',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_media' => 'array',
        ];
    }

    /**
     * Get the logo URL with proper storage path
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // If it's a relative path starting with /storage, it's from the public disk
        if (str_starts_with($this->logo, '/storage/')) {
            return config('app.url') . $this->logo;
        }

        // Otherwise, treat it as a filename in the organizations disk
        $storageService = app(StorageService::class);
        return $storageService->getFileUrl($this->logo, 'organizations');
    }

    /**
     * Get the logo path for storage operations
     *
     * @return string|null
     */
    public function getLogoPathAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        // If it's a full URL or public storage path, extract the filename
        if (filter_var($this->logo, FILTER_VALIDATE_URL) || str_starts_with($this->logo, '/storage/')) {
            return basename($this->logo);
        }

        return $this->logo;
    }

    /**
     * Delete the logo file when organization is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($organization) {
            if ($organization->logo) {
                $storageService = app(StorageService::class);
                $filename = $organization->logo_path;
                if ($filename) {
                    $storageService->deleteFile($filename, 'organizations');
                }
            }
        });
    }

    /**
     * Get the members of the organization.
     */
    public function members()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the activities for the organization.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the news articles for the organization.
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }
}