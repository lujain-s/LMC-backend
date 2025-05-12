<?php

namespace App\Providers;

use App\Models\CourseSchedule;
use Illuminate\Support\ServiceProvider;
use App\Observers\CourseScheduleObserver;

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
    public function boot()
    {
        CourseSchedule::observe(CourseScheduleObserver::class);
    }
}
