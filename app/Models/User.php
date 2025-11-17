<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case KEMAHASISWAAN = 'KEMAHASISWAAN';
    case ORMAWA = 'ORMAWA';
    case USER = 'USER';
}

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'organization_id',
        'is_email_verified',
        'email_verification_code',
        'email_verification_expires',
        'password_reset_token',
        'password_reset_expires',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_expires' => 'datetime',
            'password_reset_expires' => 'datetime',
            'is_email_verified' => 'boolean',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the organization that the user belongs to.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the activities created by the user.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    /**
     * Get the announcements created by the user.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get the news articles created by the user.
     */
    public function news()
    {
        return $this->hasMany(News::class, 'created_by');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user is an organization admin.
     */
    public function isOrgAdmin(): bool
    {
        return $this->role === UserRole::ORG_ADMIN;
    }

    /**
     * Check if user is an organization admin for a specific organization.
     */
    public function isOrgAdminForOrganization(int $organizationId): bool
    {
        return $this->role === UserRole::ORG_ADMIN && $this->organization_id === $organizationId;
    }

    /**
     * Check if user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /**
     * Check if user can manage organizations.
     */
    public function canManageOrganizations(): bool
    {
        return $this->isAdmin() || $this->isOrgAdmin();
    }
}
