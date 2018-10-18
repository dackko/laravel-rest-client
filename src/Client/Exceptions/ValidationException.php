<?php

namespace RestfulClient\Client\Exceptions;


use Illuminate\Http\JsonResponse;

class ValidationException extends Base
{
    protected $messages = [];

    public function __construct(array $messages)
    {
        parent::__construct('The given data failed to pass validation.', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $this->messages = $messages;
    }
}