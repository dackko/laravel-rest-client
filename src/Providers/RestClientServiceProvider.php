<?php


namespace RestfulClient\Providers;


use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use RestfulClient\Client\Config\ClientConfiguration;
use RestfulClient\Client\Config\ClientConfigurationInterface;
use RestfulClient\Client\GuzzleRestfulClient;
use RestfulClient\Client\RestfulClientInterface;

class RestClientServiceProvider extends ServiceProvider
{
    public static $configKey = 'backend';

    public static $configFile = 'rest-client';

    public function boot()
    {
        $this->publishes(
            [__DIR__ . '/../../config/rest-client.php' => config_path('rest-client.php')],
            'rest-client'
        );
    }

    public function register()
    {
        $this->app->bind(RestfulClientInterface::class, GuzzleRestfulClient::class);

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