<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserQuery
{
    public function build(Request $request): Builder
    {
        return User::query()
            ->with('roles')
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
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('role'),
                fn(Builder $query) => $query->role(
                    $request
                        ->string('role')
                        ->toString()
                )
            )
            ->when(
                $request->filled('status'),
                function (Builder $query) use ($request) {
                    $status = $request
                        ->string('status')
                        ->toString();

                    if ($status === 'active') {
                        $query->where(
                            'is_active',
                            true
                        );
                    }

                    if ($status === 'inactive') {
                        $query->where(
                            'is_active',
                            false
                        );
                    }
                }
            );
    }
}
