<?php

namespace App\Providers;

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
        // Only override if accessed via a subfolder (XAMPP style)
        $root = request()->getBasePath();

        if (!empty($root) && $root !== '/') {
            \Livewire\Livewire::setUpdateRoute(function ($handle) use ($root) {
                return \Illuminate\Support\Facades\Route::post($root . '/livewire/update', $handle);
            });
        }
    }

    




}
