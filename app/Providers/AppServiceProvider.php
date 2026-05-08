<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.admin.topbar', function ($view) {
            $user = Auth::user();

            if ($user) {
                $notifications = $user->notifications()->latest()->take(10)->get();
                $unreadCount = $user->unreadNotifications()->count();

                // $notifications = cache()->remember(
                //     "user_{$user->id}_notifications",
                //     10, // detik
                //     fn () => $user->notifications()->latest()->take(10)->get()
                // );

                // $unreadCount = cache()->remember(
                //     "user_{$user->id}_unread_count",
                //     10,
                //     fn () => $user->unreadNotifications()->count()
                // );

                $view->with([
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount
                ]);
            }
        });
    }
}
