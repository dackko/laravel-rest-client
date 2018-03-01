<?php


namespace RestfulClient\Client;


use Exception;
use RestfulClient\Caller\Client\CallerInterface;


/**
 * @method CallerInterface get($route, string $service = null, RequestData $data = null)
 * @method CallerInterface post($route, string $service = null, RequestData $data = null)
 * @method CallerInterface put($route, string $service = null, RequestData $data = null)
 * @method CallerInterface delete($route, string $service = null, RequestData $data = null)
 */
class GuzzleRestfulClient implements RestfulClientInterface
{
    protected $methods = ['get', 'post', 'put', 'delete'];

    protected $service;

    /**
     * @var CallerInterface
     */
    protected $client;

    public function __construct(CallerInterface $client)
    {
        $this->service = config('rest-client.default');
        $this->client = $client;
    }

    public function service(string $service)
    {
        $this->service = $service;
    }

    public function __call($method, $arguments)
    {
        $requests = [];
        if ( ! in_array($method, $this->methods)) {
            throw new Exception("Method not found: {$method}");
        }

        $routes = $arguments[0];
        unset($arguments[0]);
        if ( ! is_array($routes)) {
            $routes = [$routes];
        }

        if (is_string($arguments[1] ?? null)) {
            $service = $arguments[1];
            unset($arguments[1]);
        } else {
            $service = config('rest-client.default');
        }

        foreach ($routes as $route) {
            $requests[] = new Request($route, $service, ...$arguments);
        }

        return $this->client->{$method}($requests);
    }

    public function buildRequest($route, string $service)
    {
        return new Request($route, $service);
    }

    public function cookies()
    {
        return $this->client->cookies();
    }
}