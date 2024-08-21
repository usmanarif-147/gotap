<?php

namespace App\Http\Requests\Api\Platform;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SwapPlatformRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'orderList' => ['required', 'array'],
            'orderList.*.id' => ['required', 'integer'],
            'orderList.*.order' => ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'orderList.required' => 'Order List is required.',
            'orderList.array' => 'Order List must be an array.',
            'orderList.*.id.required' => 'Each item in the Order List must have an ID.',
            'orderList.*.id.integer' => 'Each ID in the Order List must be an integer.',
            'orderList.*.order.required' => 'Each item in the Order List must have an order value.',
            'orderList.*.order.integer' => 'Each order value in the Order List must be an integer.',
        ];
    }
}
