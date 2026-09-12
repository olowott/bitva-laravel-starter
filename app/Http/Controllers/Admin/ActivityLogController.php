<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->when(
                $request->filled('search'),
                fn($query) => $query->where(
                    'description',
                    'like',
                    '%' . $request->string('search') . '%'
                )
            )
            ->when(
                $request->filled('event'),
                fn($query) => $query->where(
                    'event',
                    $request->string('event')
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();


        $events = Activity::query()
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view(
            'admin.activity.index',
            compact('activities', 'events')
        );
    }
}
