@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <form action="{{ route('store.update', $store->id) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
                @csrf
                @method('PUT')

                <h5 class="mb-3 pb-2 font-weight-bold">{{ __('messages.edit_store_information') }}</h5>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">{{ __('messages.store_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required value="{{ old('name', $store->name) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">{{ __('messages.contact_phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $store->phone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('messages.contact_email') }}</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $store->email) }}">
                </div>

                <div class="form-group">
                    <label for="address">{{ __('messages.address') }}</label>
                    <textarea name="address" class="form-control" id="address" rows="2">{{ old('address', $store->address) }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 border-bottom pb-2 font-weight-bold">{{ __('messages.store_description') }}</h5>

                <div class="form-group">
                    <label for="description">{{ __('messages.description') }}</label>
                    <textarea name="description" class="form-control" id="description" rows="4">{{ old('description', $store->description) }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 pb-2 font-weight-bold">{{ __('messages.store_logo_banner') }}</h5>

                <div class="form-group">
                    <label for="logo">{{ __('messages.upload_logo') }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                        <label class="custom-file-label" for="logo">{{ __('messages.choose_file') }}</label>
                    </div>
                    <small class="form-text text-muted mt-2">{{ __('messages.image_upload_hint') }}</small>

                    @if ($store->logo_url)
                        <div class="mt-3">
                            <label class="d-block">{{ __('messages.current_logo') }}</label>
                            <img src="{{ asset('storage/' . $store->logo_url) }}" alt="Store Logo" class="img-fluid rounded border" style="max-height: 120px;">
                        </div>
                    @endif
                </div>

                <div class="form-group mt-4">
                    <label for="qr_identifier">{{ __('messages.qr_identifier') }} <span class="text-danger">*</span></label>
                    <input type="text" name="qr_identifier" class="form-control" id="qr_identifier" required value="{{ old('qr_identifier', $store->qr_identifier) }}">
                </div>

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.update_store') }}</button>
                    <a href="{{ route('store.index') }}" class="btn btn-secondary px-4 ml-2">{{ __('messages.cancel') }}</a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection