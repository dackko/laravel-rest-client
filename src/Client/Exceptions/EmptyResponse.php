<?php


namespace App\Modules\Client\Exceptions;


use Illuminate\Http\Request;

class EmptyResponse extends Base
{
    public function __construct()
    {
        parent::__construct('The backend returned empty response.');
    }

    protected function httpResponse(Request $request)
    {
        return redirect()->back()->with(['status' => 'danger', 'message' => $this->getMessage()]);
    }
}