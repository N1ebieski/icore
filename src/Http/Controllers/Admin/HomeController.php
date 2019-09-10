<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use Illuminate\Http\Request;
use N1ebieski\ICore\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('icore::admin.home.index');
    }
}
