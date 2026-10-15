@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit User</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Roles</label>
                        <div class="row">
                            @foreach ($roles as $role)
                                <div class="col-md-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}" class="custom-control-input" {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray()), true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="role-{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @include('users.partials.store-select')

                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
