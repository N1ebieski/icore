<?php

namespace N1ebieski\ICore\Exceptions\File;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Exceptions\CustomException;

class NotFoundException extends CustomException
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $message = 'File not found';

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $code = HttpResponse::HTTP_FORBIDDEN;
}
