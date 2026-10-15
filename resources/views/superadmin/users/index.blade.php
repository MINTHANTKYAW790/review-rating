@extends('layouts.app')

@section('title', 'Users')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="All users" subtitle="Every account, role, and store count" />

        <x-form-card>
            @if ($users->isEmpty())
                <x-empty-state message="No users found." icon="fa-users" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Stores</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="badge badge-primary mr-1">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">None</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $user->stores_count }}</td>
                                    <td>
                                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @if (! $user->is(auth()->user()))
                                            <form action="{{ route('superadmin.users.active', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
