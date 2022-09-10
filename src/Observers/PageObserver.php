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

namespace N1ebieski\ICore\Observers;

use Illuminate\Support\Facades\Cache;
use N1ebieski\ICore\Models\Page\Page;

class PageObserver
{
    /**
     * [private description]
     * @var bool
     */
    private static $pivotEvent = false;

    /**
     * Handle the link "saving" event.
     *
     * @param  Page  $page
     * @return void
     */
    public function saving(Page $page)
    {
        $page->real_depth = $page->getNextRealDepth();
    }

    /**
     * Handle the link "saved" event.
     *
     * @param  Page  $page
     * @return void
     */
    public function saved(Page $page)
    {
        $page->reorderRealDepths();
    }

    /**
     * Handle the page "created" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function created(Page $page)
    {
        Cache::tags(['pages'])->flush();
    }

    /**
     * Handle the page "updated" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function updated(Page $page)
    {
        Cache::tags(['page.' . $page->slug, 'pages'])->flush();
    }

    /**
     *
     * @param Page $page
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @param mixed $pivotIdsAttributes
     * @return void
     */
    public function pivotAttached(Page $page, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['tags'])) {
            $this->updated($page);

            self::$pivotEvent = true;
        }
    }

    /**
     *
     * @param Page $page
     * @param mixed $relationName
     * @param mixed $pivotIds
     * @return void
     */
    public function pivotDetached(Page $page, $relationName, $pivotIds)
    {
        if (self::$pivotEvent === false && in_array($relationName, ['tags'])) {
            $this->updated($page);

            self::$pivotEvent = true;
        }
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function deleted(Page $page)
    {
        $page->nextSiblings()->decrement($page->getPositionColumn());

        Cache::tags(['page.' . $page->slug, 'pages'])->flush();
    }

    /**
     * Handle the page "restored" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function restored(Page $page)
    {
        //
    }

    /**
     * Handle the page "force deleted" event.
     *
     * @param  \N1ebieski\ICore\Models\Page\Page  $page
     * @return void
     */
    public function forceDeleted(Page $page)
    {
        //
    }
}
