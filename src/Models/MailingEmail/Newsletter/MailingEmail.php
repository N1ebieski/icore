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

namespace N1ebieski\ICore\Models\MailingEmail\Newsletter;

use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail as BaseMailingEmail;

/**
 * N1ebieski\ICore\Models\MailingEmail\Newsletter\MailingEmail
 *
 * @property int $id
 * @property int $mailing_id
 * @property string $model_type
 * @property int|null $model_id
 * @property string $email
 * @property \N1ebieski\ICore\ValueObjects\MailingEmail\Sent $sent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $poli
 * @property-read \N1ebieski\ICore\Models\Mailing $mailing
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $morph
 * @method static \N1ebieski\ICore\Database\Factories\MailingEmail\MailingEmailFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail query()
 * @method static Builder|MailingEmail unsent()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereMailingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingEmail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailingEmail extends BaseMailingEmail
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\MailingEmail\MailingEmail::class;
    }

    // Attributes

    /**
     *
     * @return Attribute
     */
    public function modelType(): Attribute
    {
        return new Attribute(fn (): string => \N1ebieski\ICore\Models\Newsletter::class);
    }

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'newsletter');
    }
}
