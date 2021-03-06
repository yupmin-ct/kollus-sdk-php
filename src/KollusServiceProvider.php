<?php

namespace Kollus\Component;

use Illuminate\Support\ServiceProvider;

class KollusServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/kollus.php' => config_path('kollus.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client\ApiClient::class, function () {

            $kollusApiClient = new Client\ApiClient(
                config('kollus.domain'),
                config('kollus.api_version'),
                config('kollus.language')
            );

            $serviceAccount = new Container\ServiceAccount([
                'key' => config('kollus.service_account.key'),
                'api_access_token' => config('kollus.service_account.api_access_token')
            ]);

            $kollusApiClient->setServiceAccount($serviceAccount);
            $kollusApiClient->connect();
            if (config('kollus.use_https')) {
                $kollusApiClient->setSchema('https');
            }

            return $kollusApiClient;
        });

        $this->app->singleton(Client\VideoGatewayClient::class, function () {

            $kollusVideoGatewayClient = new Client\VideoGatewayClient(
                config('kollus.domain'),
                config('kollus.api_version'),
                config('kollus.language')
            );

            $serviceAccount = new Container\ServiceAccount([
                'key' => config('kollus.service_account.key'),
                'custom_key' => config('kollus.service_account.custom_key'),
                'security_key' => config('kollus.service_account.security_key', null),
            ]);
            if (config('kollus.use_https')) {
                $kollusVideoGatewayClient->setSchema('https');
            }

            $kollusVideoGatewayClient->setServiceAccount($serviceAccount);
            $kollusVideoGatewayClient->connect();

            return $kollusVideoGatewayClient;
        });

        $this->app->singleton(Callback::class, function () {

            $serviceAccount = new Container\ServiceAccount([
                'key' => config('kollus.service_account.key'),
                'custom_key' => config('kollus.service_account.custom_key'),
                'security_key' => config('kollus.service_account.security_key'),
            ]);

            $kollusCallback = new Callback($serviceAccount);

            return $kollusCallback;
        });
    }
}
