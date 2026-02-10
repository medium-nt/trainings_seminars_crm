<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'payment_date',
        'amount',
        'receipt_path',
        'receipt_name',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Связь с пользователем (студентом)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с группой
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Номер платежа для группы (1, 2, 3)
     */
    public function getPaymentNumberAttribute(): int
    {
        return self::where('user_id', $this->user_id)
            ->where('group_id', $this->group_id)
            ->where('id', '<=', $this->id)
            ->count();
    }

    /**
     * Scope для фильтрации по пользователю
     */
    public function scopeFilterByUser(Builder $query, ?int $userId): Builder
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }

        return $query;
    }

    /**
     * Scope для фильтрации по группе
     */
    public function scopeFilterByGroup(Builder $query, ?int $groupId): Builder
    {
        if ($groupId) {
            return $query->where('group_id', $groupId);
        }

        return $query;
    }
}
