<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\Stores;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.seller', function ($view) {
            $store = null;

            if (Auth::check() && Auth::user()->isSeller()) {
                $store = Stores::where('user_id', Auth::id())
                    ->select('id', 'name', 'is_active', 'img_url')
                    ->first();
            }

            $view->with('store', $store);
        });
    }
}
