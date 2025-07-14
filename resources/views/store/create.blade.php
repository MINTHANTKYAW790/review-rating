@extends('adminlte::page')

@section('title', 'Create Store Profile')

@section('content_header')
<h1>Create Store Profile</h1>
@stop

@section('content')
<form method="POST" action="{{ route('stores.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-medium mb-4">Basic Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium">Store Name</label>
                <input type="text" name="name" id="name" required class="w-full border rounded p-2" />
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium">Contact Phone</label>
                <input type="text" name="phone" id="phone" class="w-full border rounded p-2" />
            </div>
        </div>

        <div class="mt-4">
            <label for="email" class="block text-sm font-medium">Contact Email</label>
            <input type="email" name="email" id="email" class="w-full border rounded p-2" />
        </div>

        <div class="mt-4">
            <label for="address" class="block text-sm font-medium">Address</label>
            <textarea name="address" id="address" class="w-full border rounded p-2"></textarea>
        </div>
    </div>

    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-medium mb-4">Store Description</h2>

        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea name="description" id="description" class="w-full border rounded p-2"></textarea>
        </div>
    </div>

    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-medium mb-4">Store Logo/Banner</h2>

        <div>
            <input type="file" name="logo" accept="image/*" class="border p-4 w-full rounded" />
            <p class="text-sm text-gray-500 mt-2">PNG, JPG, or GIF up to 10MB</p>
        </div>
    </div>

    <div class="text-right">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Save Store</button>
    </div>
</form>

@stop