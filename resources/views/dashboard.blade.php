@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-layout.page-header title="Dashboard" description="Overview of your application." />

    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500">
                Total Users
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ \App\Models\User::count() }}
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500">
                Active
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                0
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500">
                Pending
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                0
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-slate-500">
                This Month
            </p>

            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                0
            </p>
        </x-ui.card>

    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">

        <x-ui.card class="xl:col-span-2">
            <h2 class="text-base font-semibold text-slate-900">
                Getting Started
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                This application is powered by the BitVa Laravel Starter.
                Project modules will appear here as they are enabled.
            </p>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-900">
                System
            </h2>

            <dl class="mt-4 space-y-3 text-sm">

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Laravel</dt>
                    <dd class="font-medium text-slate-900">
                        {{ app()->version() }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">PHP</dt>
                    <dd class="font-medium text-slate-900">
                        {{ PHP_VERSION }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Environment</dt>
                    <dd class="font-medium text-slate-900">
                        {{ app()->environment() }}
                    </dd>
                </div>

            </dl>
        </x-ui.card>

    </div>

@endsection
