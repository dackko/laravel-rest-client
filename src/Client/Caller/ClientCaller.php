<?php


namespace RestfulClient\Caller\Client;


use Exception;
use GuzzleHttp\Client;
use RestfulClient\Client\Request;
use RestfulClient\Client\Response;

class ClientCaller implements CallerInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = app('rest.client');
    }

    public function get(array $requests)
    {
        return $this->call($requests);
    }

    public function post(array $requests)
    {
        return $this->call($requests);
    }

    public function put(array $requests)
    {
        return $this->call($requests);
    }

    public function delete(array $requests)
    {
        return $this->call($requests);
    }

    public function call(array $requests)
    {
        /** @var Request $request */
        foreach ($requests as $request) {
            if ( ! $request instanceof Request) {
                throw new Exception('Invalid request');
            }

            $promises[$request->getRoute()] = $this->client->{"{$request->getMethod()}"}($request->getUrl(),
                $request->getOptions());
        }

        return (new Response($promises ?? []))->respond();
    }
}