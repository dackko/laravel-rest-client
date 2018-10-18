<?php


namespace RestfulClient\Client\Exceptions;


use Illuminate\Http\JsonResponse;

class ForbiddenAccessException extends Base
{
    public function __construct()
    {
        parent::__construct('This action is forbidden.', JsonResponse::HTTP_FORBIDDEN);
    }
}