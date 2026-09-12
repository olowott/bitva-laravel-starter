<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'roles.view',
            'roles.manage',

            'settings.view',
            'settings.manage',

            'documents.view',
            'documents.upload',
            'documents.download',
            'documents.delete',

            'users.export',
            'activity.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions([
            'users.view',
            'users.create',
            'users.update',
            'roles.view',
            'settings.view',
            'documents.view',
            'documents.upload',
            'documents.download',
            'users.export',
            'activity.export',
        ]);
    }
}
