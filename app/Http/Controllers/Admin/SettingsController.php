<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\ActivityLogService;
use App\Services\Branding\BrandingAssetService;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        $timezones = timezone_identifiers_list();

        return view(
            'admin.settings.edit',
            compact('settings', 'timezones')
        );
    }

    public function update(
        UpdateSettingsRequest $request,
        SettingService $settingService,
        ActivityLogService $activityLogService,
        BrandingAssetService $brandingAssetService
    ): RedirectResponse {
        $validated = $request->validated();

        $settings = collect($validated)
            ->except([
                'logo',
                'favicon',
            ])
            ->toArray();

        $trackedKeys = [
            ...array_keys($settings),
            'logo',
            'favicon',
        ];

        $before = Setting::query()
            ->whereIn('key', $trackedKeys)
            ->pluck('value', 'key')
            ->toArray();

        $settingService->setMany($settings);

        if ($request->hasFile('logo')) {
            $brandingAssetService->store(
                'logo',
                $request->file('logo')
            );
        }

        if ($request->hasFile('favicon')) {
            $brandingAssetService->store(
                'favicon',
                $request->file('favicon')
            );
        }

        $after = Setting::query()
            ->whereIn('key', $trackedKeys)
            ->pluck('value', 'key')
            ->toArray();

        if ($before !== $after) {
            $activityLogService->log(
                'Application settings updated',
                null,
                [
                    'before' => $before,
                    'after' => $after,
                ],
                'updated'
            );
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with(
                'success',
                'Settings updated successfully.'
            );
    }

    public function removeLogo(
        BrandingAssetService $brandingAssetService,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        $before = setting('logo');

        $brandingAssetService->remove('logo');

        if ($before) {
            $activityLogService->log(
                'Application logo removed',
                null,
                [
                    'before' => $before,
                    'after' => null,
                ],
                'deleted'
            );
        }

        return back()->with(
            'success',
            'Application logo removed successfully.'
        );
    }

    public function removeFavicon(
        BrandingAssetService $brandingAssetService,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        $before = setting('favicon');

        $brandingAssetService->remove('favicon');

        if ($before) {
            $activityLogService->log(
                'Application favicon removed',
                null,
                [
                    'before' => $before,
                    'after' => null,
                ],
                'deleted'
            );
        }

        return back()->with(
            'success',
            'Application favicon removed successfully.'
        );
    }
}
