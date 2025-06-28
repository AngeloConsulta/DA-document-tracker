<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            if (auth()->check()) {
                $notifications = Notification::where('user_id', auth()->id())
                    ->latest()
                    ->take(5)
                    ->get();
                
                $unreadNotifications = Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();

                $view->with([
                    'notifications' => $notifications,
                    'unreadNotifications' => $unreadNotifications
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotifications' => 0
                ]);
            }
        });
    }
} 