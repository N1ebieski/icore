<?php

namespace N1ebieski\ICore\Loads\Admin\Category;

use Illuminate\Http\Request;

class EditLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $category = $request->route('category');

        $parent = $category->getParent();

        if ($parent !== null) {
            $parent->loadAncestorsExceptSelf();
        }

        $category->setRelations(['parent' => $parent])
            ->with('descendants');
    }
}
