<?php

namespace N1ebieski\ICore\Http\Requests\Api\Auth;

use Illuminate\Support\Facades\App;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;

class RefreshRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->user() || !($this->user()->currentAccessToken() instanceof PersonalAccessToken)) {
            return App::abort(
                HttpResponse::HTTP_UNAUTHORIZED,
                'Unauthenticated by token. Used authentication by cookie.'
            );
        }

        return true;
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
