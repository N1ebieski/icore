<?php

namespace N1ebieski\ICore\Http\Resources\Role;

use N1ebieski\ICore\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'created_at_diff' => $this->created_at_diff,
            'updated_at' => $this->when(
                optional($request->user())->can('admin.users.view'),
                function () {
                    return $this->updated_at;
                }
            ),
            'updated_at_diff' => $this->when(
                optional($request->user())->can('admin.users.view'),
                function () {
                    return $this->updated_at_diff;
                }
            )
            ];
    }
}
