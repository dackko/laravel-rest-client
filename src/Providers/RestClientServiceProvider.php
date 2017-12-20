<?php


namespace RestfulClient\Providers;


use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use RestfulClient\Caller\Client\CallerInterface;
use RestfulClient\Caller\Client\ClientCaller;
use RestfulClient\Client\GuzzleRestfulClient;
use RestfulClient\Client\RestfulClientInterface;

class RestClientServiceProvider extends ServiceProvider
{
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
        $this->app->bind(CallerInterface::class, ClientCaller::class);

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