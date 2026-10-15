<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTriageRequest;
use App\Models\Review;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Review::class);

        if ($request->ajax() || $request->wantsJson()) {
            $userStoreIds = auth('web')->user()->accessibleStores()->pluck('id');

            $query = Review::query()
                ->with(['store', 'staff'])
                ->whereIn('store_id', $userStoreIds)
                ->select('reviews.*');

            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('staff_id')) {
                $query->where('staff_id', $request->staff_id);
            }

            if ($request->filled('rating_store')) {
                $query->where('rating_store', $request->rating_store);
            }

            if ($request->filled('rating_staff')) {
                $query->where('rating_staff', $request->rating_staff);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $queue = $request->input('queue', Review::STATUS_INBOX);

            match ($queue) {
                Review::STATUS_INBOX => $query->where('follow_up_status', Review::STATUS_INBOX),
                Review::STATUS_DONE => $query->where('follow_up_status', Review::STATUS_DONE),
                'starred' => $query->where('is_important', true),
                'wants_contact' => $query->where('contact_requested', true),
                default => null,
            };

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('shop_name', fn (Review $review) => $review->store?->name ?? '-')
                ->addColumn('shop_star', function (Review $review) {
                    $rating = max(0, min(5, (int) $review->rating_store));
                    $html = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $html .= $i <= $rating
                            ? '<i class="fas fa-star text-[#f4b740]"></i>'
                            : '<i class="far fa-star text-gray-300"></i>';
                    }

                    return '<div class="star-display">'.$html.'</div>';
                })
                ->addColumn('staff_name', fn (Review $review) => $review->staff?->name ?? '-')
                ->addColumn('staff_star', function (Review $review) {
                    $rating = max(0, min(5, (int) $review->rating_staff));
                    $html = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $html .= $i <= $rating
                            ? '<i class="fas fa-star text-[#f4b740]"></i>'
                            : '<i class="far fa-star text-gray-300"></i>';
                    }

                    return '<div class="star-display">'.$html.'</div>';
                })
                ->addColumn('customer_phone', function (Review $review) {
                    $phone = e($review->customer_phone ?? '-');
                    if (! $review->contact_requested) {
                        return $phone;
                    }

                    return $phone.' <span class="badge badge-info ml-1">'.e(__('messages.wants_contact')).'</span>';
                })
                ->addColumn('attention', function (Review $review) {
                    $parts = [];

                    if ($review->isUnread()) {
                        $parts[] = '<span class="review-unread-dot" title="'.e(__('messages.review_unread')).'"></span>';
                    }

                    if ($review->is_important) {
                        $parts[] = '<i class="fas fa-star text-warning" title="'.e(__('messages.review_starred')).'"></i>';
                    }

                    if ($review->isDone()) {
                        $parts[] = '<span class="badge badge-secondary">'.e(__('messages.review_queue_done')).'</span>';
                    }

                    if ($parts === []) {
                        return '<span class="text-muted">-</span>';
                    }

                    return '<div class="review-attention d-flex align-items-center">'.implode('', $parts).'</div>';
                })
                ->addColumn('review_text', fn (Review $review) => Str::limit($review->comment ?? 'No review text provided', 80, '...'))
                ->addColumn('remark_preview', fn (Review $review) => Str::limit($review->admin_remark ?? '-', 60, '...'))
                ->addColumn('reviewed_at', fn (Review $review) => $review->created_at ? $review->created_at->format('d M Y h:i A') : '-')
                ->addColumn('action', fn (Review $review) => '<a href="'.route('review.show', $review->id).'" class="details-link">Details</a>')
                ->rawColumns(['shop_star', 'staff_star', 'customer_phone', 'attention', 'action'])
                ->make(true);
        }

        $stores = auth('web')->user()->accessibleStores()->orderBy('name')->get();
        $staffMembers = Staff::query()
            ->whereIn('store_id', $stores->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('admin.reviews.index', compact('stores', 'staffMembers'));
    }

    public function show(Review $review)
    {
        $this->authorize('view', $review);

        if (is_null($review->read_at) && auth('web')->user()->can('update', $review)) {
            $review->forceFill(['read_at' => now()])->save();
        }

        $review->load(['store', 'staff', 'remarkedBy']);

        return view('admin.reviews.show', compact('review'));
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return redirect()->route('review.index')->with('success', 'Review deleted successfully.');
    }

    public function deleteComment(Review $review)
    {
        $this->authorize('update', $review);
        $review->comment = null;
        $review->save();

        return redirect()->route('review.index')->with('success', 'Comment deleted successfully.');
    }

    public function updateTriage(ReviewTriageRequest $request, Review $review)
    {
        $remark = $request->validated()['admin_remark'] ?? null;
        $remarkChanged = ($review->admin_remark ?? '') !== ($remark ?? '');

        $review->admin_remark = $remark;

        if ($remarkChanged) {
            $review->remarked_by = $remark ? $request->user()->id : null;
            $review->remarked_at = $remark ? now() : null;
        }

        $review->save();

        return redirect()->route('review.show', $review)->with('success', __('messages.review_remark_saved'));
    }

    public function toggleStar(Review $review)
    {
        $this->authorize('update', $review);

        $review->forceFill(['is_important' => ! $review->is_important])->save();

        return redirect()->route('review.show', $review);
    }

    public function markDone(Review $review)
    {
        $this->authorize('update', $review);

        $review->forceFill(['follow_up_status' => Review::STATUS_DONE])->save();

        return redirect()->route('review.show', $review)->with('success', __('messages.review_marked_done'));
    }

    public function reopen(Review $review)
    {
        $this->authorize('update', $review);

        $review->forceFill(['follow_up_status' => Review::STATUS_INBOX])->save();

        return redirect()->route('review.show', $review)->with('success', __('messages.review_reopened'));
    }

    public function markRead(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        if (is_null($review->read_at)) {
            $review->forceFill(['read_at' => now()])->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->unreadPayload($review));
        }

        return redirect()->route('review.show', $review);
    }

    /**
     * @return array{success: bool, unread_count: int, review_id: int}
     */
    private function unreadPayload(Review $review): array
    {
        $userStoreIds = auth('web')->user()->accessibleStores()->pluck('id');
        $unreadCount = Review::whereIn('store_id', $userStoreIds)
            ->whereNull('read_at')
            ->count();

        return [
            'success' => true,
            'unread_count' => $unreadCount,
            'review_id' => $review->id,
        ];
    }
}
