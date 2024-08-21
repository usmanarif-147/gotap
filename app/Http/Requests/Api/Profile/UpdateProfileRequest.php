<?php

namespace App\Http\Requests\Api\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'max:20'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'min:5', 'max:15'],
            'username' => ['required', 'min:3', 'max:20', 'regex:/^[A-Za-z][A-Za-z0-9_.]{5,25}$/', Rule::unique(Profile::class)->ignore(getActiveProfile()->id)],
            'work_position' => ['nullable', 'string'],
            'job_title' => ['nullable', 'string'],
            'company' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'cover_photo' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'gender.in' => 'Please enter 1 for male, 2 for female, 3 for not-share',
    //         'username.regex' => 'The username must start with a letter and can only contain letters (uppercase or lowercase), numbers, underscores, or periods. It should be between 5 and 25 characters long.'
    //     ];
    // }

    public function messages()
    {
        return [
            'name.required' => trans('validation.name_required'),
            'name.min' => trans('validation.name_min'),
            'name.max' => trans('validation.name_max'),

            'username.required' => trans('validation.username_required'),
            'username.min' => trans('validation.username_min'),
            'username.max' => trans('validation.username_max'),
            'username.regex' => trans('validation.username_regex'),
            'username.unique' => trans('Username already exists'),
            'phone.min' => trans('validation.phone_min'),
            'phone.max' => trans('validation.phone_max'),
            'gender.in' => trans('validation.gender_in'),
            'dob.date' => trans('validation.dob_date'),
            'dob.before' => trans('validation.dob_before'),
            'private.required' => trans('validation.private_required'),
            'name.string' => trans('validation.name_string'),
            'cover_photo.mimes' => trans('validation.cover_photo_mimes'),
            'cover_photo.max' => trans('validation.cover_photo_max'),
            'photo.mimes' => trans('validation.photo_mimes'),
            'photo.max' => trans('validation.photo_max'),
            'job_title.string' => trans('validation.job_title_string'),
            'company.string' => trans('validation.company_string'),
        ];
    }
}
