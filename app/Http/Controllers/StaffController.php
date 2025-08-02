<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $store = auth('web')->user()->store;

    if (!$store || $store == null) {
        // Optionally redirect or just show a blank view
        return view('staff.index', ['staff' => collect()]); // empty collection
    }

    $staff = Staff::where('store_id', $store->id)->get();

    return view('staff.index', compact('staff'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('staff_images', 'public');
            $validated['image_url'] = $path;
        }

        $validated['store_id'] = auth('web')->user()->store->id;

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
