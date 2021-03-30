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
        Gate::policy(Models\TaskTeam::class, Policies\Team::class);

        LaravelNova::resources([
            Nova\Activity::class, 
            Nova\Team::class,
            Nova\Task::class,
            Nova\Work::class,
        ]); 

        Models\Task::observe(Observers\TaskObserver::class);

        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('referrers', function($userModel) {
            return $userModel
                    ->belongsToMany($userModel, 'task_substitutes', 'agent_id', 'user_id')
                    ->withPivot('end_date')
                    ->wherePivot('end_date', '>=', now());
        }); 

        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('substitutes', function($userModel) {
            return $userModel
                    ->belongsToMany($userModel, 'task_substitutes', 'user_id', 'agent_id')
                    ->withPivot('end_date')
                    ->wherePivot('end_date', '>=', now());
        }); 

        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('teams', function($userModel) {
            return $userModel->belongsToMany(Models\TaskTeam::class, 'task_team_user', 'user_id', 'task_team_id');
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
