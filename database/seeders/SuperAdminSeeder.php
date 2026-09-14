<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');
        $name = env('SUPER_ADMIN_NAME', 'Super Admin');

        if (!$email || !$password) {
            $this->command?->warn(
                'Super admin was not created. Set SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD to seed one.'
            );

            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $user->syncRoles(['super_admin']);

        $this->command?->info("Super admin created or updated: {$email}");
    }
}
