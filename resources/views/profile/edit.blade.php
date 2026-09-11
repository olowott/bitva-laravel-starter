@extends('layouts.app')

@section('title', 'Profile')

@section('content')

    <x-layout.page-header title="Profile" description="Manage your account information and security settings." />

    <div class="mt-6 space-y-6">

        <x-ui.card>
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-ui.card>

    </div>

@endsection
