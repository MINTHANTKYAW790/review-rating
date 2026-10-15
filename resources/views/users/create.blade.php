@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Create User" subtitle="Add a new account and assign roles" />

        <div class="row">
            <div class="col-lg-8">
                <x-form-card>
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Roles</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}" class="custom-control-input" {{ in_array($role->name, old('roles', []), true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="role-{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @include('users.partials.store-select')

                        <div class="text-right">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save User</button>
                        </div>
                    </form>
                </x-form-card>
            </div>
        </div>
    </div>
</section>
@endsection
