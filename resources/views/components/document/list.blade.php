@props(['documents'])

<div class="space-y-3">

    @forelse ($documents as $document)
        <div
            class="flex flex-col gap-3 rounded-xl border border-slate-200
    bg-white px-4 py-3
    dark:border-slate-800 dark:bg-slate-900
    sm:flex-row sm:items-center sm:justify-between">

            <div class="min-w-0">

                <div class="flex items-center gap-2">

                    <x-heroicon-o-document class="size-5 shrink-0 text-slate-400" />

                    <p class="truncate text-sm font-medium text-slate-900 dark:text-white">
                        {{ $document->original_name }}
                    </p>

                </div>

                <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500">

                    @if ($document->category)
                        <span>
                            {{ str($document->category)->replace('_', ' ')->title() }}
                        </span>
                    @endif

                    <span>
                        {{ number_format($document->size / 1024, 1) }} KB
                    </span>

                    <span>
                        {{ $document->created_at->format('d M Y') }}
                    </span>

                </div>

            </div>

            <div class="flex items-center gap-3">

                @can('documents.download')
                    <a href="{{ route('admin.documents.download', $document) }}"
                        class="text-sm font-medium text-brand-600 transition hover:text-brand-700">
                        Download
                    </a>
                @endcan


                @can('documents.delete')
                    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}"
                        onsubmit="return confirm('Delete this document?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="text-sm font-medium text-red-600 transition hover:text-red-700">
                            Delete
                        </button>

                    </form>
                @endcan

            </div>

        </div>

    @empty

        <div class="rounded-xl border border-dashed border-slate-300 px-6 py-10 text-center">
            <x-heroicon-o-document class="mx-auto size-8 text-slate-300" />

            <p class="mt-3 text-sm font-medium text-slate-900 dark:text-white">
                No documents uploaded
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Uploaded documents will appear here.
            </p>
        </div>
    @endforelse

</div>
