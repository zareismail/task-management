<?php

namespace Zareismail\Task;

use Illuminate\Support\Facades\Gate; 
use Illuminate\Contracts\Support\DeferrableProvider; 
use Illuminate\Support\ServiceProvider as LaravelServiceProvider; 
use Laravel\Nova\Nova as LaravelNova; 

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{ 
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Gate::policy(Models\Task::class, Policies\Task::class);

        LaravelNova::resources([
            Nova\Activity::class, 
            Nova\Task::class,
            Nova\Work::class,
        ]); 

        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('referrers', function($userModel) {
            return $userModel
                    ->belongsToMany(get_class($userModel), 'task_referrers', 'agent_id', 'user_id')
                    ->withPivot('end_date')
                    ->wherePivot('end_date', '>=', now());
        }); 

        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('substitutes', function($userModel) {
            return $userModel
                    ->belongsToMany(get_class($userModel), 'task_referrers', 'user_id', 'agent_id')
                    ->withPivot('end_date')
                    ->wherePivot('end_date', '>=', now());
        }); 
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [
            \Laravel\Nova\Events\ServingNova::class,
            \Illuminate\Console\Events\ArtisanStarting::class,
        ];
    }
}
