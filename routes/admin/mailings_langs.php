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

use N1ebieski\ICore\Http\Controllers\Admin\MailingLang\MailingLangController;

Route::delete('mailings-langs/{mailingLang}', [MailingLangController::class, 'destroy'])
    ->name('mailing_lang.destroy')
    ->where('mailingLang', '[0-9]+')
    ->middleware('permission:admin.mailings.delete');
