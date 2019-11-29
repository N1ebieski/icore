<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest;
use N1ebieski\ICore\Mails\ContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * [ContactController description]
 */
class ContactController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('icore::web.contact.index');
    }

    /**
     * [send description]
     * @param  SendRequest $request [description]
     * @param  ContactMail $mail    [description]
     * @return RedirectResponse     [description]
     */
    public function send(SendRequest $request, ContactMail $mail) : RedirectResponse
    {
        Mail::send(app()->make(ContactMail::class));

        return redirect()->route('web.contact.index')->with('success', trans('icore::contact.success.send'));
    }
}
