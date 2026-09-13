<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentService
{
    public function store(
        UploadedFile $file,
        ?Model $documentable = null,
        ?string $category = null,
        array $metadata = []
    ): Document {
        $disk = 'local';

        $directory = $this->directory(
            $documentable,
            $category
        );

        $extension = $file->extension();

        $filename = (string) Str::uuid();

        if ($extension) {
            $filename .= '.' . $extension;
        }

        $path = $file->storeAs(
            $directory,
            $filename,
            $disk
        );

        if (!$path) {
            throw new \RuntimeException(
                'The document could not be stored.'
            );
        }

        try {
            $document = new Document();

            $document->documentable_type =
                $documentable?->getMorphClass();

            $document->documentable_id =
                $documentable?->getKey();

            $document->uploaded_by = auth()->id();

            $document->category = $category;

            $document->disk = $disk;
            $document->path = $path;

            $document->original_name =
                $this->sanitizeOriginalName(
                    $file->getClientOriginalName()
                );

            $document->mime_type = $file->getMimeType();
            $document->size = $file->getSize();

            $document->metadata =
                $metadata ?: null;

            $document->save();

            return $document;
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);

            throw $exception;
        }
    }

    public function delete(Document $document): void
    {
        if ($document->disk === 'local') {
            $disk = Storage::disk('local');

            if ($disk->exists($document->path)) {
                $disk->delete($document->path);
            }
        }

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
            $categorySlug = Str::slug($category);

            if ($categorySlug !== '') {
                $parts[] = $categorySlug;
            }
        }

        return implode('/', $parts);
    }

    protected function sanitizeOriginalName(string $name): string
    {
        $name = basename($name);

        return preg_replace(
            '/[\x00-\x1F\x7F]/u',
            '',
            $name
        ) ?: 'document';
    }
}
