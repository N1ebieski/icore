<?php

namespace N1ebieski\ICore\Filters;

use N1ebieski\ICore\Models\User;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collect;

abstract class Filter
{
    protected $collect;

    public $parameters;

    public function __construct(Request $request, Collect $collect)
    {
        $this->collect = $collect;
        $this->setFilters((array)$request->input('filter'));
    }

    public function setRole(Role $role)
    {
        $this->parameters['role'] = $role;

        return $role;
    }

    public function setAuthor(User $user)
    {
        $this->parameters['author'] = $user;

        return $this;
    }

    public function setCategory(Category $category)
    {
        $this->parameters['category'] = $category;

        return $this;
    }

    public function setFilters(array $attributes) : self
    {
        foreach ($this->filters as $filter) {
            $method_name = 'filter'.ucfirst($filter);
            if (method_exists($this, $method_name)) {
                $this->$method_name(
                    array_key_exists($filter, $attributes) ?
                    (strlen($attributes[$filter]) ? $attributes[$filter] : null)
                    : null
                );
            }
        }

        return $this;
    }

    public function filterSearch(string $value = null) : void
    {
        $this->parameters['search'] = $value;
    }

    public function filterReport(int $value = null) : void
    {
        $this->parameters['report'] = $value;
    }

    public function filterPaginate(int $value = null) : void
    {
        $this->parameters['paginate'] = $value;
    }

    public function filterOrderBy(string $value = null) : void
    {
        $this->parameters['orderby'] = $value;
    }

    public function filterCensored(int $value = null) : void
    {
        $this->parameters['censored'] = $value;
    }

    public function filterStatus(int $value = null) : void
    {
        $this->parameters['status'] = $value;
    }

    public function filterRole(int $id = null) : void
    {
        $this->parameters['role'] = null;

        if ($id !== null) {
            if ($role = $this->findRole($id))
            {
                $this->setRole($role);
            }
        }
    }

    public function findRole(int $id = null) : Role
    {
        return Role::find($id, ['id', 'name']);
    }

    public function filterParent(int $id = null)
    {
        $this->parameters['parent'] = null;

        if ($id === 0) {
            return $this->parameters['parent'] = 0;
        }

        if ($id !== null) {
            if ($parent = $this->findParent($id))
            {
                return $this->setParent($parent);
            }
        }
    }

    public function filterCategory(int $id = null)
    {
        $this->parameters['category'] = null;

        if ($id === 0) {
            return $this->parameters['category'] = 0;
        }

        if ($id !== null) {
            if ($category = $this->findCategory($id))
            {
                return $this->setCategory($category);
            }
        }
    }

    public function findCategory(int $id = null) : Category
    {
        return Category::find($id, ['id', 'name']);
    }

    public function filterAuthor(int $id = null) : void
    {
        $this->parameters['author'] = null;

        if ($id !== null) {
            if ($author = $this->findAuthor($id))
            {
                $this->setAuthor($author);
            }
        }
    }

    protected function findAuthor(int $id) : User
    {
        return User::find($id, ['id', 'name']);
    }

    public function all() : array
    {
        return (array)$this->parameters;
    }

    public function get(string $parameter)
    {
        return $this->parameters[$parameter] ?? null;
    }

    /**
     * Check if all parameters are null
     *
     * @return bool [description]
     */
    public function isNull() : bool
    {
        if ($this->parameters) {
            if (!array_filter($this->parameters, function($value) {
                return $value === null;
            })) return false;
        }

        return true;
    }
}
