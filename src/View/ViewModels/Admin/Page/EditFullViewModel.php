<?php

namespace N1ebieski\ICore\View\ViewModels\Admin\Page;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\User;
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Database\Eloquent\Collection;

class EditFullViewModel extends ViewModel
{
    /**
     * Undocumented variable
     *
     * @var Page
     */
    public $page;

    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Request
     */
    protected $request;

    /**
     * Undocumented function
     *
     * @param Page $page
     * @param User $user
     * @param Request $request
     */
    public function __construct(
        Page $page,
        User $user,
        Request $request
    ) {
        $this->page = $page;
        $this->user = $user;

        $this->request = $request;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function parents(): Collection
    {
        return $this->page->makeService()->getAsFlatTreeExceptSelf();
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
            /**
             * @var User|null
             */
            return $this->user->find($userId);
        }

        return $this->page->user;
    }
}
