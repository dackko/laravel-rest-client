<?php


namespace RestfulClient\Client;


/**
 * @method get(array $route, array $parameters = [])
 * @method post(array $routeName, RequestData $data = null)
 * @method put(array $routeName, RequestData $data = null)
 * @method delete(array $routeName, RequestData $data = null)
 */
interface RestfulClientInterface
{
    /**
     * @param string $service
     * @return self
     */
    public function service(string $service);
}