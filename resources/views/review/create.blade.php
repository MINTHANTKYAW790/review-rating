@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <form action="{{ route('store.store') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
                @csrf

                <h5 class="mb-3 pb-2 font-weight-bold">Basic Information</h5>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Store Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Contact Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Contact Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" class="form-control" id="address" rows="2">{{ old('address') }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 border-bottom pb-2 font-weight-bold">Store Description</h5>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 pb-2 font-weight-bold">Store Logo/Banner</h5>

                <div class="form-group">
                    <label for="logo">Upload Logo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                        <label class="custom-file-label" for="logo">Choose file</label>
                    </div>
                    <small class="form-text text-muted mt-2">PNG, JPG, or GIF up to 10MB</small>
                </div>

                <div class="form-group mt-4">
                    <label for="qr_identifier">QR Identifier <span class="text-danger">*</span></label>
                    <input type="text" name="qr_identifier" class="form-control" id="qr_identifier" required value="{{ old('qr_identifier') }}">
                </div>

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary px-4">Create Store</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection