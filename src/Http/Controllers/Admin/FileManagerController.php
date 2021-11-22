<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class FileManagerController
{
    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function index(): HttpResponse
    {
        return Response::view('icore::admin.filemanager.index');
    }
}
