<?php


namespace App\Modules\Client;


interface RestClientInterface
{
    /**
     * @param mixed $routeName
     * @param array $parameters
     * @return mixed
     */
    public function get($routeName, array $parameters = []);

    /**
     * @param $routeName
     * @param RequestData|null $data
     * @return mixed
     */
    public function post($routeName, $data = null);

    /**
     * @param mixed $routeName
     * @param RequestData|null $data
     * @return mixed
     */
    public function put($routeName, $data = null);

    /**
     * @param mixed $routeName
     * @param RequestData|null $data
     * @return mixed
     */
    public function delete($routeName, $data = null);

    /**
     * @param $config
     * @param null $data
     * @param array $parameters
     * @return array
     */
    public function buildRequest($config, $data = null, array $parameters = []);

}