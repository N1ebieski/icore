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

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing\Traits;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

trait HasEmailsJson
{
    /**
     *
     * @return void
     * @throws BadRequestException
     */
    protected function prepareEmailsJsonAttribute(): void
    {
        if ($this->has('emails_json') && preg_match('/^\[[\s\S]*\]$/', $this->get('emails_json')) === 0) {
            $emails = explode("\r\n", $this->get('emails_json'));

            $emailsToJson = [];

            foreach ($emails as $email) {
                if (empty($email)) {
                    continue;
                }

                $emailsToJson[] = [
                    'email' => $email
                ];
            }

            if (count($emailsToJson) > 0) {
                $this->merge([
                    'emails_json' => json_encode($emailsToJson)
                ]);
            }
        }
    }
}
