<?php

namespace Seeds\ICore;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use N1ebieski\ICore\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * [RolesAndPermissionsSeeder description]
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permdissions
        Permission::create(['name' => 'access admin']);

        Permission::create(['name' => 'index dashboard']);

        Permission::create(['name' => 'index users']);

        Permission::create(['name' => 'index posts']);
        Permission::create(['name' => 'create posts']);
        Permission::create(['name' => 'edit posts']);
        Permission::create(['name' => 'destroy posts']);
        Permission::create(['name' => 'status posts']);

        Permission::create(['name' => 'index categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'edit categories']);
        Permission::create(['name' => 'destroy categories']);
        Permission::create(['name' => 'status categories']);

        Permission::create(['name' => 'index comments']);
        Permission::create(['name' => 'show comments']);
        Permission::create(['name' => 'create comments']);
        Permission::create(['name' => 'suggest comments']);
        Permission::create(['name' => 'edit comments']);
        Permission::create(['name' => 'destroy comments']);
        Permission::create(['name' => 'status comments']);

        Permission::create(['name' => 'index bans']);
        Permission::create(['name' => 'create bans']);
        Permission::create(['name' => 'edit bans']);
        Permission::create(['name' => 'destroy bans']);

        Permission::create(['name' => 'index mailings']);
        Permission::create(['name' => 'create mailings']);
        Permission::create(['name' => 'edit mailings']);
        Permission::create(['name' => 'destroy mailings']);
        Permission::create(['name' => 'status mailings']);

        Permission::create(['name' => 'index pages']);
        Permission::create(['name' => 'create pages']);
        Permission::create(['name' => 'edit pages']);
        Permission::create(['name' => 'destroy pages']);
        Permission::create(['name' => 'status pages']);

        Permission::create(['name' => 'index roles']);

        // create roles and assign created permissions
        $role = Role::create(['name' => 'super-admin']);

        $role = Role::create(['name' => 'admin'])
            ->givePermissionTo([
                'access admin',
                'index dashboard',
                'index users',
                'index posts',
                'create posts',
                'edit posts',
                'destroy posts',
                'status posts',
                'index categories',
                'create categories',
                'edit categories',
                'destroy categories',
                'status categories',
                'index comments',
                'show comments',
                'create comments',
                'edit comments',
                'destroy comments',
                'status comments',
                'index bans',
                'create bans',
                'edit bans',
                'destroy bans',
                'index mailings',
                'create mailings',
                'edit mailings',
                'destroy mailings',
                'status mailings',
                'index pages',
                'create pages',
                'edit pages',
                'destroy pages',
                'status pages',
                'index roles'
            ]);

        $role = Role::create(['name' => 'user'])
            ->givePermissionTo([
                'create comments'
            ]);

        $user = User::create([
            'name' => 'N1ebieski',
            'ip' => '324.544.23.67',
            'email' => 'mariusz.wysokinski@neostrada.pl',
            'password' => Hash::make('depet1'),
            'email_verified_at' => now(),
            'status' => 1
        ]);
        $user->assignRole(['super-admin', 'user']);
    }
}
