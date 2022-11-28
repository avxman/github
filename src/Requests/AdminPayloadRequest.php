<?php

namespace Avxman\Github\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminPayloadRequest extends FormRequest {

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
        return [
            'link' => 'required|string|min:85',
            'group' => 'required|string',
            'view' => 'required|string'
        ];
    }

    /**  */
    public function messages()
    {
        return [
            'link.required'=>'The link should be filled',
            'link.string'=>'The link should be a line',
            'link.min'=>'The link should be at least :min characters',
            'group.required'=>'The group should be filled',
            'group.string'=>'The group should be a line',
            'view.required'=>'The template must be filled',
            'view.string'=>'The template should be a line',
        ];
    }

}
