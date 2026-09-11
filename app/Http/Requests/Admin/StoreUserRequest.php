<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'role' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    if (!$this->user()->can('roles.manage')) {
                        $fail('You are not authorised to assign roles.');

                        return;
                    }

                    if (!Role::where('name', $value)->exists()) {
                        $fail('The selected role is invalid.');
                    }
                },
            ],
        ];
    }
}