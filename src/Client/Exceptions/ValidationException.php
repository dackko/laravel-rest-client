<?php

namespace RestfulClient\Client\Exceptions;


use Illuminate\Http\Request;

class ValidationException extends Base
{
    protected $messages = [];

    public function __construct(array $messages)
    {
        parent::__construct('The given data failed to pass validation.');
        $this->messages = $messages;
    }

    protected function httpResponse(Request $request)
    {
        return redirect()->back()->withInput($request->input())->withErrors($this->messages);
    }
}