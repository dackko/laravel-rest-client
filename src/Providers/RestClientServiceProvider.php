<?php


namespace RestfulClient\Providers;


use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use RestfulClient\Client\RestClientImpl;
use RestfulClient\Client\RestClientInterface;

class RestClientServiceProvider extends ServiceProvider
{
    static $configKey = 'backend';

    static $configFile = 'rest-client';

    public function register()
    {
        $this->app->bind(RestClientInterface::class, RestClientImpl::class);

        $this->app->bind('rest.client', function () {
            $config = [
                'headers' => [
                    'accept' => 'application/json',
                ]
            ];

            return new Client($config);
        });
    }
}