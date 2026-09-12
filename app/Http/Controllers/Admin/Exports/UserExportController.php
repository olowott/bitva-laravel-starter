<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Concerns\HandlesTableSorting;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Exports\CsvExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Queries\UserQuery;

class UserExportController extends Controller
{
    use HandlesTableSorting;

    public function __invoke(
        Request $request,
        CsvExportService $csvExportService,
        UserQuery $userQuery
    ): StreamedResponse {
        [$sort, $direction] = $this->resolveTableSort(
            $request,
            [
                'name',
                'email',
                'created_at',
                'is_active',
            ]
        );

        $users = $userQuery
            ->build($request)
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
            ->cursor();

        return $csvExportService->download(
            filename: 'users-' . now()->format('Y-m-d-His') . '.csv',

            headings: [
                'Name',
                'Email',
                'Role',
                'Status',
                'Created At',
            ],

            rows: $users,

            mapRow: fn(User $user) => [
                $user->name,
                $user->email,
                $user->roles->pluck('name')->implode(', '),
                $user->is_active ? 'Active' : 'Inactive',
                $user->created_at?->format('Y-m-d H:i:s'),
            ],
        );
    }
}
