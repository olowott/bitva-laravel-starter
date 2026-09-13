@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-layout.page-header title="Dashboard" description="Overview of your application." />

    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Total Users
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
                {{ \App\Models\User::count() }}
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Active
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
                0
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Pending
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
                0
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                This Month
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
                0
            </p>
        </x-ui.card>

    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">

        <x-ui.card class="xl:col-span-2">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                Getting Started
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                This application is powered by the BitVa Laravel Starter.
                Project modules will appear here as they are enabled.
            </p>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                System
            </h2>

            <dl class="mt-4 space-y-3 text-sm">

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500 dark:text-slate-400">Laravel</dt>
                    <dd class="font-medium text-slate-900 dark:text-white">
                        {{ app()->version() }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500 dark:text-slate-400">PHP</dt>
                    <dd class="font-medium text-slate-900 dark:text-white">
                        {{ PHP_VERSION }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500 dark:text-slate-400">Environment</dt>
                    <dd class="font-medium text-slate-900 dark:text-white">
                        {{ app()->environment() }}
                    </dd>
                </div>

            </dl>
        </x-ui.card>


    </div>

    {{-- MODAL BUTTON --}}


    <x-ui.button type="button" x-data @click="$dispatch('open-modal', 'delete-user')" variant="danger" class="mt-6">
        Modal Button
    </x-ui.button>

    {{-- MODAL  --}}
    <x-ui.modal name="delete-user" title="Modal Button">
        <p class="text-sm text-slate-600 dark:text-slate-300">
            Are you sure you want to delete this user?
            This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end gap-3">

            <x-ui.button type="button" variant="secondary" x-data @click="$dispatch('close-modal', 'delete-user')">
                Cancel
            </x-ui.button>

            <form method="POST" action="#">
                @csrf
                @method('DELETE')

                <x-ui.button type="submit" variant="danger">
                    Delete User
                </x-ui.button>
            </form>

        </div>
    </x-ui.modal>

@endsection
