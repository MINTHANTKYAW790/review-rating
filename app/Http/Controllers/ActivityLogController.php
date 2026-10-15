<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    public function index(): View
    {
        $activities = Activity::query()
            ->with('causer')
            ->latest()
            ->paginate(25);

        return view('activity-log.index', compact('activities'));
    }
}
