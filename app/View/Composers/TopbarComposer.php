<?php

namespace App\View\Composers;

use Illuminate\View\View;

class TopbarComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (!$user) {
            $view->with([
                'unreadCount' => 0,
                'recentNotifications' => collect(),
            ]);

            return;
        }

        $view->with([
            'unreadCount' => $user
                ->unreadNotifications()
                ->count(),

            'recentNotifications' => $user
                ->notifications()
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
