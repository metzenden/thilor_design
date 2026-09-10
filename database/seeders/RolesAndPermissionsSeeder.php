<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    private const PERMISSIONS = [
        'products.manage',
        'categories.manage',
        'collections.manage',
        'orders.manage',
        'customers.manage',
        'reviews.manage',
        'content.manage',   // pages, bannières
        'marketing.manage', // coupons, newsletter
        'settings.manage',
        'admins.manage',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(self::PERMISSIONS);

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'products.manage', 'categories.manage', 'collections.manage',
            'orders.manage', 'customers.manage', 'reviews.manage', 'content.manage',
        ]);

        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@thilor-design.com'],
            [
                'name' => 'Administrateur THILOR',
                'phone' => '+221 77 000 00 00',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $adminUser->syncRoles(['admin']);
    }
}
