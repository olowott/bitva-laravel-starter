<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Concerns\HandlesTableSorting;
use App\Http\Controllers\Controller;
use App\Queries\ActivityLogQuery;
use App\Services\Exports\CsvExportService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogExportController extends Controller
{
    use HandlesTableSorting;

    public function __invoke(
        Request $request,
        CsvExportService $csvExportService,
        ActivityLogQuery $activityLogQuery
    ): StreamedResponse {
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
            ->cursor();

        return $csvExportService->download(
            filename: 'activity-logs-' . now()->format('Y-m-d-His') . '.csv',

            headings: [
                'Activity',
                'Event',
                'Actor',
                'Subject Type',
                'Subject ID',
                'Created At',
            ],

            rows: $activities,

            mapRow: fn(Activity $activity) => [
                $activity->description,
                $activity->event,
                $activity->causer?->name
                ?? $activity->causer?->email
                ?? 'System',
                $activity->subject_type
                ? class_basename($activity->subject_type)
                : null,
                $activity->subject_id,
                $activity->created_at?->format('Y-m-d H:i:s'),
            ],
        );
    }
}
