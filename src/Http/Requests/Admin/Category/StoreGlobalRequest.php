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

namespace N1ebieski\ICore\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class StoreGlobalRequest extends FormRequest
{
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
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->prepareNamesAttribute();

        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
        }
    }

    /**
     *
     * @return void
     * @throws BadRequestException
     */
    protected function prepareNamesAttribute(): void
    {
        if ($this->has('names') && preg_match('/^\[[\s\S]*\]$/', $this->get('names')) === 0) {
            $names = explode("\r\n", $this->get('names'));

            $namesToJson = [];

            foreach ($names as $name) {
                if (empty($name)) {
                    continue;
                }

                $namesToJson[] = [
                    'name' => $name
                ];
            }

            if (count($namesToJson) > 0) {
                $this->merge([
                    'names' => json_encode($namesToJson)
                ]);
            }
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
            'names' => 'required|json',
            'clear' => 'boolean',
            'parent_id' => 'nullable|integer|exists:categories,id'
        ];
    }
}
