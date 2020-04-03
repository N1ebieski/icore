<?php

namespace N1ebieski\ICore\Http\Responses;

use Illuminate\Http\JsonResponse;

interface JsonResponseFactory
{
    public function makeResponse() : JsonResponse;
}
