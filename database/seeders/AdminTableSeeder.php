<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admins')->truncate();

       \DB::table('roles')->delete();
        \DB::table('permissions')->delete();
        $arr = [
            ['name' => 'View Dashboard Latest Orders', 'guard_name' => 'admin'],

            ['name' => 'Edit Setting', 'guard_name' => 'admin'],
            ['name' => 'Update Setting', 'guard_name' => 'admin'],

            ['name' => 'View Investor', 'guard_name' => 'admin'],
            ['name' => 'Add Investor', 'guard_name' => 'admin'],
            ['name' => 'Edit Investor', 'guard_name' => 'admin'],
            ['name' => 'Delete Investor', 'guard_name' => 'admin'],

            ['name' => 'View Musicians', 'guard_name' => 'admin'],
            ['name' => 'Add Musicians', 'guard_name' => 'admin'],
            ['name' => 'Edit Musicians', 'guard_name' => 'admin'],
            ['name' => 'Delete Musicians', 'guard_name' => 'admin'],

            ['name' => 'View Artists', 'guard_name' => 'admin'],
            ['name' => 'Add Artists', 'guard_name' => 'admin'],
            ['name' => 'Edit Artists', 'guard_name' => 'admin'],
            ['name' => 'Delete Artists', 'guard_name' => 'admin'],

            ['name' => 'View Role', 'guard_name' => 'admin'],
            ['name' => 'Add Role', 'guard_name' => 'admin'],
            ['name' => 'Edit Role', 'guard_name' => 'admin'],
            ['name' => 'Delete Role', 'guard_name' => 'admin'],

            ['name' => 'View Permission', 'guard_name' => 'admin'],
            ['name' => 'Add Permission', 'guard_name' => 'admin'],
            ['name' => 'Edit Permission', 'guard_name' => 'admin'],
            ['name' => 'Delete Permission', 'guard_name' => 'admin'],

            ['name' => 'View Category', 'guard_name' => 'admin'],
            ['name' => 'Add Category', 'guard_name' => 'admin'],
            ['name' => 'Edit Category', 'guard_name' => 'admin'],
            ['name' => 'Delete Category', 'guard_name' => 'admin'],

            ['name' => 'View Product', 'guard_name' => 'admin'],
            ['name' => 'Add Product', 'guard_name' => 'admin'],
            ['name' => 'Edit Product', 'guard_name' => 'admin'],
            ['name' => 'Delete Product', 'guard_name' => 'admin'],

            ['name' => 'View Auction Product', 'guard_name' => 'admin'],

            ['name' => 'View Faq Categories', 'guard_name' => 'admin'],
            ['name' => 'Add Faq Categories', 'guard_name' => 'admin'],
            ['name' => 'Edit Faq Categories', 'guard_name' => 'admin'],
            ['name' => 'Delete Faq Categories', 'guard_name' => 'admin'],

            ['name' => 'View Faq', 'guard_name' => 'admin'],
            ['name' => 'Add Faq', 'guard_name' => 'admin'],
            ['name' => 'Edit Faq', 'guard_name' => 'admin'],
            ['name' => 'Delete Faq', 'guard_name' => 'admin'],

            ['name' => 'View Blog Categories', 'guard_name' => 'admin'],
            ['name' => 'Add Blog Categories', 'guard_name' => 'admin'],
            ['name' => 'Edit Blog Categories', 'guard_name' => 'admin'],
            ['name' => 'Delete Blog Categories', 'guard_name' => 'admin'],

            ['name' => 'View Blog', 'guard_name' => 'admin'],
            ['name' => 'Add Blog', 'guard_name' => 'admin'],
            ['name' => 'Edit Blog', 'guard_name' => 'admin'],
            ['name' => 'Delete Blog', 'guard_name' => 'admin'],


            ['name' => 'View Product Report Abuse', 'guard_name' => 'admin'],
            ['name' => 'Add Product Report Abuse', 'guard_name' => 'admin'],
            ['name' => 'Edit Product Report Abuse', 'guard_name' => 'admin'],
            ['name' => 'Delete Product Report Abuse', 'guard_name' => 'admin'],

            ['name' => 'View Product Report Items', 'guard_name' => 'admin'],
            ['name' => 'Delete Product Report Items', 'guard_name' => 'admin'],

            ['name' => 'View Orders', 'guard_name' => 'admin'],

            ['name' => 'View Transactions', 'guard_name' => 'admin'],

            ['name' => 'View Bidding History', 'guard_name' => 'admin'],

            ['name' => 'View Reviews', 'guard_name' => 'admin'],

            ['name' => 'View Event Log', 'guard_name' => 'admin'],

            ['name' => 'View CMS Page', 'guard_name' => 'admin'],
            ['name' => 'Add CMS Page', 'guard_name' => 'admin'],
            ['name' => 'Edit CMS Page', 'guard_name' => 'admin'],
            ['name' => 'Delete CMS Page', 'guard_name' => 'admin'],

            ['name' => 'View Contact Us Log', 'guard_name' => 'admin'],
            ['name' => 'Delete Contact Us Log', 'guard_name' => 'admin'],
            ['name' => 'Detail Contact Us Log', 'guard_name' => 'admin'],
            ['name' => 'Reply Contact Us Log', 'guard_name' => 'admin'],

            ['name' => 'View Messages', 'guard_name' => 'admin'],
            ['name' => 'Send Messages', 'guard_name' => 'admin'],

            ['name' => 'Edit Reviews Status', 'guard_name' => 'admin'],
            ['name' => 'View Feedback Review', 'guard_name' => 'admin'],

            ['name' => 'View Templates', 'guard_name' => 'admin'],
            ['name' => 'Add Templates', 'guard_name' => 'admin'],
            ['name' => 'Edit Templates', 'guard_name' => 'admin'],
            ['name' => 'Delete Templates', 'guard_name' => 'admin'],

            ['name' => 'View Video Guides', 'guard_name' => 'admin'],
            ['name' => 'Add Video Guides', 'guard_name' => 'admin'],
            ['name' => 'Edit Video Guides', 'guard_name' => 'admin'],
            ['name' => 'Delete Video Guides', 'guard_name' => 'admin'],

            ['name' => 'View Store', 'guard_name' => 'admin'],
            ['name' => 'Add Store', 'guard_name' => 'admin'],
            ['name' => 'Edit Store', 'guard_name' => 'admin'],
            ['name' => 'Delete Store', 'guard_name' => 'admin'],

            ['name' => 'View Language', 'guard_name' => 'admin'],
            ['name' => 'Add Language', 'guard_name' => 'admin'],
            ['name' => 'Edit Language', 'guard_name' => 'admin'],
            ['name' => 'Delete Language', 'guard_name' => 'admin'],

            ['name' => 'View Admin Users', 'guard_name' => 'admin'],
            ['name' => 'Add Admin Users', 'guard_name' => 'admin'],
            ['name' => 'Edit Admin Users', 'guard_name' => 'admin'],
            ['name' => 'Delete Admin Users', 'guard_name' => 'admin'],

            ['name' => 'View Ad', 'guard_name' => 'admin'],
            ['name' => 'Add Ad', 'guard_name' => 'admin'],
            ['name' => 'Edit Ad', 'guard_name' => 'admin'],
            ['name' => 'Delete Ad', 'guard_name' => 'admin'],

            ['name' => 'View Banner', 'guard_name' => 'admin'],
            ['name' => 'Add Banner', 'guard_name' => 'admin'],
            ['name' => 'Edit Banner', 'guard_name' => 'admin'],
            ['name' => 'Delete Banner', 'guard_name' => 'admin'],

        ];
        \DB::table('permissions')->insert($arr);

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $user = Admin::create([
            'id' => 1,
            'firstname' => 'OlympusNFT',
            'lastname' => 'Admin',
            'email' => 'definevalue@protonmail.com',
            'password' => Hash::make('2454590'),
            'is_active' => 1,
        ]);

        $user->assignRole($role);
    }
}
