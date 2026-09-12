<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function store(
        StoreDocumentRequest $request,
        DocumentService $documentService,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        $user = User::findOrFail(
            $request->integer('user_id')
        );

        $document = $documentService->store(
            file: $request->file('document'),
            documentable: $user,
            category: $request->string('category')->toString() ?: null,
        );

        $activityLogService->log(
            'Document uploaded',
            $document,
            [
                'after' => [
                    'name' => $document->original_name,
                    'category' => $document->category,
                    'size' => $document->size,
                    'documentable_type' => $document->documentable_type,
                    'documentable_id' => $document->documentable_id,
                ],
            ],
            'created'
        );

        return back()->with(
            'success',
            'Document uploaded successfully.'
        );
    }

    public function download(
        Request $request,
        Document $document
    ): StreamedResponse {
        abort_unless(
            $request->user()->can('documents.download'),
            403
        );

        abort_unless(
            \Storage::disk($document->disk)->exists($document->path),
            404
        );

        return \Storage::disk($document->disk)->download(
            $document->path,
            $document->original_name
        );
    }

    public function destroy(
        Request $request,
        Document $document,
        DocumentService $documentService,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('documents.delete'),
            403
        );

        $activityLogService->log(
            'Document deleted',
            $document,
            [
                'before' => [
                    'name' => $document->original_name,
                    'category' => $document->category,
                    'size' => $document->size,
                    'documentable_type' => $document->documentable_type,
                    'documentable_id' => $document->documentable_id,
                ],
            ],
            'deleted'
        );

        $documentService->delete($document);

        return back()->with(
            'success',
            'Document deleted successfully.'
        );
    }
}
