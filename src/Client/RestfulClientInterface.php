<?php


namespace RestfulClient\Client;

use RestfulClient\Caller\Client\CallerInterface;


/**
 * @method CallerInterface get($route, string $service = null, RequestData $data = null)
 * @method CallerInterface post($route, string $service = null, RequestData $data = null)
 * @method CallerInterface put($route, string $service = null, RequestData $data = null)
 * @method CallerInterface delete($route, string $service = null, RequestData $data = null)
 */
interface RestfulClientInterface
{
    /**
     * @param string $service
     * @return self
     */
    public function service(string $service);

    /**
     * @param array|string $route
     * @param string       $service
     * @return Request
     */
    public function buildRequest($route, string $service);
}