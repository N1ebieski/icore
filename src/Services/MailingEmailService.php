<?php

namespace N1ebieski\ICore\Services;

use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Models\MailingEmail;
use Illuminate\Database\DatabaseManager as DB;
class MailingEmailService
{
    /**
     * [private description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * [private description]
     * @var User
     */
    protected $user;

    /**
     * [private description]
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

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
        MailingEmail $mailingEmail,
        User $user,
        Newsletter $newsletter,
        Carbon $carbon,
        DB $db
    ) {
        $this->mailingEmail = $mailingEmail;
        $this->user = $user;
        $this->newsletter = $newsletter;

        $this->carbon = $carbon;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function createGlobal(array $attributes) : void
    {
        $this->db->transaction(function () use ($attributes) {
            if (isset($attributes['users']) && (bool)$attributes['users'] === true) {
                $this->createUserRecipients();
            }

            if (isset($attributes['newsletters']) && (bool)$attributes['newsletters'] === true) {
                $this->createNewsletterRecipients();
            }

            if (isset($attributes['emails']) && (bool)$attributes['emails'] === true) {
                $this->createEmailRecipients(json_decode($attributes['emails_json']));
            }
        });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function createUserRecipients() : void
    {
        $this->db->transaction(function () {
            $this->user->marketing()
                ->active()
                ->chunk(1000, function ($items) {
                    $id = $this->makeLastId();

                    $attributes = [];

                    $items->each(function ($item) use (&$attributes, &$id) {
                        // Create attributes manually, no within model because multiple
                        // models may be huge performance impact
                        $attributes[] = [
                            'id' => $id++,
                            'mailing_id' => $this->mailingEmail->mailing->id,
                            'model_type' => get_class($item),
                            'model_id' => $item->id,
                            'email' => $item->email,
                            'sent' => MailingEmail::UNSENT,
                            'created_at' => $this->carbon->now(),
                            'updated_at' => $this->carbon->now()
                        ];
                    });

                    $this->mailingEmail->insertIgnore($attributes);
                });
        });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function createNewsletterRecipients() : void
    {
        $this->db->transaction(function () {
            $this->newsletter->active()
                ->chunk(1000, function ($items) {
                    $id = $this->makeLastId();

                    $attributes = [];

                    $items->each(function ($item) use (&$attributes, &$id) {
                        // Create attributes manually, no within model because multiple
                        // models may be huge performance impact
                        $attributes[] = [
                            'id' => $id++,
                            'mailing_id' => $this->mailingEmail->mailing->id,
                            'model_type' => get_class($item),
                            'model_id' => $item->id,
                            'email' => $item->email,
                            'sent' => MailingEmail::UNSENT,
                            'created_at' => $this->carbon->now(),
                            'updated_at' => $this->carbon->now()
                        ];
                    });

                    $this->mailingEmail->insertIgnore($attributes);
                });
        });
    }

    /**
     * Undocumented function
     *
     * @param array $emails_json
     * @return void
     */
    public function createEmailRecipients(array $items) : void
    {
        $this->db->transaction(function () use ($items) {
            $id = $this->makeLastId();

            foreach ($items as $item) {
                // Create attributes manually, no within model because multiple
                // models may be huge performance impact
                $attributes[] = [
                    'id' => $id++,
                    'mailing_id' => $this->mailingEmail->mailing->id,
                    'model_type' => null,
                    'model_id' => null,
                    'email' => $item->email,
                    'sent' => MailingEmail::UNSENT,
                    'created_at' => $this->carbon->now(),
                    'updated_at' => $this->carbon->now()
                ];

                $this->mailingEmail->insertIgnore($attributes);
            }
        });
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
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
     * @return integer
     */
    protected function makeLastId() : int
    {
        return optional($this->mailingEmail->makeRepo()->firstByLatestId())->id ?? 1;
    }
}
