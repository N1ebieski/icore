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

namespace N1ebieski\ICore\Services\MailingEmail;

use Throwable;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\MailingEmail\Sent;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;

class MailingEmailService
{
    /**
     * Undocumented function
     *
     * @param MailingEmail $mailingEmail
     * @param User $user
     * @param Newsletter $newsletter
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected MailingEmail $mailingEmail,
        protected User $user,
        protected Newsletter $newsletter,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function createGlobal(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            if (isset($attributes['users']) && (bool)$attributes['users'] === true) {
                $this->createUserRecipients([
                    'mailing' => $attributes['mailing']
                ]);
            }

            if (isset($attributes['newsletters']) && (bool)$attributes['newsletters'] === true) {
                $this->createNewsletterRecipients([
                    'mailing' => $attributes['mailing']
                ]);
            }

            if (isset($attributes['emails']) && (bool)$attributes['emails'] === true) {
                $this->createEmailRecipients([
                    'mailing' => $attributes['mailing'],
                    'recipients' => json_decode($attributes['emails_json'])
                ]);
            }
        });
    }

    /**
     *
     * @param array $attributes
     * @return void
     * @throws Throwable
     */
    public function createUserRecipients(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            $this->user->marketing()
                ->active()
                ->chunk(1000, function (Collection $users) use ($attributes) {
                    $id = $this->makeLastId() + 1;

                    $values = [];

                    $users->each(function (User $user) use ($attributes, &$values, &$id) {
                        // Create attributes manually, no within model because multiple
                        // models may be huge performance impact
                        $values[] = [
                            'id' => $id++,
                            'mailing_id' => $attributes['mailing'],
                            'model_type' => $user->getMorphClass(),
                            'model_id' => $user->id,
                            'email' => $user->email,
                            'sent' => Sent::UNSENT,
                            'created_at' => $this->carbon->now(),
                            'updated_at' => $this->carbon->now()
                        ];
                    });

                    $this->mailingEmail->newQuery()->insertOrIgnore($values);
                });
        });
    }

    /**
     *
     * @param array $attributes
     * @return void
     * @throws Throwable
     */
    public function createNewsletterRecipients(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            $this->newsletter->active()
                ->chunk(1000, function (Collection $newsletters) use ($attributes) {
                    $id = $this->makeLastId() + 1;

                    $values = [];

                    $newsletters->each(function (Newsletter $newsletter) use ($attributes, &$values, &$id) {
                        // Create attributes manually, no within model because multiple
                        // models may be huge performance impact
                        $values[] = [
                            'id' => $id++,
                            'mailing_id' => $attributes['mailing'],
                            'model_type' => $newsletter->getMorphClass(),
                            'model_id' => $newsletter->id,
                            'email' => $newsletter->email,
                            'sent' => Sent::UNSENT,
                            'created_at' => $this->carbon->now(),
                            'updated_at' => $this->carbon->now()
                        ];
                    });

                    $this->mailingEmail->newQuery()->insertOrIgnore($values);
                });
        });
    }

    /**
     *
     * @param array $attributes
     * @return void
     * @throws Throwable
     */
    public function createEmailRecipients(array $attributes): void
    {
        $this->db->transaction(function () use ($attributes) {
            $id = $this->makeLastId() + 1;

            $values = [];

            foreach ($attributes['recipients'] as $recipient) {
                // Create attributes manually, no within model because multiple
                // models may be huge performance impact
                $values[] = [
                    'id' => $id++,
                    'mailing_id' => $attributes['mailing'],
                    'model_type' => null,
                    'model_id' => null,
                    'email' => $recipient->email,
                    'sent' => Sent::UNSENT,
                    'created_at' => $this->carbon->now(),
                    'updated_at' => $this->carbon->now()
                ];
            }

            $this->mailingEmail->newQuery()->insertOrIgnore($values);
        });
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear(): int
    {
        return $this->db->transaction(function () {
            return $this->mailingEmail
                ->where('mailing_id', $this->mailingEmail->mailing->id)
                ->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function markAsSent(): bool
    {
        return $this->db->transaction(function () {
            return $this->mailingEmail->update(['sent' => Sent::SENT]);
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function markAsError(): bool
    {
        return $this->db->transaction(function () {
            return $this->mailingEmail->update(['sent' => Sent::ERROR]);
        });
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected function makeLastId(): int
    {
        return $this->mailingEmail->makeRepo()->firstByLatestId()?->id ?? 1;
    }
}
