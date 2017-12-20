<?php


namespace RestfulClient\Client;

use GuzzleHttp\ClientInterface;


/**
 * @method ClientInterface get(array $route, string $service = null, array $parameters = [])
 * @method ClientInterface post(array $routeName, string $service = null, RequestData $data = null)
 * @method ClientInterface put(array $routeName, string $service = null, RequestData $data = null)
 * @method ClientInterface delete(array $routeName, string $service = null, RequestData $data = null)
 */
interface RestfulClientInterface
{
    /**
     * @param string $service
     * @return self
     */
    public function service(string $service);
}