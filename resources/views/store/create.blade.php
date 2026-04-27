@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <form action="{{ route('store.store') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
                @csrf

                <h5 class="mb-3 pb-2 font-weight-bold">{{ __('messages.basic_information') }}</h5>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">{{ __('messages.store_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">{{ __('messages.contact_phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('messages.contact_email') }}</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="address">{{ __('messages.address') }}</label>
                    <textarea name="address" class="form-control" id="address" rows="2">{{ old('address') }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 border-bottom pb-2 font-weight-bold">{{ __('messages.store_description') }}</h5>

                <div class="form-group">
                    <label for="description">{{ __('messages.description') }}</label>
                    <textarea name="description" class="form-control" id="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <h5 class="mb-3 mt-4 pb-2 font-weight-bold">{{ __('messages.store_logo_banner') }}</h5>

                <div class="form-group">
                    <label for="logo">{{ __('messages.upload_logo') }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*" onchange="previewImage(event)">
                        <label class="custom-file-label" for="logo">{{ __('messages.choose_file') }}</label>
                    </div>
                    <small class="form-text text-muted mt-2">{{ __('messages.image_upload_hint') }}</small>
                    <div class="mt-2" id="logo-preview-container" style="display: none;">
                        <label>{{ __('messages.image_preview') }}</label><br>
                        <img id="logo-preview" src="#" alt="Logo Preview" class="img-thumbnail preview-image" width="150">
                    </div>
                </div>

                @push('scripts')
                <script>
                    function previewImage(event) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var output = document.getElementById('logo-preview');
                            output.src = reader.result;
                            document.getElementById('logo-preview-container').style.display = 'block';
                        };
                        reader.readAsDataURL(event.target.files[0]);
                        // Update the custom file label
                        var fileName = event.target.files[0].name;
                        var nextSibling = event.target.nextElementSibling;
                        if (nextSibling && nextSibling.classList.contains('custom-file-label')) {
                            nextSibling.innerText = fileName;
                        }
                    };

                    document.addEventListener('DOMContentLoaded', function() {
                        // Handle initial file input label if an old value exists (though not directly for new create forms)
                        // This part is more relevant for edit forms, but good to have.
                        var logoInput = document.getElementById('logo');
                        if (logoInput && logoInput.files.length > 0) {
                            var fileName = logoInput.files[0].name;
                            var nextSibling = logoInput.nextElementSibling;
                            if (nextSibling && nextSibling.classList.contains('custom-file-label')) {
                                nextSibling.innerText = fileName;
                            }
                        }
                    });
                </script>
                @endpush

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.create_store') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
