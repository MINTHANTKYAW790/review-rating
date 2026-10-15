<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Plan::class);

        $plans = Plan::query()->orderBy('price_mmk')->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        $this->authorize('create', Plan::class);

        return view('superadmin.plans.create');
    }

    public function store(PlanRequest $request): RedirectResponse
    {
        $this->authorize('create', Plan::class);

        Plan::query()->create($request->validated());

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        $this->authorize('update', $plan);

        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        $data = $request->validated();

        if ($plan->isProtected()) {
            $data['slug'] = $plan->slug;
            $data['is_account_free'] = $plan->is_account_free;
        }

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        if ($plan->subscriptions()->exists()) {
            $plan->update(['is_active' => false]);

            return redirect()->route('superadmin.plans.index')->with('success', 'Plan is in use, so it was deactivated instead of deleted.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted.');
    }
}
