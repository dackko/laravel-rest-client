<?php

namespace RestfulClient\Client;


use RestfulClient\Client\Exceptions\EmptyResponse;
use RestfulClient\Client\Exceptions\NotFoundException;
use RestfulClient\Client\Exceptions\UnauthorizedException;
use RestfulClient\Client\Exceptions\ValidationException;
use Closure;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\JsonResponse;
use function GuzzleHttp\Promise\settle;

class Response
{
    /**
     * @var Closure
     */
    private $promises;

    private $response = [];

    function __construct(array $promises)
    {
        $this->promises = $promises;
        $this->execute();
    }

    private function execute()
    {
        $responses = settle($this->promises)->wait(true);

        foreach ($responses as $route => $response) {
            if ($response['state'] !== PromiseInterface::FULFILLED) {
                $this->unwrapException($response['reason']);
                continue;
            }

            $this->response[$route] = $this->unwrapSuccess($response['value']);
        }
    }


    public function respond()
    {
        if (count($this->response) == 1) {
            return array_first($this->response);
        }

        return $this->response;
    }

    private function unwrapSuccess(GuzzleResponse $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param ServerException|ClientException $exception
     * @throws EmptyResponse
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    private function unwrapException($exception)
    {
        $response = $exception->getResponse();
        if ( ! $response) {
            throw new EmptyResponse;
        }
        $content = json_decode($response->getBody()->getContents(), true);

        switch ($exception->getCode()) {
            case JsonResponse::HTTP_UNPROCESSABLE_ENTITY:
                throw new ValidationException($content['validator']);
                break;

            case JsonResponse::HTTP_NOT_FOUND:
                throw new NotFoundException;
                break;

            case JsonResponse::HTTP_UNAUTHORIZED:
                throw new UnauthorizedException;
                break;
        }

        # Debug
        throw $exception;
    }
}