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

namespace N1ebieski\ICore\Loads\Admin\Post;

use Illuminate\Http\Request;
use N1ebieski\ICore\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class EditFullLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        /** @var Post */
        $post = $request->route('post');

        $post->loadAllRels([
            'categories' => function (MorphToMany|Builder|Category $query) {
                return $query->withAncestorsExceptSelf()->with('langs');
            }
        ]);
    }
}
