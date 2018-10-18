<?php


namespace RestfulClient\Client\Exceptions;


use Exception;
use Illuminate\Http\Request;

abstract class Base extends Exception
{
    protected $messages = [];

    protected $response;

    protected $service = null;

    public function setService(string $service)
    {
        $this->service = $service;

        return $this;
    }

    public function getResponse($request = null)
    {
        return $this->defaultResponse($request ?? request(), $this->getCode());
    }

    protected function jsonResponse($code = 200)
    {
        $config = $this->getResponseConfig($code);

        return response()->json(['message' => $config['message'] ?? $this->getMessage()], $code);
    }

    protected function defaultResponse(Request $request, $code = 200)
    {
        if ($request->wantsJson()) {
            return $this->jsonResponse($code);
        }

        return $this->httpResponse($request, $code);
    }

    protected function httpResponse(Request $request, int $code)
    {
        $config = $this->getResponseConfig($code);

        return redirect()->route($config['route'] ?? 'home')->with([
            'status' => $config['status'] ?? 'danger',
            'message' => $config['message'] ?? $this->getMessage()
        ]);
    }

    protected function getResponseConfig(int $code): array
    {
        $serviceConfig = config("rest-client.{$this->service}.redirects");
        $generalConfig = config("rest-client.redirects");

        if ( ! empty($config = $serviceConfig[$code] ?? [])) {
            return $config;
        } elseif ( ! empty($config = $serviceConfig['default'] ?? [])) {
            return $config;
        } elseif ( ! empty($config = $generalConfig[$code] ?? [])) {
            return $config;
        } elseif ( ! empty($config = $generalConfig['default'] ?? [])) {
            return $config;
        }

        return [];
    }
}
