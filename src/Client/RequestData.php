<?php


namespace RestfulClient\Client;


class RequestData
{
    const PUT = 'put';
    const POST = 'post';
    const DELETE = 'delte';

    private $attributes = [];

    private $multipart = false;

    private $method = 'POST';

    public function __construct(array $data, $multipart = false, $method = 'POST')
    {
        unset($data['_token']);
        $this->attributes = $data;
        $this->multipart = $multipart;
        $this->method = $method;

        if (in_array($this->method, ['put', 'patch', 'delete'])) {
            $this->attributes['_method'] = $this->method;
        }
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