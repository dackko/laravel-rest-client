<?php

namespace App\Modules\Client\Exceptions;


use Exception;
use Illuminate\Http\Request;

class UnauthorizedException extends Base
{
    public function __construct()
    {
        parent::__construct('User not authorized.');
    }

    protected function httpResponse(Request $request)
    {
        return redirect()->route('home')->with(['status' => 'danger', 'message' => $this->getMessage()]);
    }

}