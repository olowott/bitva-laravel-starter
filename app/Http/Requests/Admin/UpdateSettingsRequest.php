<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ->can('settings.manage');
    }

    public function rules(): array
    {
        return [
            'app_name' => [
                'required',
                'string',
                'max:255',
            ],

            'app_tagline' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'company_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'timezone' => [
                'required',
                'timezone',
            ],

            'primary_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'secondary_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'favicon' => [
                'nullable',
                'image',
                'mimes:png',
                'max:512',
            ],
        ];
    }
}
