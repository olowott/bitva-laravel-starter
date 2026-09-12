<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::updateOrCreate(
            [
                'email' => 'admin@bitvatech.test',
            ],
            [
                'name' => 'BitVa Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $user->syncRoles([$role]);
    }
}
