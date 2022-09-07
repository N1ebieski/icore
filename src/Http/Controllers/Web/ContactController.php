<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Mail\Contact\Mail as ContactMail;
use N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest;

class ContactController
{
    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function show(): HttpResponse
    {
        return Response::view('icore::web.contact.show');
    }

    /**
     * [send description]
     * @param  SendRequest $request [description]
     * @return RedirectResponse     [description]
     */
    public function send(SendRequest $request): RedirectResponse
    {
        Mail::send(App::make(ContactMail::class));

        return Response::redirectToRoute('web.contact.show')->with(
            'success',
            Lang::get('icore::contact.success.send')
        );
    }
}
