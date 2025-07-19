<?php

namespace App\Providers;

use App\Models\TravelRequest;
use App\Policies\TravelRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
