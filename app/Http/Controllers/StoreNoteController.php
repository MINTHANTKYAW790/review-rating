<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Models\Review;
use App\Models\Staff;
use App\Models\StoreNote;
use Illuminate\Http\Request;

class StoreNoteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', StoreNote::class);

        $stores = auth('web')->user()->accessibleStores()->orderBy('name')->get();
        $storeIds = $stores->pluck('id');

        $notes = StoreNote::query()
            ->with(['store', 'staff', 'review', 'author'])
            ->whereIn('store_id', $storeIds)
            ->when($request->filled('store_id'), fn ($query) => $query->where('store_id', $request->store_id))
            ->when($request->filled('staff_id'), fn ($query) => $query->where('staff_id', $request->staff_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $staffMembers = Staff::query()
            ->whereIn('store_id', $storeIds)
            ->orderBy('name')
            ->get();

        return view('notes.index', compact('notes', 'stores', 'staffMembers'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', StoreNote::class);

        [$stores, $staffMembers, $note] = $this->formData($request);

        return view('notes.create', compact('stores', 'staffMembers', 'note'));
    }

    public function store(StoreNoteRequest $request)
    {
        $this->authorize('create', StoreNote::class);

        $note = StoreNote::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('notes.show', $note)->with('success', __('messages.note_created'));
    }

    public function show(StoreNote $note)
    {
        $this->authorize('view', $note);
        $note->load(['store', 'staff', 'review', 'author']);

        return view('notes.show', compact('note'));
    }

    public function edit(StoreNote $note)
    {
        $this->authorize('update', $note);

        [$stores, $staffMembers] = $this->formData();

        return view('notes.edit', compact('note', 'stores', 'staffMembers'));
    }

    public function update(StoreNoteRequest $request, StoreNote $note)
    {
        $this->authorize('update', $note);

        $note->update($request->validated());

        return redirect()->route('notes.show', $note)->with('success', __('messages.note_updated'));
    }

    public function destroy(StoreNote $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')->with('success', __('messages.note_deleted'));
    }

    /**
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2?: StoreNote}
     */
    private function formData(?Request $request = null): array
    {
        $stores = auth('web')->user()->accessibleStores()->orderBy('name')->get();
        $staffMembers = Staff::query()
            ->whereIn('store_id', $stores->pluck('id'))
            ->orderBy('name')
            ->get();

        if (! $request) {
            return [$stores, $staffMembers];
        }

        $note = new StoreNote([
            'store_id' => $request->query('store_id'),
            'staff_id' => $request->query('staff_id'),
            'review_id' => $request->query('review_id'),
            'status' => StoreNote::STATUS_OPEN,
        ]);

        if ($request->filled('review_id')) {
            $review = Review::query()
                ->whereKey($request->query('review_id'))
                ->whereIn('store_id', $stores->pluck('id'))
                ->first();

            if ($review) {
                $note->store_id = $review->store_id;
                $note->staff_id = $note->staff_id ?: $review->staff_id;
                $note->review_id = $review->id;
            }
        }

        return [$stores, $staffMembers, $note];
    }
}
