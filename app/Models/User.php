<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
 use Illuminate\Database\Eloquent\Casts\Attribute;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'last_name',
        'name',
        'patronymic',
        'email',
        'phone',
        'password',
        'role_id',
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

    public function adminlte_profile_url(): string
    {
        return url('/profile');
    }

    public function adminlte_desc(): string
    {
        $user = auth()->user();

        return $user->name;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public static function clients()
    {
        return Role::where('name', 'client')
            ->first()
            ->users;
    }

    public static function managers()
    {
        return Role::where('name', 'manager')
            ->first()
            ->users;
    }

    public static function teachers()
    {
        return Role::where('name', 'teacher')
            ->first()
            ->users;
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role?->name === 'client';
    }

    public function isManager(): bool
    {
        return $this->role?->name === 'manager';
    }

    public function isTeacher(): bool
    {
        return $this->role?->name === 'teacher';
    }

    public function roleName(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->role?->name) {
                'client' => 'Клиент',
                'manager' => 'Менеджер',
                'admin' => 'Админ',
                'teacher' => 'Преподаватель',
                default => '---',
            };
        });
    }

}
