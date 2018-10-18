<?php

namespace RestfulClient\Client;


use Closure;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\JsonResponse;
use RestfulClient\Client\Exceptions\EmptyResponse;
use RestfulClient\Client\Exceptions\ForbiddenAccessException;
use RestfulClient\Client\Exceptions\NotFoundException;
use RestfulClient\Client\Exceptions\UnauthorizedException;
use RestfulClient\Client\Exceptions\ValidationException;
use function GuzzleHttp\Promise\settle;

class Response
{
    /**
     * @var Closure
     */
    private $promises;

    private $response = [];

    private $cookies = [];

    function __construct(array $promises)
    {
        $this->promises = $promises;
        $this->execute();
    }

    private function execute()
    {
        foreach ($this->promises as $route => $array) {
            $promises[$route] = $array['promise'];
        }
        $responses = settle($promises ?? [])->wait(true);

        foreach ($responses as $route => $response) {
            if ($response['state'] !== PromiseInterface::FULFILLED) {
                $this->unwrapException($response['reason'], $this->promises[$route]['service']);
                continue;
            }

            $this->response[$route] = $this->unwrapSuccess($response['value'], $route);
        }
    }

    public function respond()
    {
        if (count($this->response) == 1) {
            return array_first($this->response);
        }

        return $this->response;
    }

    public function hasCookies()
    {
        return ! empty($this->cookies);
    }

    public function cookies()
    {
        return $this->cookies;
    }

    private function unwrapSuccess(GuzzleResponse $response, $route)
    {
        if ( ! empty($cookies = $response->getHeader('Set-Cookie'))) {
            foreach ($cookies as $cookie) {
                $this->cookies[$route][] = $this->setBrowserCookie($cookie);
            }
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function setBrowserCookie(string $cookie)
    {
        $chunks = explode('; ', $cookie);
        $nameValue = explode('=', $chunks[0] ?? '');
        $expires = explode('=', $chunks[1] ?? '')[1] ?? null;
        $age = explode('=', $chunks[2] ?? '')[1] ?? null;
        $path = explode('=', $chunks[3] ?? '')[1] ?? '/';
        $domain = explode('=', $chunks[4] ?? '')[1] ?? null;
        $http = explode('=', $chunks[5] ?? '')[1] ?? false;

        return [
            'name' => $nameValue[0] ?? null,
            'value' => $nameValue[1] ?? null,
            'expires' => 60 * 120,
            'path' => $path,
            'domain' => $domain,
            'secure' => false,
            'httpOnly' => $http
        ];
    }

    /**
     * @param ServerException|ClientException $exception
     * @param string                          $service
     * @throws EmptyResponse
     * @throws ForbiddenAccessException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    private function unwrapException($exception, string $service)
    {
        $response = $exception->getResponse();
        if ( ! $response) {
            throw new EmptyResponse;
        }
        $content = json_decode($response->getBody()->getContents(), true);

        switch ($exception->getCode()) {
            case JsonResponse::HTTP_UNPROCESSABLE_ENTITY:
                throw (new ValidationException($content['validator']))->setService($service);
                break;

            case JsonResponse::HTTP_NOT_FOUND:
                throw (new NotFoundException)->setService($service);
                break;

            case JsonResponse::HTTP_UNAUTHORIZED:
                throw (new UnauthorizedException)->setService($service);
                break;

            case JsonResponse::HTTP_FORBIDDEN:
                throw (new ForbiddenAccessException)->setService($service);
                break;
        }

        # Debug
        throw $exception;
    }
}