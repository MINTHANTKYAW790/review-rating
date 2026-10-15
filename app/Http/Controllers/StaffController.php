<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Staff::class);

        $stores = auth('web')->user()->accessibleStores()
            ->with(['staff' => function ($query) {
                $query->withAvg('reviews', 'rating_staff');
            }])
            ->get();

        return view('staff.index', compact('stores'));
    }

    public function create()
    {
        $this->authorize('create', Staff::class);
        $stores = auth('web')->user()->stores;

        return view('staff.create', compact('stores'));
    }

    public function store(StaffRequest $request)
    {
        $this->authorize('create', Staff::class);

        $validated = $request->validated();

        if ($request->hasFile('image_url')) {
            $validated['image_url'] = $request->file('image_url')->store('staff_images', 'public');
        }

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff added successfully.');
    }

    public function show(Staff $staff)
    {
        $this->authorize('view', $staff);

        return redirect()->route('staff.index');
    }

    public function edit(Staff $staff)
    {
        $this->authorize('update', $staff);
        $stores = auth('web')->user()->stores;

        return view('staff.edit', compact('staff', 'stores'));
    }

    public function update(StaffRequest $request, Staff $staff)
    {
        $this->authorize('update', $staff);

        $validated = $request->validated();

        if ($request->hasFile('image_url')) {
            if ($staff->image_url) {
                Storage::disk('public')->delete($staff->image_url);
            }
            $validated['image_url'] = $request->file('image_url')->store('staff_images', 'public');
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $this->authorize('delete', $staff);

        if ($staff->image_url) {
            Storage::disk('public')->delete($staff->image_url);
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
}
