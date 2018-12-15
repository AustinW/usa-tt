<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google_Client;
use Revolution\Google\Sheets\Sheets;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sheets', function ($app) {
            $client = new Google_Client();
            $client->setApplicationName(config('google.application_name'));
            $client->setClientId(config('google.client_id'));
            $client->setScopes(config('google.scopes'));
            $client->setAuthConfig(config('google.service.file'));
            $client->useApplicationDefaultCredentials();

            if ($client->isAccessTokenExpired()) {
                $client->refreshTokenWithAssertion();
            }

            $service_token = $client->getAccessToken();

            $service = new \Google_Service_Sheets($client);

            $sheets = new Sheets();
            $sheets->setService($service);

            return $sheets;
        });
    }
}
