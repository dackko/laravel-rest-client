<?php


namespace RestfulClient\Client\Exceptions;


class EmptyResponse extends Base
{
    public function __construct()
    {
        parent::__construct('The backend returned empty response.');
    }
}