@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

    <x-layout.page-header title="Activity Logs" description="Review important administrative and system activity." />

    <x-ui.filter-panel :action="route('admin.activity.index')" class="mt-6">

        <div class="md:col-span-2">
            <x-form.input name="search" label="Search" placeholder="Search activity description..." :value="request('search')" />
        </div>

        <div>
            <x-form.select name="event" label="Event">
                <option value="">
                    All events
                </option>

                @foreach ($events as $event)
                    <option value="{{ $event }}" @selected(request('event') === $event)>
                        {{ str($event)->replace('_', ' ')->title() }}
                    </option>
                @endforeach
            </x-form.select>
        </div>

        <div class="flex items-end gap-3 md:col-span-4">

            <x-ui.button type="submit">
                <x-heroicon-o-funnel class="mr-2 size-4" />
                Apply Filters
            </x-ui.button>

            @if (request()->filled('search') || request()->filled('event'))
                <a href="{{ route('admin.activity.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <x-heroicon-o-x-mark class="mr-2 size-4" />
                    Clear
                </a>
            @endif

        </div>

    </x-ui.filter-panel>


    <x-ui.active-filters class="mt-3" :filters="[
        'Search' => request('search'),
        'Event' => request('event') ? str(request('event'))->replace('_', ' ')->title() : null,
    ]" :clear-url="route('admin.activity.index')" />


    <div class="mt-6">

        <x-table.index :empty="$activities->isEmpty()" empty-title="No activity found"
            empty-description="Activity will appear here as administrative actions are performed.">

            <table class="min-w-full">

                <thead>
                    <x-table.header>

                        <x-table.head>
                            <x-table.sortable column="description" label="Activity" />
                        </x-table.head>

                        <x-table.head>
                            Actor
                        </x-table.head>

                        <x-table.head>
                            Subject
                        </x-table.head>

                        <x-table.head>
                            <x-table.sortable column="event" label="Event" />
                        </x-table.head>

                        <x-table.head>
                            <x-table.sortable column="created_at" label="Time" />
                        </x-table.head>

                        <x-table.head align="right">
                            Details
                        </x-table.head>

                    </x-table.header>
                </thead>


                <tbody>

                    @foreach ($activities as $activity)
                        <x-table.row>

                            <x-table.cell>

                                <div class="font-medium text-slate-900">
                                    {{ $activity->description }}
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    #{{ $activity->id }}
                                </div>

                            </x-table.cell>


                            <x-table.cell>

                                @if ($activity->causer)
                                    <div class="font-medium text-slate-900">
                                        {{ $activity->causer->name ?? 'User' }}
                                    </div>

                                    @if (!empty($activity->causer->email))
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $activity->causer->email }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400">
                                        System
                                    </span>
                                @endif

                            </x-table.cell>


                            <x-table.cell>

                                @if ($activity->subject)
                                    <div class="text-slate-900">
                                        {{ class_basename($activity->subject_type) }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        ID: {{ $activity->subject_id }}
                                    </div>
                                @else
                                    <span class="text-slate-400">
                                        —
                                    </span>
                                @endif

                            </x-table.cell>


                            <x-table.cell>

                                @php
                                    $eventVariant = match ($activity->event) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        default => 'neutral',
                                    };
                                @endphp

                                <x-ui.badge :variant="$eventVariant">
                                    {{ str($activity->event ?? 'activity')->replace('_', ' ')->title() }}
                                </x-ui.badge>

                            </x-table.cell>


                            <x-table.cell class="whitespace-nowrap">

                                <div class="text-slate-700">
                                    {{ $activity->created_at->format('d M Y') }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $activity->created_at->format('H:i') }}
                                </div>

                            </x-table.cell>


                            <x-table.cell align="right">

                                @if ($activity->properties->isNotEmpty())
                                    <details class="inline-block text-left">

                                        <summary
                                            class="cursor-pointer text-sm font-medium text-brand-600 transition hover:text-brand-700">
                                            View
                                        </summary>

                                        <div class="mt-3 w-80 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <pre class="whitespace-pre-wrap break-words text-xs text-slate-600">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>

                                    </details>
                                @else
                                    <span class="text-slate-400">
                                        —
                                    </span>
                                @endif

                            </x-table.cell>

                        </x-table.row>
                    @endforeach

                </tbody>

            </table>


            <x-slot:footer>
                <x-table.footer :paginator="$activities" />
            </x-slot:footer>

        </x-table.index>

    </div>

@endsection
