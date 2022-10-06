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

namespace N1ebieski\ICore\View\ViewModels\Admin\Category;

use Illuminate\Http\Request;
use Spatie\ViewModels\ViewModel;
use N1ebieski\ICore\Models\Category\Category;

class CreateViewModel extends ViewModel
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param Request $request
     */
    public function __construct(
        public Category $category,
        protected Request $request
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return Category|null
     */
    public function parent(): ?Category
    {
        $id = $this->request->input('parent_id');

        if (!is_null($id)) {
            /**
             * @var Category|null
             */
            $category = $this->category->find($id);

            return optional($category)->loadAncestorsExceptSelf();
        }

        return null;
    }
}
