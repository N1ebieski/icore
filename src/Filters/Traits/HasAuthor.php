<?php

namespace N1ebieski\ICore\Filters\Traits;

use N1ebieski\ICore\Models\User;

trait HasAuthor
{
    /**
     * [setAuthor description]
     * @param User $user [description]
     */
    public function setAuthor(User $user)
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
     * @return User     [description]
     */
    protected function findAuthor(int $id): User
    {
        return User::find($id);
    }
}
