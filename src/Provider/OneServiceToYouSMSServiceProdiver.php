<?php
/**
 * Author: Danny Villa Kalonji
 * Date: 03/01/2020
 * Time: 02:44
 */

namespace Oxanfoxs\OneServiceToYouSMS\Provider;

use Illuminate\Support\ServiceProvider;
use Oxanfoxs\OneServiceToYouSMS\MessageApi;

class OneServiceToYouSMSServiceProdiver extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/1s2u.php' => config_path('1s2u.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('messageApi', function($app) {
            $return = $app->make(MessageApi::class);

            return $return;
        });
    }
}