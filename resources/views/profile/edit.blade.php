@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-layout.page-header title="My Profile"
        description="Manage your personal information, profile photo and account security." />

    <div class="mt-6 space-y-6">

        {{-- Profile Information --}}
        <x-ui.card>
            @include('profile.partials.update-profile-information-form')
        </x-ui.card>

        {{-- Password --}}
        <x-ui.card>

            @include('profile.partials.update-password-form')

        </x-ui.card>

        {{-- Delete Account --}}
        @unless (auth()->user()->hasRole('super_admin'))
            <x-ui.card>

                @include('profile.partials.delete-user-form')

            </x-ui.card>
        @endunless

    </div>
@endsection
