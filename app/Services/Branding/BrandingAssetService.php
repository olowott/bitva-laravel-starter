<?php

namespace App\Services\Branding;

use App\Services\SettingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandingAssetService
{
    public function __construct(
        protected SettingService $settingService
    ) {
    }

    public function store(
        string $key,
        UploadedFile $file
    ): string {
        $oldPath = $this->settingService->get($key);

        $path = $file->store(
            'branding',
            'public_assets'
        );

        $this->settingService->set(
            $key,
            $path
        );

        if (
            $oldPath &&
            $oldPath !== $path &&
            Storage::disk('public_assets')->exists($oldPath)
        ) {
            Storage::disk('public_assets')->delete($oldPath);
        }

        return $path;
    }

    public function remove(string $key): void
    {
        $path = $this->settingService->get($key);

        if (
            $path &&
            Storage::disk('public_assets')->exists($path)
        ) {
            Storage::disk('public_assets')->delete($path);
        }

        $this->settingService->set(
            $key,
            null
        );
    }
}
