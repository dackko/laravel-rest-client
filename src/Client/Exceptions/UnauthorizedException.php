<?php

namespace RestfulClient\Client\Exceptions;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnauthorizedException extends Base
{
    public function __construct()
    {
        parent::__construct('User is not authorized.', JsonResponse::HTTP_UNAUTHORIZED);
    }
}