<?php


namespace RestfulClient\Client;

use RestfulClient\Caller\Client\CallerInterface;


/**
 * @method CallerInterface get(array $route, string $service = null, array $parameters = [])
 * @method CallerInterface post(array $routeName, string $service = null, RequestData $data = null)
 * @method CallerInterface put(array $routeName, string $service = null, RequestData $data = null)
 * @method CallerInterface delete(array $routeName, string $service = null, RequestData $data = null)
 */
interface RestfulClientInterface
{
    /**
     * @param string $service
     * @return self
     */
    public function service(string $service);
}