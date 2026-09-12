<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogQuery
{
    public function build(Request $request): Builder
    {
        return Activity::query()
            ->with([
                'causer',
                'subject',
            ])
            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request) {
                    $search = $request
                        ->string('search')
                        ->toString();

                    $query->where(
                        function (Builder $query) use ($search) {
                            $query
                                ->where(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'event',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('event'),
                fn(Builder $query) => $query->where(
                    'event',
                    $request
                        ->string('event')
                        ->toString()
                )
            );
    }
}
