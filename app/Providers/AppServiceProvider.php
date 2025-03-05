<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\Product;
use App\Policies\ProductPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        Role::class => RolePolicy::class,
        Product::class => ProductPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
