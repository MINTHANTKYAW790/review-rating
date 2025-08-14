<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Review;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    // For index
    public function index()
    {
        $stores = Store::latest()->get();
        return view('store.index', compact('stores'));
    }

    // For show
    public function show($id)
    {
        $store = Store::findOrFail($id);
        return view('store.show', compact('store'));
    }

    public function create()
    {
        return view('store.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Store::create([
            'user_id' => auth('web')->user()->id(),
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'logo_url' => $logoPath,
            'qr_identifier' => Str::uuid(),
        ]);

        return redirect()->route('store.create')->with('success', 'Store created successfully!');
    }

    public function edit($id)
    {
        $store = Store::findOrFail($id);
        return view('store.edit', compact('store'));
    }

    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qr_identifier' => 'required|string|unique:stores,qr_identifier,' . $store->id,
            // Add more validations as needed
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_url'] = $logoPath;
        }

        $store->update($validated);

        return redirect()->route('store.index')->with('success', 'Store updated successfully!');
    }

    public function showQr()
    {
        info("showQr called");
        $store = auth('web')->user()->store;
        info("store: " . json_encode($store));
        if (!$store) {
            info("no store found");
            return back()->with('error', 'No store found.');
        }
        info("yes exists");
        $url = url("/qr/" . $store->slug); // example: http://127.0.0.1:8000/cosmeticstore

        return view('qr.index', [
            'qrUrl' => $url,
        ]);
    }

    public function showStore($slug)
    {
        info("showStore called with slug: " . $slug);
        $store = Store::where('slug', $slug)->firstOrFail();

        return view('public.review', compact('store'));
    }


    public function submitReview(Request $request, $slug)
    {
        info("submitReview called with slug: " . $slug);
        $store = Store::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'rating_store' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'staff_id' => 'nullable|exists:staff,id',
            'rating_staff' => 'nullable|integer|min:1|max:5',
            'comment_staff' => 'nullable|string',
        ]);

        info("validated data: " . json_encode($validated));
        info($validated['rating_store']);
        info($validated['rating_staff']);


        Review::create([
            'store_id' => $store->id,
            'staff_id' => $validated['staff_id'] ?? null,
            'rating_store' => $validated['rating_store'],
            'rating_staff' => $validated['staff_id'] ? ($validated['rating_staff'] ?? null) : null,
            'comment' => $validated['comment'] ?? null,
            'comment_staff' => $validated['comment_staff'] ?? null,
        ]);

        return redirect()->route('public.review', $slug)->with('success', 'Thank you for your feedback!');
    }
}
