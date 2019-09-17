<?php

namespace N1ebieski\ICore\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Role;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    /**
     * [private description]
     * @var Role
     */
    private $role;

    /**
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Keszuje bo JS validator w blade odwoluje sie do tej metody i duplikuje
        // zapytanie o liste dostepnych rol uzytkownikow, a przez dependency injection
        // sie nie da
        // $roles = cache()->remember('indexRequest.rules.roles',
        // now()->addMinutes(config('icore.cache.minutes')), function() {
        //     return Role::getIdsAsArray();
        // });

        $paginate = config('database.paginate');

        return [
            'page' => 'integer',
            'filter' => 'array|no_js_validation',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.status' => 'bail|nullable|integer|in:0,1|no_js_validation',
            'filter.role' => [
                'bail',
                'nullable',
                Rule::in($this->role->getRepo()->getIdsAsArray()),
                'no_js_validation'
            ],
            'filter.orderby' => [
                'bail',
                'nullable',                
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc',
                'no_js_validation'
            ],
            'filter.paginate' => Rule::in([$paginate, ($paginate*2), ($paginate*4)]) . '|integer|no_js_validation'
        ];
    }

    // /**
    //  * Inject GET parameter "type" into validation data
    //  *
    //  * @param array $keys Properties to only return
    //  *
    //  * @return array
    //  */
    // public function all($keys = null)
    // {
    //     $request = parent::all($keys);
    //
    //     return $request;
    // }
}
