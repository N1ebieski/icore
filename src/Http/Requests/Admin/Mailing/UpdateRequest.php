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
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\ValueObjects\Mailing\Status;

/**
 * @property Mailing $mailing
 */
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->mailing->status->isRunning();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            'emails_json' => 'nullable|required_if:emails,true|json',
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
        ];
    }
}
