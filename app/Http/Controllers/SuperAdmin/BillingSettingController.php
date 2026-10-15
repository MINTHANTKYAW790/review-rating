<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingSettingRequest;
use App\Models\BillingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingSettingController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        return view('superadmin.billing-settings.edit', [
            'settings' => BillingSetting::current(),
        ]);
    }

    public function update(BillingSettingRequest $request): RedirectResponse
    {
        BillingSetting::current()->update($request->validated());

        return redirect()->route('superadmin.billing-settings.edit')->with('success', 'Transfer details updated.');
    }
}
