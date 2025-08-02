@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="card shadow p-4 text-center">
        <h4 class="mb-4 font-weight-bold">QR Code Preview</h4>

        <!-- QR Code Image -->
        <div class="d-flex justify-content-center mb-4">
            {!! QrCode::size(200)->generate($qrUrl) !!}
        </div>

        <!-- QR Code URL Display -->
        <div class="bg-light p-3 rounded">
            <strong>QR Code URL:</strong>
            <input type="text" class="form-control mt-2 text-center" readonly value="{{ $qrUrl }}">
        </div>
    </div>

</div>
@endsection
