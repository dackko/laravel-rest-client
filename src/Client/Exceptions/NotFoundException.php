<?php

namespace RestfulClient\Client\Exceptions;


use Illuminate\Http\Request;

class NotFoundException extends Base
{
    public function __construct()
    {
        parent::__construct('Entity not found.');
    }

    protected function httpResponse(Request $request)
    {
        return redirect()->back()->with(['status' => 'danger', 'message' => $this->getMessage()]);
    }
}