<?php

namespace N1ebieski\ICore\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CustomException extends Exception
{
    /**
     * Undocumented function
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            !empty($this->message) && empty($message) ? $this->message : $message,
            !empty($this->code) && empty($code) ? $this->code : $code,
            $previous
        );
    }

    /**
     * Report the exception.
     *
     * @return bool
     */
    public function report()
    {
        return false;
    }

    /**
     *
     * @param Request $request
     * @return bool
     */
    public function render(Request $request)
    {
        if (Config::get('app.debug') === true) {
            return false;
        }

        App::abort($this->getCode(), $this->getMessage());
    }
}
