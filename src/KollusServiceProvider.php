<?php

namespace Kollus\Component;

use Illuminate\Support\ServiceProvider;

class KollusServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Kollus\Component\Client\ApiClient::class, function() {

            $kollusApiClient = new \Kollus\Component\Client\ApiClient(
                config('kollus.domain'),
                config('kollus.api_version'),
                config('kollus.language')
            );

            $serviceAccount = new \Kollus\Component\Container\ServiceAccount([
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

        $this->app->singleton(\Kollus\Component\Client\VideoGatewayClient::class, function() {

            $kollusVideoGatewayClient = new \Kollus\Component\Client\VideoGatewayClient(
                config('kollus.domain'),
                config('kollus.api_version'),
                config('kollus.language')
            );

            $serviceAccount = new \Kollus\Component\Container\ServiceAccount([
                'key' => config('kollus.service_account.key'),
                'custom_key' => config('kollus.service_account.custom_key')
            ]);
            if (config('kollus.use_https')) {
                $kollusVideoGatewayClient->setSchema('https');
            }

            $kollusVideoGatewayClient->setServiceAccount($serviceAccount);
            $kollusVideoGatewayClient->connect();

            return $kollusVideoGatewayClient;
        });
    }
}

