<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_IT_DEV = 'it_dev';
    public const ROLE_IT_TEST = 'it_test';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUSPENDED = 'suspended';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'password',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Built-in role options for the frontend.
     *
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_IT_DEV => 'IT DEV',
            self::ROLE_IT_TEST => 'IT DEV (Read Only)',
        ];
    }

    /**
     * Built-in status options for the frontend.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    /**
     * Normalize a role value into a safe label.
     */
    public static function roleLabel(?string $role): string
    {
        $role = trim((string) $role);

        if ($role === '') {
            return 'Unknown';
        }

        return static::roleLabels()[$role] ?? $role;
    }

    /**
     * Normalize a status value into a safe label.
     */
    public static function statusLabel(?string $status): string
    {
        $status = trim((string) $status);

        if ($status === '') {
            return 'Unknown';
        }

        return static::statusLabels()[$status] ?? $status;
    }

    /**
     * Check whether the user has the given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isItDev(): bool
    {
        return $this->hasRole(self::ROLE_IT_DEV);
    }

    public function isItTest(): bool
    {
        return $this->hasRole(self::ROLE_IT_TEST);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Resolve frontend permissions from the current role.
     *
     * @return array<string, bool>
     */
    public function permissions(): array
    {
        if ($this->isAdmin()) {
            return [
                'view-dashboard' => true,
                'view-projects' => true,
                'view-companies' => true,
                'view-reports' => true,
                'manage-users' => true,
                'manage-companies' => true,
                'manage-projects' => true,
                'manage-tasks' => true,
                'manage-trainings' => true,
                'manage-requests' => true,
                'manage-settings' => true,
            ];
        }

        if ($this->isItDev()) {
            return [
                'view-dashboard' => true,
                'view-projects' => true,
                'view-companies' => true,
                'view-reports' => true,
                'manage-users' => false,
                'manage-companies' => true,
                'manage-projects' => true,
                'manage-tasks' => true,
                'manage-trainings' => true,
                'manage-requests' => true,
                'manage-settings' => false,
            ];
        }

        return [
            'view-dashboard' => true,
            'view-projects' => true,
            'view-companies' => true,
            'view-reports' => true,
            'manage-users' => false,
            'manage-companies' => false,
            'manage-projects' => false,
            'manage-tasks' => false,
            'manage-trainings' => false,
            'manage-requests' => false,
            'manage-settings' => false,
        ];
    }

    /**
     * Check a named frontend ability.
     */
    public function hasPermission(string $ability): bool
    {
        return $this->permissions()[$ability] ?? false;
    }

    /**
     * Get projects owned by the user.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
