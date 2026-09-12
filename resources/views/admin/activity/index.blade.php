@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

    <x-layout.page-header title="Activity Logs" description="Review important administrative and system activity." />

    <div class="mt-6 space-y-6">

        <x-ui.filter-panel :action="route('admin.activity.index')">

            <div class="md:col-span-2">
                <x-form.input name="search" label="Search" :value="request('search')" placeholder="Search activity description..." />
            </div>

            <x-form.select name="event" label="Event">
                <option value="">
                    All events
                </option>

                @foreach ($events as $event)
                    <option value="{{ $event }}" @selected(request('event') === $event)>
                        {{ ucfirst($event) }}
                    </option>
                @endforeach
            </x-form.select>

            <div class="flex items-end gap-2">

                <x-ui.button type="submit">
                    <x-heroicon-o-funnel class="mr-2 size-4" />
                    Filter
                </x-ui.button>

                @if (request()->hasAny(['search', 'event']))
                    <a href="{{ route('admin.activity.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Clear
                    </a>
                @endif

            </div>

        </x-ui.filter-panel>


        <x-ui.card :padding="false">

            @if ($activities->isEmpty())

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <x-heroicon-o-clock class="size-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                        No activity found
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Activity will appear here as administrative actions are performed.
                    </p>

                </div>
            @else
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Activity
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Actor
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Subject
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Event
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Time
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Details
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">

                            @foreach ($activities as $activity)
                                <tr class="align-top hover:bg-slate-50/50">

                                    <td class="px-6 py-4">

                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $activity->description }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            #{{ $activity->id }}
                                        </p>

                                    </td>


                                    <td class="px-6 py-4">

                                        @if ($activity->causer)
                                            <p class="text-sm font-medium text-slate-900">
                                                {{ $activity->causer->name ?? 'User' }}
                                            </p>

                                            @if (!empty($activity->causer->email))
                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $activity->causer->email }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-sm text-slate-400">
                                                System
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4">

                                        @if ($activity->subject)
                                            <p class="text-sm text-slate-900">
                                                {{ class_basename($activity->subject_type) }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                ID: {{ $activity->subject_id }}
                                            </p>
                                        @else
                                            <span class="text-sm text-slate-400">
                                                —
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4">

                                        @php
                                            $eventVariant = match ($activity->event) {
                                                'created' => 'success',
                                                'updated' => 'info',
                                                'deleted' => 'danger',
                                                default => 'neutral',
                                            };
                                        @endphp

                                        <x-ui.badge :variant="$eventVariant">
                                            {{ ucfirst($activity->event ?? 'activity') }}
                                        </x-ui.badge>

                                    </td>


                                    <td class="whitespace-nowrap px-6 py-4">

                                        <p class="text-sm text-slate-700">
                                            {{ $activity->created_at->format('d M Y') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $activity->created_at->format('H:i') }}
                                        </p>

                                    </td>


                                    <td class="px-6 py-4 text-right">

                                        @if ($activity->properties->isNotEmpty())
                                            <details class="inline-block text-left">

                                                <summary
                                                    class="cursor-pointer text-sm font-medium text-brand-600 hover:text-brand-700">
                                                    View
                                                </summary>

                                                <div
                                                    class="mt-3 w-80 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                                    <pre class="whitespace-pre-wrap break-words text-xs text-slate-600">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>

                                            </details>
                                        @else
                                            <span class="text-sm text-slate-400">
                                                —
                                            </span>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $activities->links() }}
                </div>

            @endif

        </x-ui.card>

    </div>

@endsection
