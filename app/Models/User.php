<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        'is_blocked',
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
            'is_blocked' => 'boolean',
        ];
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, ['last_name', 'name', 'patronymic']) && is_string($value)) {
            $value = $this->capitalizeName($value);
        }

        return parent::setAttribute($key, $value);
    }

    private function capitalizeName(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value);

        return mb_strtoupper(mb_substr($value, 0, 1)).mb_substr($value, 1);
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

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
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

    public static function searchClients(string $search = '', ?int $groupId = null): Builder
    {
        $role = Role::where('name', 'client')->first();
        $query = $role->users()->getQuery();

        if ($search) {
            $words = array_filter(explode(' ', trim($search)));

            foreach ($words as $word) {
                $word = mb_strtolower($word);
                $word = mb_strtoupper(mb_substr($word, 0, 1)).mb_substr($word, 1);

                $query->where(function ($q) use ($word) {
                    $q->where('name', 'like', "%$word%")
                        ->orWhere('last_name', 'like', "%$word%")
                        ->orWhere('patronymic', 'like', "%$word%")
                        ->orWhere('email', 'like', "%$word%")
                        ->orWhere('phone', 'like', "%$word%");
                });
            }
        }

        if ($groupId) {
            $query->whereHas('studentGroups', function ($q) use ($groupId) {
                $q->where('groups.id', $groupId);
            });
        }

        return $query->limit(50);
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

    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
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
            return trim(($this->last_name ?? '').' '.($this->name ?? '').' '.($this->patronymic ?? ''));
        });
    }
}
