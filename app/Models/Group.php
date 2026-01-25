<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = ['title', 'course_id', 'teacher_id', 'start_date', 'end_date', 'note', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'client_id')
            ->withTimestamps()
            ->whereHas('role', fn($q) => $q->where('name', 'client'));
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
