<?php

namespace App\Concerns;

use Illuminate\Http\Request;

trait HandlesTableSorting
{
    protected function resolveTableSort(
        Request $request,
        array $allowedSorts,
        string $defaultSort = 'created_at',
        string $defaultDirection = 'desc'
    ): array {
        $requestedSort = $request->string('sort')->toString();

        $sort = in_array(
            $requestedSort,
            $allowedSorts,
            true
        )
            ? $requestedSort
            : $defaultSort;

        $requestedDirection = $request
            ->string('direction')
            ->toString();

        $direction = in_array(
            $requestedDirection,
            ['asc', 'desc'],
            true
        )
            ? $requestedDirection
            : $defaultDirection;

        return [$sort, $direction];
    }
}
