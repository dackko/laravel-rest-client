<?php

namespace RestfulClient\Client;


use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use App\Modules\Client\RestClientServiceProvider as Provider;

class RestClientImpl implements RestClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    protected $endpoints;

    protected $config;

    public function __construct()
    {
        Provider::$configKey = 'test';
        $this->client = app('rest.client');
        $this->config = config(Provider::$configFile . '.' . Provider::$configKey);
        $this->endpoints = $this->config['endpoints'];
    }

    public function get($routeName, array $parameters = [])
    {
        if ( ! is_array($routeName)) {
            $routeName = [$routeName];
        }

        foreach ($routeName as $routeUrl) {
            [$options, $url] = $this->buildRequest($this->endpoints[$routeUrl], null, $parameters);
            $promises[$routeUrl] = $this->client->getAsync($url, $options);
        }

        return (new Response($promises ?? []))->respond();
    }

    public function post($routeName, $data = null)
    {
        [$options, $url] = $this->buildRequest($this->endpoints[$routeName], $data);
        $promises[$routeName] = $this->client->postAsync($url, $options);

        return (new Response($promises))->respond();
    }

    public function put($routeName, $data = null)
    {
        [$options, $url] = $this->buildRequest($this->endpoints[$routeName], $data);
        $promises[$routeName] = $this->client->putAsync($url, $options);

        return (new Response($promises))->respond();
    }

    public function delete($routeName, $data = null)
    {
        [$options, $url] = $this->buildRequest($this->endpoints[$routeName]);
        $promises[$routeName] = $this->client->deleteAsync($url, $options);

        return (new Response($promises))->respond();
    }

    public function buildRequest($config, $data = null, array $parameters = [])
    {
        $options['headers'] = session()->has('token') ? ['authorization' => 'Bearer ' . session('token')] : [];
        $url = $this->config['url'] . $this->config['prefix'] . $config['url'];

        if ( ! empty($config['fields'])) {
            $query['fields'] = $config['fields'];
        }

        if ( ! empty($config['query'])) {
            foreach ($config['query'] as $key => $value) {
                $query[$key] = $value;
            }
        }

        if ( ! empty($config['parameters'])) {
            $routeParams = array_only(request()->route()->parameters(), $config['parameters']);
            # Overwrite any route parameters if array is specified, and they exists in both places
            $parameters = array_merge($routeParams, $parameters);
            $search = preg_filter('/^/', ':', array_keys($parameters));
            $url = str_replace($search, $parameters, $url);
        }

        if ($data instanceof RequestData) {
            [$key, $value] = $this->setRequestData($data);
            $options[$key] = $value;
        }

        $options['query'] = $query ?? [];

        return [$options, $url];
    }

    private function buildMultipartData(array $data)
    {
        $options = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $options = array_merge($options, $this->buildMultipartDataFromArray($value, $key));
            } else {
                $option = ['name' => $key, 'contents' => $value];
                if ($value instanceof UploadedFile) {
                    $option['filename'] = $value->getClientOriginalName();
                    $option['contents'] = fopen($value->getPathname(), 'r');
                    $option['Mime-Type'] = $value->getClientMimeType();
                }
                $options[] = $option;
            }
        }

        return $options;
    }

    private function setRequestData(RequestData $requestData)
    {
        if ($requestData->isEmpty()) {
            # Avoid possible break
            return ['json', []];
        }

        if ($requestData->isMultipart()) {
            return ['multipart', $this->buildMultipartData($requestData->attributes())];
        }

        return ['json', $requestData->attributes()];
    }

    private function buildMultipartDataFromArray($data, $name)
    {
        foreach ($data as $key => $value) {
            $option = ["name" => "{$name}[{$key}]", 'contents' => $value];
            if ($value instanceof UploadedFile) {
                $option['filename'] = $value->getClientOriginalName();
                $option['contents'] = fopen($value->getPathname(), 'r');
                $option['Mime-Type'] = $value->getClientMimeType();
            }
            $options[] = $option;
        }

        return $options ?? [];
    }
}