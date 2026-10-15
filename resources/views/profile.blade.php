@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Profile" subtitle="Update your account settings" />

        <div class="row">
            <div class="col-lg-8">
                <x-form-card title="Profile information" class="mb-3">
                    <livewire:profile.update-profile-information-form />
                </x-form-card>

                <x-form-card title="Update password" class="mb-3">
                    <livewire:profile.update-password-form />
                </x-form-card>

                <x-form-card title="Delete account">
                    <livewire:profile.delete-user-form />
                </x-form-card>
            </div>
        </div>
    </div>
</section>
@endsection
