@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mx-2">
        <h5>Total Staff: <strong>{{ $staff->count() }}</strong></h5>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addStaffModal">
            + Add New Staff
        </button>
    </div>

    <div class="row">
        @foreach($staff as $member)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <img src="{{ $member->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->name) }}"
                        alt="{{ $member->name }}"
                        class="rounded-circle mb-3" width="80" height="80" style="object-fit: cover;">

                    <h6 class="mb-0">{{ $member->name }}</h6>
                    <small class="text-muted">{{ $member->role }}</small>

                    <div class="mt-2">
                        {{-- Show star ratings --}}
                        @php $rounded = floor($member->rating); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if($i <=$rounded)
                            <i class="fas fa-star text-warning"></i>
                            @elseif($i - 0.5 <= $member->rating)
                                <i class="fas fa-star-half-alt text-warning"></i>
                                @else
                                <i class="far fa-star text-warning"></i>
                                @endif
                                @endfor
                                <div class="mt-1 text-muted">{{ $member->rating }}/5</div>
                    </div>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-sm btn-outline-secondary mx-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <form action="{{ route('staff.destroy', $member->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this staff?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
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
                <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">

                <!-- Image Upload -->
                <label for="image_url" class="d-block mb-3">
                    <div class="rounded-circle border border-secondary d-inline-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px; background-color: #f8f9fa;">
                        <i class="fas fa-user fa-2x text-muted"></i>
                    </div>
                    <div class="mt-2 text-primary" style="cursor:pointer;">
                        <i class="fas fa-camera mr-1"></i> Upload Image
                    </div>
                    <input type="file" class="d-none" id="image_url" name="image_url" accept="image/*">
                </label>

                <!-- Staff Name -->
                <div class="form-group text-left">
                    <label for="name">Staff Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter staff name" required>
                </div>

                <!-- Role -->
                <div class="form-group text-left">
                    <label for="role">Role/Position (Optional)</label>
                    <input type="text" name="role" class="form-control" placeholder="e.g., Barista, Manager, Cashier">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>

        </form>
    </div>
</div>

@endsection