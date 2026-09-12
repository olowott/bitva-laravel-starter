<?php

namespace App\Concerns;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(
            Document::class,
            'documentable'
        );
    }
}
