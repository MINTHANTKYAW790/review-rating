<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use App\Models\User;
use App\Services\BillingService;
use App\Services\QrCodeService;
use App\Services\ReviewSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StoreController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
        private readonly ReviewSubmissionService $reviewSubmissionService,
        private readonly BillingService $billingService,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Store::class);
        $stores = auth('web')->user()->accessibleStores()->withAvg('reviews', 'rating_store')->latest()->get();

        return view('store.index', compact('stores'));
    }

    public function show(Store $store)
    {
        $this->authorize('view', $store);

        return view('store.show', compact('store'));
    }

    public function create()
    {
        if (! auth('web')->user()?->can('create_stores')) {
            abort(403);
        }

        if (! auth('web')->user()->canOpenAnotherStore()) {
            return redirect()->route('billing.add-store')->with('error', __('messages.buy_plan_to_add_store'));
        }

        return view('store.create', [
            'managers' => $this->managerOptions(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        if (! auth('web')->user()?->can('create_stores')) {
            abort(403);
        }

        if (! auth('web')->user()->canOpenAnotherStore()) {
            return redirect()->route('billing.add-store')->with('error', __('messages.buy_plan_to_add_store'));
        }

        $validated = $request->validated();
        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $store = DB::transaction(function () use ($validated, $logoPath, $request) {
            $attributes = [
                'user_id' => auth('web')->user()->id,
                'manager_id' => $this->assignedManagerId($validated),
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'description' => $validated['description'] ?? null,
                'logo_url' => $logoPath,
                'qr_identifier' => Str::uuid(),
            ];

            if ($request->hasFile('logo')) {
                $attributes['logo_image'] = file_get_contents($request->file('logo')->getRealPath());
            }

            $store = Store::create($attributes);

            $this->billingService->provisionNewStore(auth('web')->user(), $store);
            $this->ensureExclusiveManager($store);

            return $store;
        });

        return redirect()->route('store.index')->with('success', 'Store created successfully!');
    }

    public function edit(Store $store)
    {
        $this->authorize('update', $store);

        return view('store.edit', [
            'store' => $store,
            'managers' => $this->managerOptions(),
        ]);
    }

    public function update(StoreRequest $request, Store $store)
    {
        $this->authorize('update', $store);

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_url'] = $logoPath;
            $validated['logo_image'] = file_get_contents($request->file('logo')->getRealPath());
        }

        unset($validated['logo']);
        $validated['manager_id'] = $this->assignedManagerId($validated, $store->manager_id);

        $store->update($validated);
        $this->ensureExclusiveManager($store);

        return redirect()->route('store.index')->with('success', 'Store updated successfully!');
    }

    public function showQr()
    {
        $stores = auth('web')->user()->accessibleStores()->orderBy('name')->get();

        foreach ($stores as $store) {
            $store->qrUrl = url('public/qr/'.$store->slug);
        }

        return view('qr.index', compact('stores'));
    }

    public function showStore($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();

        if (! $store->is_active || $store->hasReachedReviewCap()) {
            return view('public.store-closed', compact('store'));
        }

        $store->load('staff');

        return view('public.review', compact('store'));
    }

    public function submitReview(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        $result = $this->reviewSubmissionService->submit($request, $store, $slug);

        return $result['redirect'];
    }

    public function downloadQr(Store $store)
    {
        $this->authorize('view', $store);
        $templates = $this->qrCodeService->templates();

        return view('qr.templates', compact('store', 'templates'));
    }

    public function getQrImage(Store $store)
    {
        $this->authorize('view', $store);
        $template = request()->query('template', 'classic');
        $requestedSize = (int) request()->query('size', 260);
        $canvasSize = max(260, min($requestedSize, 2400));
        $svg = $this->qrCodeService->buildStyledSvg($store, $template, $canvasSize);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function downloadStyledQr(Store $store, string $template)
    {
        $this->authorize('view', $store);
        $templateKey = $this->qrCodeService->sanitizeTemplate($template);
        $svg = $this->qrCodeService->buildStyledSvg($store, $templateKey, 1100);
        $fileName = $store->slug.'-qr-'.$templateKey.'.svg';

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }

    private function managerOptions()
    {
        if (! Role::where('name', 'manager')->where('guard_name', 'web')->exists()) {
            return collect();
        }

        return User::role('manager')
            ->where('created_by', auth()->id())
            ->orderBy('name')
            ->get();
    }

    private function assignedManagerId(array $validated, ?int $current = null): ?int
    {
        if (! auth('web')->user()?->can('create_stores') && ! auth('web')->user()?->can('edit_stores')) {
            return $current;
        }

        if (! array_key_exists('manager_id', $validated)) {
            return $current;
        }

        return $validated['manager_id'] !== null ? (int) $validated['manager_id'] : null;
    }

    private function ensureExclusiveManager(Store $store): void
    {
        if (! $store->manager_id) {
            return;
        }

        Store::query()
            ->where('user_id', $store->user_id)
            ->where('manager_id', $store->manager_id)
            ->whereKeyNot($store->id)
            ->update(['manager_id' => null]);
    }
}
