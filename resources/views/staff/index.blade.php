@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mx-2">
        <h5>{{ __('messages.total_staff') }}: <strong>{{ $stores->flatMap->staff->count() }}</strong></h5>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addStaffModal">
            + {{ __('messages.add_new_staff') }}
        </button>
    </div>

    <div class="row">
        @foreach($stores as $store)
        <div class="col-12 mb-4">
            <h4 class="ml-2">{{ $store->name }} : {{$store->staff->count()}}</h4>
            <div class="row">
                @if($store->staff->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">{{ __('messages.no_staff_members') }}</p>
                </div>
                @else
                @foreach($store->staff as $member)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <img src="{{ $member->image_url ? asset('storage/' . $member->image_url) : asset('default-user.jpg') }}"
                                alt="{{ $member->name }}"
                                class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">

                            <h6 class="mb-0">{{ $member->name }}</h6>
                            <small class="text-muted">{{ $member->role }}</small>

                            <div class="mt-2">
                                {{-- Show star ratings --}}
                                @php $rating = $member->reviews_avg_rating_staff ?? 0; $rounded = floor($rating); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($i <=$rounded)
                                    <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $rating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                        <i class="far fa-star text-warning"></i>
                                        @endif
                                        @endfor
                                        <div class="mt-1 text-muted">{{ number_format($rating, 1) }}/5</div>
                            </div>

                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <button class="btn btn-sm btn-outline-secondary mx-2"
                                    data-toggle="modal" data-target="#editStaffModal"
                                    onclick="editStaff(this)"
                                    data-id="{{ $member->id }}"
                                    data-name="{{ $member->name }}"
                                    data-role="{{ $member->role }}"
                                    data-image="{{ $member->image_url ? asset('storage/' . $member->image_url) : asset('default-user.jpg') }}"
                                    data-store-id="{{ $store->id }}">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-toggle="modal" data-target="#deleteStaffModal"
                                    onclick="deleteStaff(this)"
                                    data-id="{{ $member->id }}"
                                    data-name="{{ $member->name }}">
                                    <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">{{ __('messages.add_new_staff_member') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">

                <!-- Image Upload -->
                <label for="image_url" class="d-block mb-3">
                    <div class="rounded-circle border border-secondary d-inline-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px; background-color: #f8f9fa;" id="addImagePreview">
                        <i class="fas fa-user fa-2x text-muted"></i>
                    </div>
                    <div class="mt-2 text-primary" style="cursor:pointer;">
                        <i class="fas fa-camera mr-1"></i> {{ __('messages.upload_image') }}
                    </div>
                    <input type="file" class="d-none" id="image_url" name="image_url" accept="image/*">
                </label>

                <!-- Staff Name -->
                <div class="form-group text-left">
                    <label for="name">{{ __('messages.staff_name') }}</label>
                    <input type="text" name="name" class="form-control" placeholder="{{ __('messages.enter_staff_name') }}" required>
                </div>

                <!-- Role -->
                <div class="form-group text-left">
                    <label for="role">{{ __('messages.role_position_optional') }}</label>
                    <input type="text" name="role" class="form-control" placeholder="{{ __('messages.role_position_example') }}">
                </div>

                <!-- Store Selection -->
                <div class="form-group text-left">
                    <label for="store_id">{{ __('messages.select_store') }}</label>
                    <select name="store_id" id="store_id" class="form-control" required>
                        <option value="" disabled selected>{{ __('messages.select_a_store') }}</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>

        </form>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('staff.update', '') }}" method="POST" enctype="multipart/form-data" class="modal-content" id="editStaffForm">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">{{ __('messages.edit_staff_member') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">

                <!-- Image Upload -->
                <label for="edit_image_url" class="d-block mb-3">
                    <div class="rounded-circle border border-secondary d-inline-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px; background-color: #f8f9fa;" id="editImagePreview">
                        <i class="fas fa-user fa-2x text-muted"></i>
                    </div>
                    <div class="mt-2 text-primary" style="cursor:pointer;">
                        <i class="fas fa-camera mr-1"></i> {{ __('messages.change_image') }}
                    </div>
                    <input type="file" class="d-none" id="edit_image_url" name="image_url" accept="image/*">
                </label>

                <!-- Staff Name -->
                <div class="form-group text-left">
                    <label for="edit_name">{{ __('messages.staff_name') }}</label>
                    <input type="text" name="name" class="form-control" id="edit_name" placeholder="{{ __('messages.enter_staff_name') }}" required>
                </div>

                <!-- Role -->
                <div class="form-group text-left">
                    <label for="edit_role">{{ __('messages.role_position_optional') }}</label>
                    <input type="text" name="role" class="form-control" id="edit_role" placeholder="{{ __('messages.role_position_example') }}">
                </div>

                <!-- Store Selection -->
                <div class="form-group text-left">
                    <label for="edit_store_id">{{ __('messages.select_store') }}</label>
                    <select name="store_id" id="edit_store_id" class="form-control" required>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            </div>

        </form>
    </div>
</div>

<!-- Delete Staff Confirmation Modal -->
<div class="modal fade" id="deleteStaffModal" tabindex="-1" role="dialog" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="" method="POST" class="modal-content" id="deleteStaffForm">
            @csrf
            @method('DELETE')

            <div class="modal-header">
                <h5 class="modal-title" id="deleteStaffModalLabel">{{ __('messages.delete_staff_member') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <p>{{ __('messages.confirm_delete_staff') }} <strong id="deleteStaffName"></strong>?</p>
                <p class="text-muted">{{ __('messages.action_cannot_be_undone') }}</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    var basePath = '{{ rtrim(url(' / '), ' / ') }}'; // Ensure no trailing slash

    function editStaff(button) {
        console.log("Edit button clicked");
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var role = button.getAttribute('data-role');
        var image = button.getAttribute('data-image');
        var storeId = button.getAttribute('data-store-id'); // Get store_id

        console.log('Data:', id, name, role, image, storeId);

        // Set form action
        document.getElementById('editStaffForm').setAttribute('action', basePath + '/staff/' + id);

        // Fill fields
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_role').value = role;

        // Set selected store in dropdown
        document.getElementById('edit_store_id').value = storeId;

        // Handle image preview
        var preview = document.getElementById('editImagePreview');
        if (image && !image.includes('default-user.jpg')) {
            preview.innerHTML = '<img src="' + image + '" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">';
        } else {
            preview.innerHTML = '<i class="fas fa-user fa-2x text-muted"></i>';
        }

        // Clear file input
        document.getElementById('edit_image_url').value = '';
    }

    function deleteStaff(button) {
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');

        // Set form action
        document.getElementById('deleteStaffForm').setAttribute('action', basePath + '/staff/' + id);

        // Set staff name in modal
        document.getElementById('deleteStaffName').textContent = name;
    }

    // Image preview for add modal
    document.getElementById('image_url').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var preview = document.getElementById('addImagePreview');
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<i class="fas fa-user fa-2x text-muted"></i>';
        }
    });

    // Image preview for edit modal
    document.getElementById('edit_image_url').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var preview = document.getElementById('editImagePreview');
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">';
            };
            reader.readAsDataURL(file);
        }
        // If no file, keep current preview
    });
</script>
@endpush

@endsection