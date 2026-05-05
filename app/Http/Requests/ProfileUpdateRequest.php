<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'gender'   => ['nullable', 'integer', 'in:0,1,2'],
            'age'      => ['nullable', 'integer', 'min:0', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.max'    => '電話號碼最多 20 個字元',
            'gender.in'    => '性別選項無效',
            'age.integer'  => '年齡必須是數字',
            'age.min'      => '年齡不能小於 0',
            'age.max'      => '年齡不能大於 150',
        ];
    }
}