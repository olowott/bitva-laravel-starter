@props(['title', 'description' => null])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div>

        <h1 class="app-page-title">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="app-page-description">
                {{ $description }}
            </p>
        @endif

    </div>

    @if (isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif

</div>
