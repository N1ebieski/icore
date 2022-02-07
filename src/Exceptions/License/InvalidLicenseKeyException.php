<?php

namespace N1ebieski\ICore\Exceptions\License;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;

class InvalidLicenseKeyException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'The license key is invalid or empty';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
