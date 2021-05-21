<?php

namespace N1ebieski\ICore\Seeds\Install;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * [DefaultRolesAndPermissionsSeeder description]
 */
class DefaultRolesAndPermissionsSeeder extends Seeder
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
        Permission::firstOrCreate(['name' => 'admin.*']);
        Permission::firstOrCreate(['name' => 'admin.access']);

        Permission::firstOrCreate(['name' => 'admin.home.*']);
        Permission::firstOrCreate(['name' => 'admin.home.view']);

        Permission::firstOrCreate(['name' => 'admin.users.*']);
        Permission::firstOrCreate(['name' => 'admin.users.view']);

        Permission::firstOrCreate(['name' => 'admin.posts.*']);
        Permission::firstOrCreate(['name' => 'admin.posts.view']);
        Permission::firstOrCreate(['name' => 'admin.posts.create']);
        Permission::firstOrCreate(['name' => 'admin.posts.edit']);
        Permission::firstOrCreate(['name' => 'admin.posts.delete']);
        Permission::firstOrCreate(['name' => 'admin.posts.status']);

        Permission::firstOrCreate(['name' => 'admin.categories.*']);
        Permission::firstOrCreate(['name' => 'admin.categories.view']);
        Permission::firstOrCreate(['name' => 'admin.categories.create']);
        Permission::firstOrCreate(['name' => 'admin.categories.edit']);
        Permission::firstOrCreate(['name' => 'admin.categories.delete']);
        Permission::firstOrCreate(['name' => 'admin.categories.status']);

        Permission::firstOrCreate(['name' => 'admin.comments.*']);
        Permission::firstOrCreate(['name' => 'admin.comments.view']);
        Permission::firstOrCreate(['name' => 'admin.comments.create']);
        Permission::firstOrCreate(['name' => 'admin.comments.suggest']);
        Permission::firstOrCreate(['name' => 'admin.comments.edit']);
        Permission::firstOrCreate(['name' => 'admin.comments.delete']);
        Permission::firstOrCreate(['name' => 'admin.comments.status']);

        Permission::firstOrCreate(['name' => 'admin.bans.*']);
        Permission::firstOrCreate(['name' => 'admin.bans.view']);
        Permission::firstOrCreate(['name' => 'admin.bans.create']);
        Permission::firstOrCreate(['name' => 'admin.bans.edit']);
        Permission::firstOrCreate(['name' => 'admin.bans.delete']);

        Permission::firstOrCreate(['name' => 'admin.mailings.*']);
        Permission::firstOrCreate(['name' => 'admin.mailings.view']);
        Permission::firstOrCreate(['name' => 'admin.mailings.create']);
        Permission::firstOrCreate(['name' => 'admin.mailings.edit']);
        Permission::firstOrCreate(['name' => 'admin.mailings.delete']);
        Permission::firstOrCreate(['name' => 'admin.mailings.status']);

        Permission::firstOrCreate(['name' => 'admin.pages.*']);
        Permission::firstOrCreate(['name' => 'admin.pages.view']);
        Permission::firstOrCreate(['name' => 'admin.pages.create']);
        Permission::firstOrCreate(['name' => 'admin.pages.edit']);
        Permission::firstOrCreate(['name' => 'admin.pages.delete']);
        Permission::firstOrCreate(['name' => 'admin.pages.status']);

        Permission::firstOrCreate(['name' => 'admin.roles.*']);
        Permission::firstOrCreate(['name' => 'admin.roles.view']);

        Permission::firstOrCreate(['name' => 'admin.links.*']);
        Permission::firstOrCreate(['name' => 'admin.links.view']);
        Permission::firstOrCreate(['name' => 'admin.links.create']);
        Permission::firstOrCreate(['name' => 'admin.links.edit']);
        Permission::firstOrCreate(['name' => 'admin.links.delete']);

        Permission::firstOrCreate(['name' => 'admin.filemanager.*']);
        Permission::firstOrCreate(['name' => 'admin.filemanager.read']);
        Permission::firstOrCreate(['name' => 'admin.filemanager.write']);

        Permission::firstOrCreate(['name' => 'admin.tags.*']);
        Permission::firstOrCreate(['name' => 'admin.tags.view']);
        Permission::firstOrCreate(['name' => 'admin.tags.create']);
        Permission::firstOrCreate(['name' => 'admin.tags.edit']);
        Permission::firstOrCreate(['name' => 'admin.tags.delete']);

        Permission::firstOrCreate(['name' => 'web.*']);

        Permission::firstOrCreate(['name' => 'web.comments.*']);
        Permission::firstOrCreate(['name' => 'web.comments.create']);
        Permission::firstOrCreate(['name' => 'web.comments.suggest']);
        Permission::firstOrCreate(['name' => 'web.comments.edit']);

        // create roles and assign created permissions
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        $role = Role::firstOrCreate(['name' => 'admin'])
            ->givePermissionTo(['admin.*', 'web.*']);

        $role = Role::firstOrCreate(['name' => 'user'])
            ->givePermissionTo('web.*');
    }
}
