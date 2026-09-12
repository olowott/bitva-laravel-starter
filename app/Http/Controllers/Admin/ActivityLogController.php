<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use App\Concerns\HandlesTableSorting;

class ActivityLogController extends Controller
{

    use HandlesTableSorting;

    public function index(Request $request): View
    {

        [$sort, $direction] = $this->resolveTableSort(
            $request,
            [
                'description',
                'event',
                'created_at',
            ]
        );

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
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
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
