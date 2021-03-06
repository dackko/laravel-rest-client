<?php

namespace RestfulClient\Client\Exceptions;


use Illuminate\Http\JsonResponse;

class NotFoundException extends Base
{
    public function __construct()
    {
        parent::__construct('Entity not found.', JsonResponse::HTTP_NOT_FOUND);
    }
}