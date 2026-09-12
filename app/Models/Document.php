<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'uploaded_by',
        'category',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'size' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1024 * 1024) {
            return number_format(
                $bytes / (1024 * 1024),
                1
            ) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format(
                $bytes / 1024,
                1
            ) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(
            pathinfo(
                $this->original_name,
                PATHINFO_EXTENSION
            )
        );
    }
}
