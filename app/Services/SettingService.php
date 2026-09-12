<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (!array_key_exists($key, $settings)) {
            return $default;
        }

        return $settings[$key];
    }

    public function all(): array
    {
        return Cache::rememberForever('app.settings', function () {
            return Setting::query()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function group(string $group): array
    {
        return Cache::rememberForever("app.settings.{$group}", function () use ($group) {
            return Setting::query()
                ->where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function set(string $key, mixed $value): Setting
    {
        $setting = Setting::query()
            ->where('key', $key)
            ->firstOrFail();

        $setting->update([
            'value' => $value,
        ]);

        $this->clearCache();

        return $setting;
    }

    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::query()
                ->where('key', $key)
                ->update([
                    'value' => $value,
                ]);
        }

        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget('app.settings');

        Setting::query()
            ->distinct()
            ->pluck('group')
            ->each(
                fn($group) => Cache::forget("app.settings.{$group}")
            );
    }
}
