<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Interfaces\ServiceClientRepositoryInterface;
use App\Repositories\Eloquent\ServiceClientRepository;
use App\Interfaces\ApiTokenRepositoryInterface;
use App\Repositories\Eloquent\ApiTokenRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ServiceClientRepositoryInterface::class, ServiceClientRepository::class);
        $this->app->bind(ApiTokenRepositoryInterface::class, ApiTokenRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
