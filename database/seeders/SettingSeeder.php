<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'BitVa Laravel Starter',
                'type' => 'string',
                'group' => 'general',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'A reusable Laravel application starter.',
                'type' => 'string',
                'group' => 'general',
            ],
            [
                'key' => 'company_name',
                'value' => 'BitVa Tech',
                'type' => 'string',
                'group' => 'general',
            ],
            [
                'key' => 'company_email',
                'value' => null,
                'type' => 'email',
                'group' => 'general',
            ],
            [
                'key' => 'company_phone',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Lagos',
                'type' => 'string',
                'group' => 'general',
            ],
            [
                'key' => 'primary_color',
                'value' => '#4f5bd5',
                'type' => 'color',
                'group' => 'branding',
            ],
            [
                'key' => 'secondary_color',
                'value' => '#111827',
                'type' => 'color',
                'group' => 'branding',
            ],
            [
                'key' => 'logo',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
