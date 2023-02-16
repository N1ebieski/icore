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

namespace N1ebieski\ICore\Services\MailingLang;

use Throwable;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\MailingLang\MailingLang;

class MailingLangService
{
    /**
     *
     * @param MailingLang $mailingLang
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(
        protected MailingLang $mailingLang,
        protected Config $config,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return MailingLang
     * @throws Throwable
     */
    public function createOrUpdate(array $attributes): MailingLang
    {
        return $this->db->transaction(function () use ($attributes) {
            if ($this->mailingLang->exists) {
                return $this->update($attributes);
            }

            return $this->create($attributes);
        });
    }

    /**
     *
     * @param array $attributes
     * @return MailingLang
     * @throws Throwable
     */
    public function create(array $attributes): MailingLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailingLang->fill($attributes);

            $this->mailingLang->content = $attributes['content_html'];

            $this->mailingLang->mailing()->associate($attributes['mailing']);

            $this->mailingLang->save();

            return $this->mailingLang;
        });
    }

    /**
     *
     * @param array $attributes
     * @return MailingLang
     * @throws Throwable
     */
    public function update(array $attributes): MailingLang
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailingLang->fill($attributes);

            if (array_key_exists('content_html', $attributes)) {
                $this->mailingLang->content = $attributes['content_html'];
            }

            $this->mailingLang->save();

            return $this->mailingLang;
        });
    }

    /**
     *
     * @return null|bool
     * @throws Throwable
     */
    public function delete(): ?bool
    {
        return $this->db->transaction(function () {
            return $this->mailingLang->delete();
        });
    }
}
