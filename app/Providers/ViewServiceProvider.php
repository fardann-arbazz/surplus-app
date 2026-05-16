<?php

namespace App\Providers;

use App\Models\Orders;
use App\Models\Stores;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.seller', function ($view) {

            $store = null;
            $countOrder = 0;

            if (Auth::check() && Auth::user()->isSeller()) {

                $store = Stores::where('user_id', Auth::id())
                    ->select('id', 'name', 'is_active', 'img_url')
                    ->first();

                if ($store) {
                    $countOrder = Orders::where('store_id', $store->id)->Where('status', 'pending')->count();
                }
            }

            $view->with([
                'store' => $store,
                'countOrder' => $countOrder,
            ]);
        });
    }
}
