<?php


namespace RestfulClient\Client;


use Couchbase\Exception;
use Illuminate\Http\UploadedFile;

class Request
{
    /**
     * @var array
     */
    protected $config;

    protected $options = [];

    protected $url = null;

    protected $route = null;

    protected $method = 'GET';

    public function __construct(string $route, RequestData $data = null, array $parameters = [])
    {
        $this->config = config('rest-client');
        $this->route = $this->validRoute($route);
        $this->buildRequest($this->config[$route], $data, $parameters);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    protected function buildRequest(array $route, RequestData $data = null, array $parameters = []): void
    {
        $options['headers'] = session()->has('token') ? ['authorization' => 'Bearer ' . session('token')] : [];
        $url = $this->config['url'] . $this->config['prefix'] . $route['url'];

        if ( ! empty($route['fields'])) {
            $query['fields'] = $route['fields'];
        }

        if ( ! empty($route['query'])) {
            foreach ($route['query'] as $key => $value) {
                $query[$key] = $value;
            }
        }

        if ( ! empty($route['parameters'])) {
            $routeParams = array_only(request()->route()->parameters(), $route['parameters']);
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

        $this->options = $options;
        $this->url = $url;
        $this->method = $route['method'];
    }

    protected function buildMultipartData(array $data): array
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

    protected function setRequestData(RequestData $requestData): array
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

    protected function buildMultipartDataFromArray(array $data, $name): array
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

    protected function validRoute(string $route)
    {
        if (empty($this->config[$route])) {
            throw new \Exception("Route {$route} is not found in the rest-client.php config file");
        }

        return $route;
    }
}