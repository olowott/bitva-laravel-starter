<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.update');
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
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],

            'password' => [
                'nullable',
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
