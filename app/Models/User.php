<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
 use Illuminate\Database\Eloquent\Casts\Attribute;
 use Illuminate\Database\Eloquent\Collection;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Database\Eloquent\Relations\BelongsToMany;
 use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'teacher_id');
    }

    public function studentGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'client_id')->withTimestamps();
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

    public static function searchClients(string $search = ''): Collection
    {
        $role = Role::where('name', 'client')->first();
        $query = $role->users()->getQuery();

        if ($search) {
            $search = mb_strtolower($search);
            $search = mb_strtoupper(mb_substr($search, 0, 1)) . mb_substr($search, 1);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('patronymic', 'like', "%$search%");
            });
        }

        return $query->limit(50)->get();
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

    public function fullName(): Attribute
    {
        return Attribute::get(function () {
            return trim(($this->last_name ?? '') . ' ' . ($this->name ?? '') . ' ' . ($this->patronymic ?? ''));
        });
    }

}
