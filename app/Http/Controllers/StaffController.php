<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = auth('web')->user()->stores() // Access the relationship builder
            ->with(['staff' => function ($query) {
                $query->withAvg('reviews', 'rating_staff');
            }])
            ->get(); // Retrieve the collection

        return view('staff.index', compact('stores'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stores = auth('web')->user()->stores;
        return view('staff.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'image_url' => 'nullable|image|max:2048',
            'store_id' => 'required|exists:stores,id',
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('staff_images', 'public');
            $validated['image_url'] = $path;
        }

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $staff = Staff::findOrFail($id);
        // Ensure the staff belongs to the authenticated user's store
        if (!auth('web')->user()->stores->pluck('id')->contains($staff->store_id)) {
            abort(403);
        }
        $stores = auth('web')->user()->stores;
        return view('staff.edit', compact('staff', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $staff = Staff::findOrFail($id);
        // Ensure the staff belongs to the authenticated user's store
        if (!auth('web')->user()->stores->pluck('id')->contains($staff->store_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'image_url' => 'nullable|image|max:2048',
            'store_id' => 'required|exists:stores,id',
        ]);

        if ($request->hasFile('image_url')) {
            // Delete old image if exists
            if ($staff->image_url) {
                Storage::disk('public')->delete($staff->image_url);
            }
            $path = $request->file('image_url')->store('staff_images', 'public');
            $validated['image_url'] = $path;
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = Staff::findOrFail($id);
        // Ensure the staff belongs to the authenticated user's store
        if (!auth('web')->user()->stores->pluck('id')->contains($staff->store_id)) {
            abort(403);
        }

        // Delete image if exists
        if ($staff->image_url) {
            Storage::disk('public')->delete($staff->image_url);
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
}
