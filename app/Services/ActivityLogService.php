<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?string $event = null
    ): void {
        $activity = activity();

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        }

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        if ($event) {
            $activity->event($event);
        }

        $activity->log($description);
    }
}
