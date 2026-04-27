@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Choose QR Design</h4>
            <p class="text-muted mb-0">Pick a style for <strong>{{ $store->name }}</strong>, then download it as an image file.</p>
        </div>
        <a href="{{ route('public.qr-preview') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        @foreach ($templates as $key => $template)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-light rounded p-3 mb-3">
                            <img src="{{ route('qr.image', $store->id) }}?template={{ $key }}"
                                alt="{{ $template['name'] }} QR preview"
                                class="img-fluid"
                                style="max-height: 260px;">
                        </div>
                        <h5 class="mb-1">{{ $template['name'] }}</h5>
                        <p class="text-muted small mb-3">{{ $template['description'] }}</p>
                        <button type="button"
                            class="btn btn-primary btn-sm px-3 js-download-png"
                            data-svg-url="{{ route('qr.image', $store->id) }}?template={{ $key }}&size=2200"
                            data-filename="{{ $store->slug }}-qr-{{ $key }}.png">
                            Download Image (PNG)
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.js-download-png');

        const downloadBlob = (blob, filename) => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        };

        buttons.forEach((button) => {
            button.addEventListener('click', async function() {
                const svgUrl = this.dataset.svgUrl;
                const fileName = this.dataset.filename || 'qr-code.png';
                this.disabled = true;
                this.textContent = 'Preparing...';

                try {
                    const response = await fetch(svgUrl, {
                        credentials: 'same-origin'
                    });
                    if (!response.ok) {
                        throw new Error('Could not generate QR image.');
                    }

                    const svgText = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(svgText, 'image/svg+xml');
                    const svgEl = doc.documentElement;
                    const viewBox = (svgEl.getAttribute('viewBox') || '0 0 1100 1100').split(/\s+/);
                    const width = parseInt(viewBox[2], 10) || 1100;
                    const height = parseInt(viewBox[3], 10) || 1100;
                    const svgBlob = new Blob([svgText], {
                        type: 'image/svg+xml;charset=utf-8'
                    });
                    const objectUrl = URL.createObjectURL(svgBlob);

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        throw new Error('Canvas is not supported in this browser.');
                    }

                    const image = new Image();
                    image.onload = function() {
                        try {
                            ctx.clearRect(0, 0, width, height);
                            ctx.drawImage(image, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                if (!blob) {
                                    alert('Could not create PNG image. Please try again.');
                                    URL.revokeObjectURL(objectUrl);
                                    return;
                                }
                                downloadBlob(blob, fileName);
                                URL.revokeObjectURL(objectUrl);
                            }, 'image/png');
                        } catch (err) {
                            URL.revokeObjectURL(objectUrl);
                            alert('Could not render PNG image. Please try another template.');
                        }
                    };

                    image.onerror = function() {
                        URL.revokeObjectURL(objectUrl);
                        alert('Could not render image in browser. Please try another template.');
                    };

                    image.src = objectUrl;
                } catch (error) {
                    alert(error.message || 'Download failed. Please try again.');
                } finally {
                    this.disabled = false;
                    this.textContent = 'Download Image (PNG)';
                }
            });
        });
    });
</script>
@endpush
