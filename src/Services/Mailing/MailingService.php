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

namespace N1ebieski\ICore\Services\Mailing;

use Throwable;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

class MailingService
{
    /**
     * Undocumented function
     *
     * @param Mailing $mailing
     * @param DB $db
     */
    public function __construct(
        protected Mailing $mailing,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Mailing
     * @throws Throwable
     */
    public function create(array $attributes): Mailing
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailing->content_html = $attributes['content_html'];
            $this->mailing->content = $this->mailing->content_html;
            $this->mailing->title = $attributes['title'];
            $this->mailing->status = $attributes['status'];

            if ($this->mailing->status->isScheduled()) {
                $this->mailing->activation_at =
                    $attributes['date_activation_at'] . $attributes['time_activation_at'];
            }

            $this->mailing->save();

            $attributes['mailing'] = $this->mailing->id;

            /** @var MailingEmail */
            $mailingEmail = $this->mailing->emails()->make();

            $mailingEmail->makeService()->createGlobal($attributes);

            return $this->mailing;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Mailing
     * @throws Throwable
     */
    public function update(array $attributes): Mailing
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailing->content_html = $attributes['content_html'];
            $this->mailing->content = $this->mailing->content_html;
            $this->mailing->title = $attributes['title'];
            $this->mailing->status = $attributes['status'];

            if ($this->mailing->status->isScheduled()) {
                $this->mailing->activation_at =
                    $attributes['date_activation_at'] . $attributes['time_activation_at'];
            }

            if ($this->mailing->emails->count() === 0) {
                $attributes['mailing'] = $this->mailing->id;

                /** @var MailingEmail */
                $mailingEmail = $this->mailing->emails()->make();
    
                $mailingEmail->makeService()->createGlobal($attributes);
            }

            $this->mailing->save();

            return $this->mailing;
        });
    }

    /**
     *
     * @param int $status
     * @return bool
     * @throws Throwable
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            return $this->mailing->update(['status' => $status]);
        });
    }

    /**
     *
     * @return int
     * @throws Throwable
     */
    public function reset(): int
    {
        return $this->db->transaction(function () {
            return $this->mailing->emails()->make()
                ->setRelations(['mailing' => $this->mailing])
                ->makeService()
                ->clear();
        });
    }

    /**
     * Remove the specified Mailing from storage.
     *
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            return $this->mailing->delete();
        });
    }

    /**
     * Remove the collection of Mailings from storage.
     *
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            return $this->mailing->whereIn('id', $ids)->delete();
        });
    }
}
