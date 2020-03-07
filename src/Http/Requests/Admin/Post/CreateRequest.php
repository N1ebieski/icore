<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Post\Category;

class CreateRequest extends FormRequest
{
    /**
     * [private description]
     * @var Category
     */
    protected $category;

    /**
     * [__construct description]
     * @param Category $category [description]
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
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

    public function prepareForValidation()
    {
        // Brzyki hook, ale nie mam innego pomyslu. Request dla kategorii zwraca tylko IDki
        // a w widoku edycji posta potrzebujemy calej kolekcji, co w przypadku wstawiania
        // danych z helpera old() stanowi problem
        if ($this->old('categories')) {
            $this->session()->flash(
                '_old_input.categories_collection',
                $this->category->makeRepo()->getByIds($this->old('categories'))
            );
        } else {
            $this->session()->forget('_old_input.categories_collection');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
