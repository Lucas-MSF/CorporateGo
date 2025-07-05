<?php

namespace App\Providers;

use App\Events\TravelOrder\TravelOrderStatusChangedEvent;
use App\Interfaces\Repositories\TravelOrderRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\AuthServiceInterface;
use App\Interfaces\Services\TravelOrderServiceInterface;
use App\Interfaces\Services\UserServiceInterface;
use App\Listeners\TravelOrder\SendTravelOrderStatusNotification;
use App\Repositories\TravelOrderRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\TravelOrderService;
use App\Services\UserService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        $this->app->bind(UserServiceInterface::class,UserService::class);
        $this->app->bind(AuthServiceInterface::class,AuthService::class);
        $this->app->bind(TravelOrderRepositoryInterface::class,TravelOrderRepository::class);
        $this->app->bind(TravelOrderServiceInterface::class,TravelOrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            TravelOrderStatusChangedEvent::class,
            SendTravelOrderStatusNotification::class
        );
    }
}
