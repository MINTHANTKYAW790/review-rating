@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        @foreach($stores as $store)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">

                    <h5 class="fw-bold mb-3">{{ $store->name }}</h5>

                    <div class="bg-light p-3 rounded mb-3">
                        {!! QrCode::size(150)->generate($store->qrUrl) !!}
                    </div>
                    <div class="mt-auto">
                        <p class="text-muted small mb-3">{{ $store->address }}</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#qrModal{{ $store->id }}">
                        {{ __('messages.view_qr_code') }}
                    </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Modal -->
        <div class="modal fade" id="qrModal{{ $store->id }}" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel{{ $store->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel{{ $store->id }}">{{ __('messages.qr_code_for') }} {{ $store->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <!-- QR Code Image -->
                        <div class="mb-4">
                            {!! QrCode::size(200)->generate($store->qrUrl) !!}
                        </div>

                        <!-- QR Code URL -->
                        <div class="bg-light p-3 rounded mb-3">
                            <strong>{{ __('messages.qr_code_url') }}</strong>
                            <input type="text" class="form-control mt-2 text-center" readonly value="{{ $store->qrUrl }}">
                        </div>

                        <!-- Download Button -->
                        <a href="{{ route('qr.download', $store->id) }}" class="btn btn-success w-">{{ __('messages.download_qr_code') }}</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
