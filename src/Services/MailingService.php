<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Mailing;
use N1ebieski\ICore\Models\Newsletter;
use N1ebieski\ICore\Models\MailingEmail;
use N1ebieski\ICore\Models\User;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Support\Collection as Collect;

/**
 * [MailingService description]
 */
class MailingService implements Serviceable
{
    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

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
     * [private description]
     * @var MailingEmail
     */
    protected $mailingEmail;

    /**
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [protected description]
     * @var array|null
     */
    protected $models;

    /**
     * [__construct description]
     * @param Mailing      $mailing    [description]
     * @param MailingEmail $email      [description]
     * @param User         $user       [description]
     * @param Newsletter   $newsletter [description]
     * @param Collect      $collect
     */
    public function __construct(
        Mailing $mailing,
        MailingEmail $email,
        User $user,
        Newsletter $newsletter,
        Collect $collect
    )
    {
        $this->mailing = $mailing;
        $this->email = $email;
        $this->user = $user;
        $this->newsletter = $newsletter;
        $this->collect = $collect;
    }

    /**
     * Store a newly created Mailing in storage.
     *
     * @param  array  $attributes [description]
     * @return Mailing             [description]
     */
    public function create(array $attributes) : Model
    {
        // Dodanie modelu mailingu do bazy, potrzebne by uzyskać ID
        $this->mailing->content_html = $attributes['content_html'];
        $this->mailing->content = $this->mailing->content_html;
        $this->mailing->title = $attributes['title'];
        $this->mailing->status = (int)$attributes['status'];

        if ($this->mailing->status === 2) {
            $this->mailing->activation_at =
                $attributes['date_activation_at'].$attributes['time_activation_at'];
        }

        $this->mailing->save();

        // Dodanie grup odbiorców mailingu
        $this->createRecipients($attributes);

        return $this->mailing;
    }

    /**
     * Update the specified Mailing in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->mailing->content_html = $attributes['content_html'];
        $this->mailing->content = $this->mailing->content_html;
        $this->mailing->title = $attributes['title'];
        $this->mailing->status = (int)$attributes['status'];

        if ($this->mailing->status === 2) {
            $this->mailing->activation_at =
                $attributes['date_activation_at'].$attributes['time_activation_at'];
        }

        if ($this->mailing->emails->count() === 0) {
            $this->createRecipients($attributes);
        }

        return $this->mailing->save();
    }

    /**
     * Update Status attribute the specified Mailing in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        // if ($attributes['status'] == 1) {
        //     $this->sendMailing->dispatch($this->mailing);
        // }

        return $this->mailing->update(['status' => $attributes['status']]);
    }

    /**
     * Store Recipients for Mailing in storage.
     *
     * @param  array  $attributes [description]
     * @return Collect|null             [description]
     */
    public function createRecipients(array $attributes) : ?Collect
    {
        // Pobieramy poszczególne grupy odbiorców zaczynając od tych, które
        // mają powiązanie z modelem
        if (isset($attributes['users']) && (bool)$attributes['users'] === true) {
            $this->makeUserRecipients();
        }

        if (isset($attributes['newsletters']) && (bool)$attributes['newsletters'] === true) {
            $this->makeNewsletterRecipients();
        }

        if (isset($attributes['emails']) && (bool)$attributes['emails'] === true) {
            $this->makeEmailRecipients(json_decode($attributes['emails_json']));
        }

        if (isset($this->models)) {
            // Usuwamy duplikaty emailów, w pierwszej kolejności zostawiając te
            // powiązanie z modelem
            $models = $this->collect->make($this->models)->unique('email');
            return $this->mailing->emails()->saveMany($models);
        }

        return null;
    }

    /**
     * Reset all Recipients of Mailing in storage.
     */
    public function reset() : void
    {
        $this->deleteRecipients();
    }

    /**
     * Remove the Mailing's Recipients from storage.
     *
     * @return bool [description]
     */
    public function deleteRecipients() : bool
    {
        return $this->mailing->emails()->delete();
    }

    /**
     * Make array of UserRecipients.
     *
     * @return array|null [description]
     */
    public function makeUserRecipients() : ?array
    {
        $users = $this->user->all(['id', 'email']);

        foreach ($users as $user) {
            $this->models[] = $email = $this->email->make();
            $email->morph()->associate($user);
            $email->email = $user->email;
        }

        return $this->models ?? null;
    }

    /**
     * Make array of NewsletterRecipients.
     *
     * @return array|null [description]
     */
    public function makeNewsletterRecipients() : ?array
    {
        $subscribers = $this->newsletter->whereStatus(1)->get(['id', 'email']);

        foreach ($subscribers as $subscriber) {
            $this->models[] = $email = $this->email->make();
            $email->morph()->associate($subscriber);
            $email->email = $subscriber->email;
        }

        return $this->models ?? null;
    }

    /**
     * Make array of EmailRecipients.
     *
     * @param array $emails_json
     * @return array|null [description]
     */
    public function makeEmailRecipients(array $emails_json) : ?array
    {
        foreach ($emails_json as $email_json) {
            $this->models[] = $email = $this->email->make();
            $email->email = $email_json->email;
        }

        return $this->models ?? null;
    }

    /**
     * Remove the specified Mailing from storage.
     *
     * @return bool [description]
     */
    public function delete() : bool
    {
        return $this->mailing->delete();
    }

    /**
     * Remove the collection of Mailings from storage.
     *
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        return $this->mailing->whereIn('id', $ids)->delete();
    }
}
