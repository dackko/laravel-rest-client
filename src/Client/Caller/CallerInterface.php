<?php


namespace RestfulClient\Caller\Client;


use Exception;
use RestfulClient\Client\Response;

interface CallerInterface
{
    /**
     * @param string $service
     * @return array
     */
    public function cookies(string $service);

    /**
     * @param array $requests
     * @throws Exception
     * @return Response
     */
    public function get(array $requests);

    /**
     * @param array $requests
     * @throws Exception
     * @return Response
     */
    public function post(array $requests);

    /**
     * @param array $requests
     * @throws Exception
     * @return Response
     */
    public function put(array $requests);

    /**
     * @param array $requests
     * @throws Exception
     * @return Response
     */
    public function delete(array $requests);
}