<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Carbon::setLocale('es');

        if (
            config('app.env') === 'production' ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
        ) {
            URL::forceScheme('https');
        }
    }
}
