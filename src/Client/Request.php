<?php


namespace RestfulClient\Client;


use Exception;
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

    protected $service = null;

    public function __construct(string $route, string $service, RequestData $data = null, array $parameters = [])
    {
        [$this->route, $service] = $this->validate($route, $service);
        $this->service = $service;
        $this->config = config("rest-client.{$service}");
        $this->buildRequest($this->config['endpoints'][$route], $data, $parameters);
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getHeaders(string $key = null)
    {
        $headers = $this->getOptions('headers');

        if ($key) {
            return $headers[$key] ?? null;
        }

        return $headers;
    }

    public function getOptions(string $key = null)
    {
        if ($key) {
            return $this->options[$key];
        }

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

    protected function buildRequest(array $route, RequestData $data = null, array $parameters): void
    {
        $options['headers'] = [];
        if ($this->config['has-auth']) {
            $options['headers']['authorization'] = $this->addAuthorizationHeader();
        }

        $url = "{$this->config['url']}/{$this->config['prefix']}/{$route['url']}";

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
            $options['headers'] = array_merge($options['headers'], $data->getHeaders() ?? []);
            [$key, $value] = $this->setRequestData($data);
            $options[$key] = $value;
        }

        $options['query'] = $query ?? [];
        if (strtolower($route['method']) === 'get') {
            $options['query'] = array_merge($options['query'],$options['json'] ?? []);
            $options['json'] = [];
        }

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
                    $option = array_merge($option, $this->multipartContentFromFile($value));
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
        $options = [];

        foreach ($data as $key => $value) {
            $optionName = "{$name}[{$key}]";
            if (is_array($value)) {
                $options = array_merge($options, $this->buildMultipartDataFromArray($value, $optionName));
                continue;
            }

            $option['name'] = $optionName;
            $option['contents'] = $value;

            if ($value instanceof UploadedFile) {
                $option = array_merge($option, $this->multipartContentFromFile($value));
            }

            array_push($options, $option);
        }

        return $options ?? [];
    }

    protected function multipartContentFromFile(UploadedFile $file)
    {
        $option['filename'] = $file->getClientOriginalName();
        $option['contents'] = fopen($file->getPathname(), 'r');
        $option['Mime-Type'] = $file->getClientMimeType();

        return $option;
    }

    /**
     * @param string $route
     * @param string $service
     * @return array
     * @throws Exception
     */
    protected function validate(string $route, string $service): array
    {
        $config = config('rest-client');
        if (empty($config[$service])) {
            throw new Exception("Service `{$service}` is not found in the `rest-client.php` config file");
        }

        if (empty($config[$service]['endpoints'][$route])) {
            throw new Exception("Route `{$route}` is not found under `{$service}` service in the `rest-client.php` config file");
        }

        if (empty($config[$service]['endpoints'][$route]['url']) or empty($config[$service]['endpoints'][$route]['method'])) {
            throw new Exception("The route `{$route}` under `{$service}` service in the rest-client.php doesn't have `method` or `url` set");
        }

        return [$route, $service];
    }

    private function addAuthorizationHeader(): string
    {
        $value = request()->header('authorization', '');
        if ( ! empty($value)) {
            return $value;
        }

        return $this->addAuthorizationHeaderFromLocal();
    }

    private function addAuthorizationHeaderFromLocal(): string
    {
        $method = $this->config['method'];
        if (($value = $method($this->config['auth-key'])) !== null) {
            return "Bearer {$value}";
        }

        return '';
    }
}