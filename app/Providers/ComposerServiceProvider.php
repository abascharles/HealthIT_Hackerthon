<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(
            ['layouts.sidenav','home.home','layouts.topnav'],'App\Http\ViewComposers\DashboardComposer'
            // ['layouts.sidenav','home','layouts.topnav'],'App\Http\ViewComposers\DashboardComposerOld'
        );

        view()->composer(
            'home.home', 'App\Http\ViewComposers\DashboardComposer@tasks'
        );

        // view()->composer(
        //     'layouts.master', 'App\Http\ViewComposers\DashboardComposer@users'
        // );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
