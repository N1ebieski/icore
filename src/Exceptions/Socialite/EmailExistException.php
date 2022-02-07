<?php

namespace N1ebieski\ICore\Exceptions\Socialite;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;

class EmailExistException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'There is a registered account for the email address provided';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
