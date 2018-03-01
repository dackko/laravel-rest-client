<?php


namespace RestfulClient\Caller\Client;


use RestfulClient\Client\Response;

interface CallerInterface
{
    /**
     * @return array
     */
    public function cookies();

    /**
     * @param array $requests
     * @return Response
     */
    public function get(array $requests);

    /**
     * @param array $requests
     * @return Response
     */
    public function post(array $requests);

    /**
     * @param array $requests
     * @return Response
     */
    public function put(array $requests);

    /**
     * @param array $requests
     * @return Response
     */
    public function delete(array $requests);
}