<?php

namespace N1ebieski\ICore\Exceptions;

use Exception;
use Illuminate\Http\Request;

class CustomException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function render(Request $request)
    {
        return abort($this->getCode(), $this->getMessage());
    }
}
