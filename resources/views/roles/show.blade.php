@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Details</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $role->name }}</p>
                <p><strong>Permissions:</strong></p>
                @forelse ($role->permissions as $permission)
                    <span class="badge badge-info mr-1">{{ $permission->name }}</span>
                @empty
                    <span class="text-muted">No permissions assigned.</span>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
