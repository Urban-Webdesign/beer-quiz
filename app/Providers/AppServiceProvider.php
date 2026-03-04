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
        // SECURITY: Model::unguard() was removed — it disabled mass-assignment protection on ALL models.
        // Each model should declare its own $fillable or $guarded instead.

        // Fix for "Call to a member function make() on null" error in PendingBroadcast destructor
        // This error occurs when the application terminates and the container is set to null
        // before PendingBroadcast's destructor finishes executing
        //
        // The root cause is in Illuminate\Broadcasting\PendingBroadcast::__destruct()
        // which calls shouldDispatchToOtherConnections() that tries to use the Validator
        // which needs the container to resolve custom validation rules
        //
        // Solution: Register a terminating callback that runs BEFORE the container is nulled
        // to ensure any pending broadcasts are properly handled
        $this->app->terminating(function () {
            // Force garbage collection of objects before container is destroyed
            // This ensures PendingBroadcast destructors run while container is still available
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        });
    }
}
