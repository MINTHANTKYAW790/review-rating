@extends('layouts.app')

@section('title', 'Users')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Users" subtitle="Manage accounts and assigned roles">
            <x-slot:actions>
                @can('create_users')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add User
                    </a>
                @endcan
            </x-slot:actions>
        </x-page-header>

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
                                <th class="text-right" width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->getRoleNames() as $roleName)
                                            <span class="badge badge-primary mr-1">{{ $roleName }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @can('edit_users')
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @endcan
                                        @can('delete_users')
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                        @endcan
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
