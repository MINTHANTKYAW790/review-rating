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
            'qr_identifier' => 'required|string|unique:stores,qr_identifier,' . $store->id,
            // Add more validations as needed
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
        $this->authorize('view', $store); // Authorize downloading QR for this specific store
        // 1. Generate as SVG (No imagick needed!)
        // 2. We use url("/qr/{$store->slug}") to avoid 'public/' in the string
        $qrCode = QrCode::size(1000)
            ->margin(1)
            ->generate(url("/public/qr/" . $store->slug));

        $fileName = $store->slug . '-qr.svg';

        // Return as an SVG download
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function getQrImage(Store $store)
    {
        $this->authorize('view', $store); // Authorize viewing QR image for this specific store
        $qrCode = QrCode::size(200) // Smaller size for preview
            ->margin(1)
            ->generate(url("/public/qr/" . $store->slug));

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }
}
