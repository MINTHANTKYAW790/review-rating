@extends('layouts.app')

@section('title', __('messages.reviews_ratings_management'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Review Management" subtitle="Filter and manage customer reviews">
            <x-slot:actions>
                <button id="toggleFilterBtn" type="button" class="btn btn-outline-primary">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="review-queue-chips mb-3" id="reviewQueueChips">
            <button type="button" class="btn btn-sm btn-primary js-queue-chip" data-queue="inbox">{{ __('messages.review_queue_inbox') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary js-queue-chip" data-queue="starred">{{ __('messages.review_queue_starred') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary js-queue-chip" data-queue="wants_contact">{{ __('messages.wants_contact') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary js-queue-chip" data-queue="done">{{ __('messages.review_queue_done') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary js-queue-chip" data-queue="all">{{ __('messages.review_all') }}</button>
            <input type="hidden" id="queue" value="inbox">
        </div>

        <x-filter-bar id="reviewFilterCard">
                <form id="reviewFilterForm" class="row align-items-end">
                    <div class="col-md-6 col-xl-2">
                        <label for="store_id" class="form-label small text-uppercase text-muted fw-bold mb-2">Shop / Branch</label>
                        <select id="store_id" name="store_id" class="custom-select custom-select-sm filter-input">
                            <option value="">All Shops</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="staff_id" class="form-label small text-uppercase text-muted fw-bold mb-2">Staff</label>
                        <select id="staff_id" name="staff_id" class="custom-select custom-select-sm filter-input">
                            <option value="">All Staff</option>
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="rating_store" class="form-label small text-uppercase text-muted fw-bold mb-2">Shop Rating</label>
                        <select id="rating_store" name="rating_store" class="custom-select custom-select-sm filter-input">
                            <option value="">All</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} star</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="rating_staff" class="form-label small text-uppercase text-muted fw-bold mb-2">Staff Rating</label>
                        <select id="rating_staff" name="rating_staff" class="custom-select custom-select-sm filter-input">
                            <option value="">All</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} star</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="start_date" class="form-label small text-uppercase text-muted fw-bold mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control form-control-sm filter-input">
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label for="end_date" class="form-label small text-uppercase text-muted fw-bold mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control form-control-sm filter-input">
                    </div>
                </form>

                <div class="d-flex justify-content-end mt-3">
                    <button id="resetBtn" type="button" class="btn btn-outline-secondary mr-2">
                        Reset
                    </button>
                    <button id="searchBtn" type="button" class="btn btn-primary">
                        Search
                    </button>
                </div>
        </x-filter-bar>

        <x-form-card class="overflow-hidden">
            <div class="table-responsive">
                <table id="reviews-table" class="table table-striped table-hover mb-0 w-100 align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Shop Name</th>
                            <th>Shop Star</th>
                            <th>Staff Name</th>
                            <th>Staff Star</th>
                            <th>Phone No</th>
                            <th>{{ __('messages.review_attention') }}</th>
                            <th>Reviews</th>
                            <th>{{ __('messages.admin_remark') }}</th>
                            <th>Reviewed Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </x-form-card>
    </div>
</section>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!$.fn || !$.fn.DataTable) {
                console.error('DataTables is not loaded.');
                return;
            }

            const table = $('#reviews-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                order: [[9, 'desc']],
                pageLength: 10,
                pagingType: 'simple_numbers',
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    lengthMenu: '_MENU_ per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        previous: '«',
                        next: '»'
                    }
                },
                dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-right"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ajax: {
                    url: '{{ route('review.index') }}',
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: function (d) {
                        d.store_id = $('#store_id').val();
                        d.staff_id = $('#staff_id').val();
                        d.rating_store = $('#rating_store').val();
                        d.rating_staff = $('#rating_staff').val();
                        d.queue = $('#queue').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'shop_name', name: 'store.name' },
                    { data: 'shop_star', name: 'rating_store', orderable: false, searchable: false },
                    { data: 'staff_name', name: 'staff.name' },
                    { data: 'staff_star', name: 'rating_staff', orderable: false, searchable: false },
                    { data: 'customer_phone', name: 'customer_phone', orderable: false },
                    { data: 'attention', name: 'attention', orderable: false, searchable: false },
                    { data: 'review_text', name: 'comment', orderable: false },
                    { data: 'remark_preview', name: 'admin_remark', orderable: false },
                    { data: 'reviewed_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#toggleFilterBtn').on('click', function () {
                $('#reviewFilterCard').toggleClass('d-none');
            });

            $('#searchBtn').on('click', function () {
                table.ajax.reload();
            });

            $('.filter-input').on('change input', function () {
                table.ajax.reload();
            });

            $('#resetBtn').on('click', function () {
                $('#reviewFilterForm')[0].reset();
                table.ajax.reload();
            });

            $('.js-queue-chip').on('click', function () {
                const queue = this.dataset.queue;
                $('#queue').val(queue);
                $('.js-queue-chip').removeClass('btn-primary').addClass('btn-outline-secondary');
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                table.ajax.reload();
            });
        });
    </script>
@endpush
