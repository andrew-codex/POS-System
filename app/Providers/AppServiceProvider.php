<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\RolePermission;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            $user = Auth::user();
            $rolePermissions = [];
            $showProductsMenu = false;

            if ($user) {
                $rolePermissions = RolePermission::whereRaw(
                        'LOWER(TRIM(role)) = ?',
                        [strtolower(trim($user->role))]
                    )
                    ->pluck('permission')
                    ->map(fn ($p) => strtolower(trim($p)))
                    ->toArray();

                $showProductsMenu =
                    in_array('manage_products', $rolePermissions) ||
                    in_array('view_products', $rolePermissions) ||
                    in_array('view_categories', $rolePermissions) ||
                    in_array('view_stock', $rolePermissions);
            }

            $settings = Setting::pluck('value', 'key')->toArray();

            $view->with([
                'rolePermissions'   => $rolePermissions,
                'showProductsMenu'  => $showProductsMenu,
                'settings'          => $settings,
            ]);
        });
    }
}