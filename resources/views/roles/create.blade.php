@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Create Role" subtitle="Define a role and its permissions" />

        <x-form-card>
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">Role Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    @php
                        $permissionGroups = [
                            'System' => ['view_dashboard', 'manage_system_settings', 'view_activity_log'],
                            'Roles' => ['view_roles', 'create_roles', 'edit_roles', 'delete_roles'],
                            'Users' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
                            'Stores' => ['view_stores', 'create_stores', 'edit_stores', 'delete_stores'],
                            'Staff' => ['view_staff', 'create_staff', 'edit_staff', 'delete_staff'],
                            'Reviews' => ['view_reviews', 'reply_reviews'],
                            'Billing' => ['manage_plans', 'manage_subscriptions', 'review_payments', 'buy_plans'],
                        ];
                    @endphp

                    @foreach ($permissionGroups as $groupName => $permissions)
                        @php $groupKey = Str::slug($groupName); @endphp
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select-all-group" data-group="{{ $groupKey }}" id="select-all-{{ $groupKey }}">
                                    <label class="custom-control-label" for="select-all-{{ $groupKey }}">
                                        Select All {{ $groupName }}
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($permissions as $permission)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}" id="perm-{{ $permission }}" class="custom-control-input permission-checkbox permission-{{ $groupKey }}" {{ in_array($permission, old('permissions', []), true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="perm-{{ $permission }}">{{ $permission }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="text-right">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
        </x-form-card>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.select-all-group').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                const group = this.dataset.group;
                document.querySelectorAll('.permission-' + group).forEach(function (checkbox) {
                    checkbox.checked = toggle.checked;
                });
            });
        });
    });
</script>
@endsection
