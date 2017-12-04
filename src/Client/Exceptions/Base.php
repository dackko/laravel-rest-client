<?php


namespace App\Modules\Client\Exceptions;


use Exception;
use Illuminate\Http\Request;

abstract class Base extends Exception
{
    protected $messages = [];

    protected $response;

    public function getResponse($request = null)
    {
        return $this->defaultResponse($request ?? request());
    }

    protected function jsonResponse($code = 200)
    {
        return response()->json($this->messages, $code);
    }

    protected function defaultResponse(Request $request, $code = 200)
    {
        if ($request->expectsJson()) {
            return $this->jsonResponse($code);
        }

        return $this->httpResponse($request);
    }

    abstract protected function httpResponse(Request $request);
}
