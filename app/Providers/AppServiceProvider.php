<?php

namespace App\Providers;

use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\TravelRequestRepositoryInterface;
use App\Interfaces\TravelRequestServiceInterface;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Interfaces\AuthServiceInterface;
use App\Services\AuthService;

use App\Repositories\TravelRequestRepository;
use App\Services\TravelRequestService;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);


        $this->app->bind(TravelRequestServiceInterface::class, TravelRequestService::class);
        $this->app->bind(TravelRequestRepositoryInterface::class, TravelRequestRepository::class);
        
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
