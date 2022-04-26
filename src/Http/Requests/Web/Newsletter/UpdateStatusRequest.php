<?php

namespace N1ebieski\ICore\Http\Requests\Web\Newsletter;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->newsletter->token->token !== $this->input('token')) {
            App::abort(HttpResponse::HTTP_FORBIDDEN, 'The token is invalid.');
        }

        if (
            $this->input('status') == Status::ACTIVE
            && Carbon::parse($this->newsletter->token->updated_at)->lessThan(
                Carbon::now()->subMinutes(60)
            )
        ) {
            App::abort(HttpResponse::HTTP_FORBIDDEN, 'The token period has expired.');
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
            'token' => 'bail|required|string|max:255',
            'status' => 'bail|required|int|in:0,1'
        ];
    }
}
