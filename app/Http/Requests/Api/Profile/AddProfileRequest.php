<?php

namespace App\Http\Requests\Api\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddProfileRequest extends FormRequest
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
            'username' => ['required', 'min:3', 'max:20', 'regex:/^[A-Za-z][A-Za-z0-9_.]{5,25}$/', Rule::unique(Profile::class)],
            'work_position' => ['nullable', 'min:3', 'max:20'],
            'phone' => ['nullable', 'min:5', 'max:15'],
            'job_title' => ['nullable', 'string'],
            'company' => ['nullable', 'string'],
            'address' => ['nullable'],
            'bio' => ['nullable'],
            'cover_photo' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => trans('validation.username_required'),
            'username.min' => trans('validation.username_min'),
            'username.max' => trans('validation.username_max'),
            'username.regex' => trans('validation.username_regex'),
            'username.unique' => 'Username already exists',
            'phone.min' => trans('validation.phone_min'),
            'phone.max' => trans('validation.phone_max'),
            'cover_photo.mimes' => trans('validation.cover_photo_mimes'),
            'cover_photo.max' => trans('validation.cover_photo_max'),
            'photo.mimes' => trans('validation.photo_mimes'),
            'photo.max' => trans('validation.photo_max'),
            'job_title.string' => trans('validation.job_title_string'),
            'company.string' => trans('validation.company_string'),
        ];
    }
}
