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

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\User;

trait HasAuthor
{
    /**
     *
     * @param User $user
     * @return self
     */
    public function setAuthor(User $user): self
    {
        $this->parameters['author'] = $user;

        return $this;
    }

    /**
     *
     * @param int|null $id
     * @return void
     */
    public function filterAuthor(int $id = null): void
    {
        $this->parameters['author'] = null;

        if ($id !== null) {
            if ($author = $this->findAuthor($id)) {
                $this->setAuthor($author);
            }
        }
    }

    /**
     * [findAuthor description]
     * @param  int  $id [description]
     * @return User|null     [description]
     */
    protected function findAuthor(int $id): ?User
    {
        return User::find($id);
    }
}
