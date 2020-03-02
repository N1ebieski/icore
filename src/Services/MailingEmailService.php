<?php

namespace N1ebieski\ICore\Services;

use Carbon\Carbon;
use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\MailingEmail;
use N1ebieski\ICore\Models\Newsletter;

/**
 * [MailingEmailService description]
 */
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
     * [__construct description]
     * @param MailingEmail      $mailingEmail    [description]
     * @param User              $user       [description]
     * @param Newsletter        $newsletter [description]
     */
    public function __construct(
        MailingEmail $mailingEmail,
        User $user,
        Newsletter $newsletter
    ) {
        $this->mailingEmail = $mailingEmail;
        $this->user = $user;
        $this->newsletter = $newsletter;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function createGlobal(array $attributes) : void
    {
        // Pobieramy poszczególne grupy odbiorców zaczynając od tych, które
        // mają powiązanie z modelem
        if (isset($attributes['users']) && (bool)$attributes['users'] === true) {
            $this->createUserRecipients();
        }

        if (isset($attributes['newsletters']) && (bool)$attributes['newsletters'] === true) {
            $this->createNewsletterRecipients();
        }

        if (isset($attributes['emails']) && (bool)$attributes['emails'] === true) {
            $this->createEmailRecipients(json_decode($attributes['emails_json']));
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function createUserRecipients() : void
    {
        $attributes = [];

        $this->user->chunk(1000, function ($items) use ($attributes) {
            $items->each(function ($item) use ($attributes) {
                // Create attributes manually, no within model because multiple
                // models may be huge performance impact
                $attributes[] = [
                    'mailing_id' => $this->mailingEmail->getMailing()->id,
                    'model_type' => get_class($item),
                    'model_id' => $item->id,
                    'email' => $item->email,
                    'sent' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

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
        $attributes = [];

        $this->newsletter->active()
            ->chunk(1000, function ($items) use ($attributes) {
                $items->each(function ($item) use ($attributes) {
                    // Create attributes manually, no within model because multiple
                    // models may be huge performance impact
                    $attributes[] = [
                        'mailing_id' => $this->mailingEmail->getMailing()->id,
                        'model_type' => get_class($item),
                        'model_id' => $item->id,
                        'email' => $item->email,
                        'sent' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

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
        foreach ($items as $item) {
            // Create attributes manually, no within model because multiple
            // models may be huge performance impact
            $attributes[] = [
                'mailing_id' => $this->mailingEmail->getMailing()->id,
                'model_type' => null,
                'model_id' => null,
                'email' => $item->email,
                'sent' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->mailingEmail->insertIgnore($attributes);
        }
    }

    /**
     * [clear description]
     * @return int [description]
     */
    public function clear() : int
    {
        return $this->mailingEmail
            ->where('mailing_id', $this->mailingEmail->getMailing()->id)
            ->delete();
    }
}
