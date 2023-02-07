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

namespace N1ebieski\ICore\Http\Requests\Admin\Mailing;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Mailing\Status;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\Traits\HasEmailsJson;

class StoreRequest extends FormRequest
{
    use HasEmailsJson;

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
    protected function prepareForValidation(): void
    {
        $this->prepareEmailsJsonAttribute();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            [
                'title' => 'required|min:3|max:255',
                'status' => [
                    'bail',
                    'required',
                    'integer',
                    Rule::in([Status::ACTIVE, Status::INACTIVE, Status::SCHEDULED]),
                    'no_js_validation'
                ],
                'users' => 'in:true,false|no_js_validation',
                'newsletter' => 'in:true,false|no_js_validation',
                'emails' => 'in:true,false|no_js_validation',
                'emails_json' => 'nullable|required_if:emails,true|json|no_js_validation',
                'date_activation_at' => [
                    'required_if:status,' . Status::SCHEDULED,
                    'date',
                    'no_js_validation'
                ],
                'time_activation_at' => [
                    'required_if:status,' . Status::SCHEDULED,
                    'date_format:"H:i"',
                    'no_js_validation'
                ]
            ],
            count(Config::get('icore.multi_langs')) > 1 ? [
                'auto_translate' => 'boolean'
            ] : []
        );
    }
}
