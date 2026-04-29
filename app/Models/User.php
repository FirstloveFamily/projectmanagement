<?php

namespace App\Models;

// 1. เพิ่มการเรียกใช้ Class ที่จำเป็นสำหรับ Filament
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 2. เพิ่ม implements FilamentUser เข้าไป
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // 3. ตอนนี้ Panel จะถูกเรียกใช้ได้อย่างถูกต้องแล้ว
    public function canAccessPanel(Panel $panel): bool
    {
        // บังคับ true ไว้ก่อนเพื่อให้ผ่าน 403
        return true;
    }

    protected $fillable = [
        'name',
        'email',
        'department',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}