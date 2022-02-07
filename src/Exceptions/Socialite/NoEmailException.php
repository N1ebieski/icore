<?php

namespace N1ebieski\ICore\Exceptions\Socialite;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;

class NoEmailException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'Provider has not provided the user\'s email address';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
