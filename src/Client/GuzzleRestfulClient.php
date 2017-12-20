<?php


namespace RestfulClient\Client;


use Exception;

/**
 * @method  get(array $route, array $parameters = [])
 * @method  post(array $routeName, RequestData $data = null)
 * @method  put(array $routeName, RequestData $data = null)
 * @method  delete(array $routeName, RequestData $data = null)
 */
class GuzzleRestfulClient
{
    protected $methods = ['get', 'post', 'put', 'delete'];

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

        foreach ($routes as $route) {
            $requests[] = new Request($route, ...$arguments);
        }

        return $this->{$method}($requests);
    }
}