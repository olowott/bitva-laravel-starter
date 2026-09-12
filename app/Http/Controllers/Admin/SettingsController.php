<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        SettingService $settingService
    ): RedirectResponse {
        $settingService->setMany(
            $request->validated()
        );

        return redirect()
            ->route('admin.settings.edit')
            ->with(
                'success',
                'Settings updated successfully.'
            );
    }
}
