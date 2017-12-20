<?php


namespace RestfulClient\Client;


use Exception;
use GuzzleHttp\ClientInterface;


/**
 * @method ClientInterface get(array $route, string $service = null, array $parameters = [])
 * @method ClientInterface post(array $routeName, string $service = null, RequestData $data = null)
 * @method ClientInterface put(array $routeName, string $service = null, RequestData $data = null)
 * @method ClientInterface delete(array $routeName, string $service = null, RequestData $data = null)
 */
class GuzzleRestfulClient implements RestfulClientInterface
{
    protected $methods = ['get', 'post', 'put', 'delete'];

    protected $service;

    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client)
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

        $service = is_string($arguments[1] ?? null) ? $arguments[1] : config('rest-client.default');

        foreach ($routes as $route) {
            $requests[] = new Request($route, $service, ...$arguments);
        }

        return $this->client->{$method}($requests);
    }
}