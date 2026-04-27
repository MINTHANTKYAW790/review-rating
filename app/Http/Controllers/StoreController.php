<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Review;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreController extends Controller
{
    use AuthorizesRequests;

    // For index
    public function index()
    {
        $this->authorize('viewAny', Store::class); // Authorize viewing any stores
        $stores = auth('web')->user()->stores()->withAvg('reviews', 'rating_store')->latest()->get();
        return view('store.index', compact('stores'));
    }

    // For show
    public function show(Store $store) // Using implicit model binding
    {
        $this->authorize('view', $store); // Authorize viewing this specific store
        return view('store.show', compact('store'));
    }

    public function create()
    {
        $this->authorize('create', Store::class); // Authorize creating a store
        return view('store.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Store::class); // Authorize creating a store

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB
        ]);

        $logoPath = null;
        $logoContent = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $logoContent = file_get_contents($request->file('logo')->getRealPath());
        }

        Store::create([
            'user_id' => auth('web')->user()->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'logo_url' => $logoPath,
            'logo_image' => $logoContent, 
            'qr_identifier' => Str::uuid(),
        ]);

        return redirect()->route('store.index')->with('success', 'Store created successfully!');
    }

    public function edit(Store $store) // Using implicit model binding
    {
        $this->authorize('update', $store); // Authorize updating this specific store
        return view('store.edit', compact('store'));
    }

    public function update(Request $request, Store $store) // Using implicit model binding
    {
        $this->authorize('update', $store); // Authorize updating this specific store

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_url'] = $logoPath;
            $logoContent = file_get_contents($request->file('logo')->getRealPath());
            $validated['logo_image'] = $logoContent;
        }

        $store->update($validated);

        return redirect()->route('store.index')->with('success', 'Store updated successfully!');
    }

    public function showQr()
    {
        // This method implicitly only shows the current user's stores,
        // so `viewAny` authorization is sufficient, which is already handled.
        $stores = auth('web')->user()->stores; // Correctly returns a Collection since relationship is hasMany


        foreach ($stores as $store) {
            $store->qrUrl = url("public/qr/" . $store->slug); // Generate QR URL dynamically
        }

        return view('qr.index', compact('stores'));
    }

    public function showStore($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        // This is a public route for submitting reviews, no authorization needed for viewing the store publicly
        return view('public.review', compact('store'));
    }


    public function submitReview(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        // This is a public route for submitting reviews, no authorization needed
        $validated = $request->validate([
            'rating_store' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'staff_id' => 'nullable|exists:staff,id',
            'rating_staff' => 'nullable|integer|min:1|max:5',
            'comment_staff' => 'nullable|string',
        ]);

        $staff_id = isset($validated['staff_id']) && $validated['staff_id'] ? ($validated['rating_staff'] ?? null) : null;
        info($request->all());

        info('Guest review submission details:');
        info('IP Address: ' . $request->ip());
        info('User Agent: ' . $request->userAgent());
        info('Referer: ' . $request->header('referer'));
        $review = Review::create([
            'store_id' => $store->id,
            'staff_id' => $validated['staff_id'] ?? null,
            'rating_store' => $validated['rating_store'],
            'rating_staff' => $staff_id,
            'comment' => $validated['comment'] ?? null,
            'comment_staff' => $validated['comment_staff'] ?? null,
        ]);

        $rating = $validated['rating_store'];
        $message = '';

        if ($rating >= 3) {
            $message = 'Thank you for your valuable feedback! We appreciate your high rating and are glad you had a positive experience.';
        } else {
            $message = 'We are very sorry to hear that your experience was not satisfactory. We appreciate your honest feedback and will strive to do our best to improve. Thank you for your patience.';
        }

        return redirect()->route('public.review', $slug)->with('status', $message);
    }

    public function downloadQr(Store $store)
    {
        $this->authorize('view', $store);
        $templates = $this->qrTemplates();
        return view('qr.templates', compact('store', 'templates'));
    }

    public function getQrImage(Store $store)
    {
        $this->authorize('view', $store);
        $template = request()->query('template', 'classic');
        $requestedSize = (int) request()->query('size', 260);
        $canvasSize = max(260, min($requestedSize, 2400));
        $svg = $this->buildStyledQrSvg($store, $template, $canvasSize);
        return response($svg)
            ->header('Content-Type', 'image/svg+xml');
    }

    public function downloadStyledQr(Store $store, string $template)
    {
        $this->authorize('view', $store);
        $templateKey = $this->sanitizeQrTemplate($template);
        $svg = $this->buildStyledQrSvg($store, $templateKey, 1100);
        $fileName = $store->slug . '-qr-' . $templateKey . '.svg';

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function qrTemplates(): array
    {
        return [
            'classic' => ['name' => 'Classic Clean', 'description' => 'Simple clean border for everyday use.'],
            'neon' => ['name' => 'Neon Glow', 'description' => 'Bright gradient frame that draws attention.'],
            'royal' => ['name' => 'Royal Frame', 'description' => 'Premium layered frame for luxury style.'],
            'dotted' => ['name' => 'Dotted Pop', 'description' => 'Fun dotted corners with playful vibe.'],
            'sunburst' => ['name' => 'Sunburst', 'description' => 'Warm color burst to stand out on posters.'],
            'tech' => ['name' => 'Tech Grid', 'description' => 'Modern angular border for digital branding.'],
        ];
    }

    private function sanitizeQrTemplate(string $template): string
    {
        $available = array_keys($this->qrTemplates());
        return in_array($template, $available, true) ? $template : 'classic';
    }

    private function buildStyledQrSvg(Store $store, string $template, int $canvasSize): string
    {
        $templateKey = $this->sanitizeQrTemplate($template);
        $padding = (int) round($canvasSize * 0.16);
        $qrSize = $canvasSize - ($padding * 2);
        $url = url('/public/qr/' . $store->slug);
        $rawQrSvg = QrCode::format('svg')->size($qrSize)->margin(1)->generate($url);
        $qrInnerSvg = $this->extractInnerSvg($rawQrSvg);
        $safeStoreName = e($store->name);
        $safeUrl = e($url);
        $borderSvg = $this->buildBorderSvg($templateKey, $canvasSize);
        $labelFontSize = $this->scaled($canvasSize, 18);
        $subFontSize = $this->scaled($canvasSize, 12);
        $innerInset = $this->scaled($canvasSize, 22);
        $innerSize = $canvasSize - $this->scaled($canvasSize, 44);
        $innerRadius = $this->scaled($canvasSize, 16);
        $labelY = $canvasSize - $this->scaled($canvasSize, 30);
        $subLabelY = $canvasSize - $this->scaled($canvasSize, 12);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$canvasSize}" height="{$canvasSize}" viewBox="0 0 {$canvasSize} {$canvasSize}">
    <defs>
        <style>
            .qr-label { font-family: Arial, sans-serif; font-size: {$labelFontSize}px; fill: #2c2c44; font-weight: 600; }
            .qr-sub { font-family: Arial, sans-serif; font-size: {$subFontSize}px; fill: #666; }
        </style>
    </defs>
    {$borderSvg}
    <rect x="{$innerInset}" y="{$innerInset}" width="{$innerSize}" height="{$innerSize}" rx="{$innerRadius}" fill="#ffffff" />
    <g transform="translate({$padding}, {$padding})">   
        {$qrInnerSvg}
    </g>
</svg>
SVG;
    }

    private function buildBorderSvg(string $template, int $canvasSize): string
    {
        $inset = $this->scaled($canvasSize, 12);
        $outer = $canvasSize - ($inset * 2);
        $radius = $this->scaled($canvasSize, 26);
        $smallRadius = $this->scaled($canvasSize, 14);
        $stroke = $this->scaled($canvasSize, 6);
        $innerInset = $this->scaled($canvasSize, 28);
        $innerSize = $canvasSize - $this->scaled($canvasSize, 56);
        $innerStroke = $this->scaled($canvasSize, 2);
        $corner = $this->scaled($canvasSize, 42);
        $dotRadius = $this->scaled($canvasSize, 11);
        $lineStart = $this->scaled($canvasSize, 28);
        $lineEnd = $this->scaled($canvasSize, 70);
        $lineYTop = $this->scaled($canvasSize, 60);
        $lineYBottom = $canvasSize - $this->scaled($canvasSize, 60);
        $lineRightStart = $canvasSize - $this->scaled($canvasSize, 70);
        $lineRightEnd = $canvasSize - $this->scaled($canvasSize, 28);
        $lineStroke = $this->scaled($canvasSize, 4);
        $cornerFar = $canvasSize - $corner;

        if ($template === 'neon') {
            return <<<SVG
<defs>
    <linearGradient id="grad-neon" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#5c6cff"/>
        <stop offset="50%" stop-color="#ff5fa2"/>
        <stop offset="100%" stop-color="#ffc857"/>
    </linearGradient>
</defs>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f8f9ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="url(#grad-neon)" stroke-width="{$stroke}"/>
SVG;
        }

        if ($template === 'royal') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f7f3ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#5d4ea2" stroke-width="{$stroke}"/>
<rect x="{$innerInset}" y="{$innerInset}" width="{$innerSize}" height="{$innerSize}" rx="{$smallRadius}" fill="none" stroke="#b89be8" stroke-width="{$innerStroke}" stroke-dasharray="8 8"/>
SVG;
        }

        if ($template === 'dotted') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#fffaf4"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#ff9f43" stroke-width="{$stroke}" stroke-dasharray="3 10"/>
<circle cx="{$corner}" cy="{$corner}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$cornerFar}" cy="{$corner}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$corner}" cy="{$cornerFar}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$cornerFar}" cy="{$cornerFar}" r="{$dotRadius}" fill="#ffbe76"/>
SVG;
        }

        if ($template === 'sunburst') {
            return <<<SVG
<defs>
    <linearGradient id="grad-sun" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#ffefba"/>
        <stop offset="100%" stop-color="#ff9f43"/>
    </linearGradient>
</defs>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="url(#grad-sun)"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#f0932b" stroke-width="{$stroke}"/>
SVG;
        }

        if ($template === 'tech') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#eef5ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#2f6fb0" stroke-width="{$stroke}"/>
<path d="M {$lineStart} {$lineYTop} H {$lineEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineRightStart} {$lineYTop} H {$lineRightEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineStart} {$lineYBottom} H {$lineEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineRightStart} {$lineYBottom} H {$lineRightEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
SVG;
        }

        return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f6f7fb"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#6868ac" stroke-width="{$stroke}"/>
SVG;
    }

    private function extractInnerSvg(string $svg): string
    {
        $inner = preg_replace('/^.*?<svg[^>]*>/s', '', $svg);
        $inner = preg_replace('/<\/svg>\s*$/', '', (string) $inner);
        return trim((string) $inner);
    }

    private function scaled(int $size, int $unit): int
    {
        return (int) round(($size / 260) * $unit);
    }
}
