<?php


namespace RestfulClient\Client;


class RequestData
{
    const PUT = 'put';
    const POST = 'post';
    const DELETE = 'delete';

    private $attributes = [];

    private $multipart = false;

    private $method = 'POST';

    private $options = [];

    public function __construct(array $data = [], $multipart = false, $method = 'POST', array $options = [])
    {
        unset($data['_token']);
        $this->attributes = $data;
        $this->multipart = $multipart;
        $this->method = $method;
        $this->options = $options;

        if (in_array($this->method, ['put', 'patch', 'delete'])) {
            $this->attributes['_method'] = $this->method;
        }
    }

    public function getHeaders(string $key = null)
    {
        $headers = $this->options['headers'] ?? [];

        if (empty($headers) or is_null($key)) {
            return $headers;
        }

        return $headers[$key] ?? [];
    }

    public function isMultipart()
    {
        return $this->multipart;
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function isEmpty()
    {
        return empty($this->attributes());
    }
}