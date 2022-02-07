<?php

namespace N1ebieski\ICore\Exceptions\License;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;

class NotFoundException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'License not found';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
