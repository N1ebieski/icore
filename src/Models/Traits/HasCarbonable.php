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

namespace N1ebieski\ICore\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Container\BindingResolutionException;

trait HasCarbonable
{
    // Accessors

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function createdAtDiff(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\CreatedAtDiff::class, ['model' => $this])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function updatedAtDiff(): Attribute
    {
        return App::make(\N1ebieski\ICore\Attributes\UpdatedAtDiff::class, ['model' => $this])();
    }
}
