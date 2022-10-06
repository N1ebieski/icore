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

namespace N1ebieski\ICore\View\Composers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use N1ebieski\ICore\View\Composers\Composer;

class ValidComposer extends Composer
{
    /**
     * @param Session $session
     * @param Request $request
     */
    public function __construct(
        protected Session $session,
        protected Request $request
    ) {
        //
    }


    /**
     * [isValid description]
     * @param  string  $name [description]
     * @return string|null       [description]
     */
    public function isValid(string $name): ?string
    {
        if ($this->session->has('errors')) {
            if ($this->session->get('errors')->has($name)) {
                return 'is-invalid';
            } else {
                if (array_key_exists($name, $this->request->session()->getOldInput())) {
                    return 'is-valid';
                }
            }
        }

        return null;
    }
}
