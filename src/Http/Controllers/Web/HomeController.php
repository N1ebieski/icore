<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Http\Request;

/**
 * [HomeController description]
 */
class HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('icore::web.home.index');
    }
}
