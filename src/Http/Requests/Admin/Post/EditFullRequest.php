<?php

namespace N1ebieski\ICore\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Models\Category\Post\Category;

class EditFullRequest extends FormRequest
{
    /**
     * [private description]
     * @var Category
     */
    private $category;

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
            session()->flash('_old_input.categories_collection',
                $this->category->getRepo()->getByIds($this->old('categories')));
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
