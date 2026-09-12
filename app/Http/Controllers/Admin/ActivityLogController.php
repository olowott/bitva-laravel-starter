<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use App\Concerns\HandlesTableSorting;
use App\Queries\ActivityLogQuery;

class ActivityLogController extends Controller
{

    use HandlesTableSorting;

    public function index(Request $request, ActivityLogQuery $activityLogQuery): View
    {

        [$sort, $direction] = $this->resolveTableSort(
            $request,
            [
                'description',
                'event',
                'created_at',
            ]
        );

        $activities = $activityLogQuery
            ->build($request)
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
