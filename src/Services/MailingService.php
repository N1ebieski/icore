<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;

class MailingService implements
    Creatable,
    Updatable,
    StatusUpdatable,
    Deletable,
    GlobalDeletable
{
    /**
     * [private description]
     * @var Mailing
     */
    protected $mailing;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Mailing $mailing
     * @param DB $db
     */
    public function __construct(Mailing $mailing, DB $db)
    {
        $this->mailing = $mailing;

        $this->db = $db;
    }

    /**
     * Store a newly created Mailing in storage.
     *
     * @param  array  $attributes [description]
     * @return Mailing             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailing->content_html = $attributes['content_html'];
            $this->mailing->content = $this->mailing->content_html;
            $this->mailing->title = $attributes['title'];
            $this->mailing->status = (int)$attributes['status'];

            if ($this->mailing->status === Mailing::SCHEDULED) {
                $this->mailing->activation_at =
                    $attributes['date_activation_at'] . $attributes['time_activation_at'];
            }

            $this->mailing->save();

            $this->mailing->emails()->make()
                ->setRelations(['mailing' => $this->mailing])
                ->makeService()
                ->createGlobal($attributes);

            return $this->mailing;
        });
    }

    /**
     * Update the specified Mailing in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->mailing->content_html = $attributes['content_html'];
            $this->mailing->content = $this->mailing->content_html;
            $this->mailing->title = $attributes['title'];
            $this->mailing->status = (int)$attributes['status'];

            if ($this->mailing->status === Mailing::SCHEDULED) {
                $this->mailing->activation_at =
                    $attributes['date_activation_at'] . $attributes['time_activation_at'];
            }

            if ($this->mailing->emails->count() === 0) {
                $this->mailing->emails()->make()
                    ->setRelations(['mailing' => $this->mailing])
                    ->makeService()
                    ->createGlobal($attributes);
            }

            return $this->mailing->save();
        });
    }

    /**
     * Update Status attribute the specified Mailing in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->mailing->update(['status' => $attributes['status']]);
        });
    }

    /**
     * Reset all Recipients of Mailing in storage.
     */
    public function reset(): void
    {
        $this->db->transaction(function () {
            $this->mailing->emails()->make()
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
