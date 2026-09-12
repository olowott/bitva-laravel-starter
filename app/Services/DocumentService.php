<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function store(
        UploadedFile $file,
        ?Model $documentable = null,
        ?string $category = null,
        string $disk = 'local',
        array $metadata = []
    ): Document {
        $directory = $this->directory(
            $documentable,
            $category
        );

        $filename = Str::uuid()
            . '.'
            . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            $directory,
            $filename,
            $disk
        );

        return Document::create([
            'documentable_type' => $documentable?->getMorphClass(),
            'documentable_id' => $documentable?->getKey(),

            'uploaded_by' => auth()->id(),

            'category' => $category,

            'disk' => $disk,
            'path' => $path,

            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),

            'metadata' => $metadata ?: null,
        ]);
    }

    public function delete(Document $document): void
    {
        Storage::disk($document->disk)
            ->delete($document->path);

        $document->delete();
    }

    protected function directory(
        ?Model $documentable,
        ?string $category
    ): string {
        $parts = ['documents'];

        if ($documentable) {
            $parts[] = Str::kebab(
                class_basename($documentable)
            );

            $parts[] = $documentable->getKey();
        } else {
            $parts[] = 'general';
        }

        if ($category) {
            $parts[] = Str::kebab($category);
        }

        return implode('/', $parts);
    }
}
