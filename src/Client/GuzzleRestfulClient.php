<?php


namespace RestfulClient\Client;


use Exception;
use RestfulClient\Caller\Client\CallerInterface;


/**
 * @method CallerInterface get(array $route, string $service = null, array $parameters = [])
 * @method CallerInterface post(array $routeName, string $service = null, RequestData $data = null)
 * @method CallerInterface put(array $routeName, string $service = null, RequestData $data = null)
 * @method CallerInterface delete(array $routeName, string $service = null, RequestData $data = null)
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
            unset($arguments[1]);
            $service = $arguments[1];
        } else {
            $service = config('rest-client.default');
        }

        foreach ($routes as $route) {
            $requests[] = new Request($route, $service, ...$arguments);
        }

        return $this->client->{$method}($requests);
    }
}