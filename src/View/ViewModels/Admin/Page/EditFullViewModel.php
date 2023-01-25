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

namespace N1ebieski\ICore\View\ViewModels\Admin\Page;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\User;
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;

class EditFullViewModel extends ViewModel
{
    /**
     * Undocumented function
     *
     * @param Page $page
     * @param User $user
     * @param Request $request
     */
    public function __construct(
        public Page $page,
        protected User $user,
        protected Request $request
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function parents(): Collection
    {
        $parents = $this->page->makeService()->getAsFlatTreeExceptSelf();

        if (
            !$this->page->isRoot()
            && $parents->doesntContain(fn (Page $page) => $page->parent_id === $this->page->parent_id)
        ) {
            $parents = $parents->merge([$this->page->parent->loadAncestorsExceptSelf()->load('langs')]);
        }

        return $parents;
    }

    /**
     * [userSelection description]
     *
     * @return  User|null  [return description]
     */
    public function userSelection(): ?User
    {
        $userId = $this->request->old('user');

        if (!is_null($userId)) {
            /** @var User|null */
            return $this->user->find($userId);
        }

        return $this->page->user;
    }
}
