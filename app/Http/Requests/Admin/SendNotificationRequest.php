<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('notifications.send') ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
                'max:2000',
            ],

            'type' => [
                'required',
                'in:info,success,warning,danger',
            ],

            'url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!$value) {
                        return;
                    }

                    if (
                        !str_starts_with($value, '/')
                        || str_starts_with($value, '//')
                    ) {
                        $fail(
                            'The link must be an internal application path.'
                        );
                    }
                },
            ],

            'send_email' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
