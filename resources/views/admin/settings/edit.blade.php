@extends('layouts.app')

@section('title', 'Settings')

@section('content')

    <x-layout.page-header title="Settings" description="Manage application, company and branding settings." />



    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- General Settings --}}
        <x-ui.card>

            <div class="border-b border-slate-200 pb-5 dark:border-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    General
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Basic information about the application and organisation.
                </p>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">

                <x-form.input name="app_name" label="Application Name" :value="setting('app_name')" required />

                <x-form.input name="app_tagline" label="Application Tagline" :value="setting('app_tagline')" />

                <x-form.input name="company_name" label="Company Name" :value="setting('company_name')" />

                <x-form.input name="company_email" type="email" label="Company Email" :value="setting('company_email')" />

                <x-form.input name="company_phone" label="Company Phone" :value="setting('company_phone')" />

                <div>


                    <x-form.select name="timezone" label="Timezone" required>
                        <option value="">
                            Select timezone
                        </option>

                        @foreach ($timezones as $timezone)
                            <option value="{{ $timezone }}" @selected(old('timezone', setting('timezone', 'Africa/Lagos')) === $timezone)>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.error :messages="$errors->get('timezone')" />
                </div>

            </div>

        </x-ui.card>


        {{-- Branding --}}
        <x-ui.card>

            <div
                class="mt-8 grid gap-6 border-t border-slate-200 pt-6
    dark:border-slate-800
    md:col-span-2 md:grid-cols-2">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    Branding
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Configure the application's core brand colours.
                </p>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">

                <div>
                    <x-form.label for="primary_color" value="Primary Colour" />

                    <div class="mt-1 flex items-center gap-3">

                        <input id="primary_color_picker" type="color"
                            value="{{ old('primary_color', setting('primary_color', '#4f5bd5')) }}"
                            class="h-11 w-14 cursor-pointer rounded-lg border
    border-slate-300 bg-white p-1
    dark:border-slate-700 dark:bg-slate-900"
                            oninput="document.getElementById('primary_color').value = this.value">

                        <input id="primary_color" name="primary_color" type="text"
                            value="{{ old('primary_color', setting('primary_color', '#4f5bd5')) }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white
    px-3 py-2.5 text-sm text-slate-900 shadow-sm
    focus:border-brand-500 focus:outline-none focus:ring-2
    focus:ring-brand-500/20
    dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            oninput="document.getElementById('primary_color_picker').value = this.value">

                    </div>

                    <x-form.error :messages="$errors->get('primary_color')" />
                </div>


                <div>
                    <x-form.label for="secondary_color" value="Secondary Colour" />

                    <div class="mt-1 flex items-center gap-3">

                        <input id="secondary_color_picker" type="color"
                            value="{{ old('secondary_color', setting('secondary_color', '#111827')) }}"
                            class="h-11 w-14 cursor-pointer rounded-lg border border-slate-300 bg-white p-1"
                            oninput="document.getElementById('secondary_color').value = this.value">

                        <input id="secondary_color" name="secondary_color" type="text"
                            value="{{ old('secondary_color', setting('secondary_color', '#111827')) }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                            oninput="document.getElementById('secondary_color_picker').value = this.value">

                    </div>

                    <x-form.error :messages="$errors->get('secondary_color')" />
                </div>

                <div class="mt-8 grid gap-6 border-t border-slate-200 pt-6 md:col-span-2 md:grid-cols-2">

                    {{-- Logo --}}
                    <div>
                        <x-form.label for="logo" value="Application Logo" />

                        @if (setting('logo'))
                            <div
                                class="mt-2 flex min-h-24 items-center justify-center rounded-xl border
    border-slate-200 bg-slate-50 p-4
    dark:border-slate-800 dark:bg-slate-950">
                                <img src="{{ Storage::disk('public_assets')->url(setting('logo')) }}"
                                    alt="{{ setting('app_name') }} logo" class="max-h-16 max-w-full object-contain">
                            </div>

                            @can('settings.manage')
                                <button type="submit" form="remove-logo-form"
                                    class="mt-3 inline-flex items-center text-sm font-medium
    text-red-600 transition hover:text-red-700
    dark:text-red-400 dark:hover:text-red-300">
                                    <x-heroicon-o-trash class="mr-2 size-4" />

                                    Remove Logo
                                </button>
                            @endcan
                        @endif

                        <x-form.file name="logo" label="Application Logo" accept=".jpg,.jpeg,.png,.webp" />

                        <p class="mt-2 text-xs text-slate-500">
                            PNG, JPG or WebP. Maximum 2 MB.
                        </p>

                        <x-form.error :messages="$errors->get('logo')" />
                    </div>


                    {{-- Favicon --}}
                    <div>
                        <x-form.label for="favicon" value="Favicon" />

                        @if (setting('favicon'))
                            <div
                                class="mt-2 flex min-h-24 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <img src="{{ Storage::disk('public_assets')->url(setting('favicon')) }}" alt="Favicon"
                                    class="size-12 object-contain">
                            </div>

                            @can('settings.manage')
                                <button type="submit" form="remove-favicon-form"
                                    class="mt-3 inline-flex items-center text-sm font-medium text-red-600 transition hover:text-red-700">
                                    <x-heroicon-o-trash class="mr-2 size-4" />

                                    Remove Favicon
                                </button>
                            @endcan
                        @endif

                        <x-form.file name="favicon" label="Favicon" accept=".png" />

                        <p class="mt-2 text-xs text-slate-500">
                            PNG recommended. Maximum 512 KB.
                        </p>

                        <x-form.error :messages="$errors->get('favicon')" />
                    </div>

                </div>
            </div>

        </x-ui.card>


        @can('settings.manage')
            <div class="flex justify-end">
                <x-ui.button type="submit">
                    <x-heroicon-o-check class="mr-2 size-4" />
                    Save Settings
                </x-ui.button>
            </div>
        @endcan

    </form>

    @can('settings.manage')

        @if (setting('logo'))
            <form id="remove-logo-form" method="POST" action="{{ route('admin.settings.logo.destroy') }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif


        @if (setting('favicon'))
            <form id="remove-favicon-form" method="POST" action="{{ route('admin.settings.favicon.destroy') }}"
                class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif

    @endcan

@endsection
