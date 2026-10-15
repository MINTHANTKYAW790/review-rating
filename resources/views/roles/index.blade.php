@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Roles" subtitle="Permissions and access control">
            <x-slot:actions>
                @can('create_roles')
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Role
                    </a>
                @endcan
            </x-slot:actions>
        </x-page-header>

        <x-form-card>
            @if ($roles->isEmpty())
                <x-empty-state message="No roles found." icon="fa-user-tag" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th class="text-right" width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @forelse ($role->permissions as $permission)
                                            <span class="badge badge-info mr-1">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted">No permissions</span>
                                        @endforelse
                                    </td>
                                    <td class="text-right">
                                        @can('edit_roles')
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @endcan
                                        @can('delete_roles')
                                            @if ($role->name !== 'superadmin')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this role?')">Delete</button>
                                                </form>
                                            @endif
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