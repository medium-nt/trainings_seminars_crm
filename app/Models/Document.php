<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table = 'user_documents';

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeTitle(): string
    {
        return match ($this->type) {
            'passport_main' => 'Паспорт (основная страница)',
            'passport_reg' => 'Паспорт (прописка)',
            'snils' => 'СНИЛС',
            'diploma_basis' => 'Документ-основание для диплома',
            'contract' => 'Договор',
            'personal_data_consent' => 'Согласие на обработку ПД',
            'name_change_document' => 'Документ о смене фамилии',
            default => 'Другой',
        };
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function url(): string
    {
        return Storage::disk('local')->url($this->file_path);
    }

    protected static function booted(): void
    {
        static::deleted(function (Document $document) {
            Storage::disk('local')->delete($document->file_path);
        });
    }
}
