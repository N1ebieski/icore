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
use RuntimeException;
use Illuminate\Support\Carbon;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use Illuminate\Database\Eloquent\MassAssignmentException;

class MailingService
{
    /**
     *
     * @param Mailing $mailing
     * @param DB $db
     * @param Carbon $carbon
     * @return void
     */
    public function __construct(
        protected Mailing $mailing,
        protected DB $db,
        protected Carbon $carbon
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
                // @phpstan-ignore-next-line
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
                // @phpstan-ignore-next-line
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

    /**
     *
     * @return int
     */
    public function activateScheduled(): int
    {
        return $this->db->transaction(function () {
            return $this->mailing->newQuery()
                ->whereDate('activation_at', '<', $this->carbon->now()->format('Y-m-d'))
                ->orWhere(function (Builder $query) {
                    return $query->whereDate('activation_at', '=', $this->carbon->now()->format('Y-m-d'))
                        ->whereTime('activation_at', '<=', $this->carbon->now()->format('H:i:s'));
                })
                ->scheduled()
                ->update(['status' => Status::ACTIVE]);
        });
    }

    /**
     *
     * @return int
     * @throws RuntimeException
     * @throws MassAssignmentException
     */
    public function deactivateCompleted(): int
    {
        return $this->db->transaction(function () {
            return $this->mailing->newQuery()
                ->progress()
                ->whereDoesntHave('emails', function ($query) {
                    return $query->unsent();
                })
                ->update([
                    'status' => Status::INACTIVE,
                    'activation_at' => null
                ]);
        });
    }

    /**
     *
     * @return int
     * @throws RuntimeException
     * @throws MassAssignmentException
     */
    public function progressActivated(): int
    {
        return $this->db->transaction(function () {
            return $this->mailing->newQuery()
                ->active()
                ->whereHas('emails', function ($query) {
                    return $query->unsent();
                })
                ->update(['status' => Status::INPROGRESS]);
        });
    }
}
