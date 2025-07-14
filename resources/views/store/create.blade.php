@extends('adminlte::page')

@section('title', 'Create Store Profile')

@section('content_header')
    <h1>Create Store Profile</h1>
@stop

@section('content')
<form action="{{ route('store.store') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
    @csrf

    <!-- Basic Information -->
    <h5 class="mb-3 font-weight-bold">Basic Information</h5>
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="name">Store Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6">
            <label for="phone">Contact Phone</label>
            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="email">Contact Email</label>
        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
    </div>

    <div class="form-group mb-4">
        <label for="address">Address</label>
        <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
    </div>

    <!-- Store Logo -->
    <h5 class="mb-3 font-weight-bold">Store Logo/Banner</h5>
    <div class="form-group mb-4">
        <div class="custom-file">
            <input type="file" class="custom-file-input" name="logo" accept="image/*">
            <label class="custom-file-label" for="logo">Click to upload or drag and drop</label>
        </div>
        <small class="form-text text-muted">PNG, JPG, GIF up to 10MB</small>
    </div>

    <button type="submit" class="btn btn-success">Create Store</button>
</form>
@stop

@section('js')
<script>
    // Show selected file name
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        e.target.nextElementSibling.innerText = e.target.files[0].name;
    });
</script>
@stop
