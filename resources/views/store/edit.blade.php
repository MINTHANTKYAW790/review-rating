@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <form action="{{ route('store.update', $store->id) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
                @csrf
                @method('PUT')

                <h5 class="mb-3 pb-2 font-weight-bold">Edit Store Information</h5>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Store Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required value="{{ old('name', $store->name) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Contact Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $store->phone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Contact Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $store->email) }}">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" class="form-control" id="address" rows="2">{{ old('address', $store->address) }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 border-bottom pb-2 font-weight-bold">Store Description</h5>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="4">{{ old('description', $store->description) }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 pb-2 font-weight-bold">Store Logo/Banner</h5>

                <div class="form-group">
                    <label for="logo">Upload Logo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                        <label class="custom-file-label" for="logo">Choose file</label>
                    </div>
                    <small class="form-text text-muted mt-2">PNG, JPG, or GIF up to 10MB</small>

                    @if ($store->logo_url)
                        <div class="mt-3">
                            <label class="d-block">Current Logo:</label>
                            <img src="{{ asset('storage/' . $store->logo_url) }}" alt="Store Logo" class="img-fluid rounded border" style="max-height: 120px;">
                        </div>
                    @endif
                </div>

                <div class="form-group mt-4">
                    <label for="qr_identifier">QR Identifier <span class="text-danger">*</span></label>
                    <input type="text" name="qr_identifier" class="form-control" id="qr_identifier" required value="{{ old('qr_identifier', $store->qr_identifier) }}">
                </div>

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary px-4">Update Store</button>
                    <a href="{{ route('store.index') }}" class="btn btn-secondary px-4 ml-2">Cancel</a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
