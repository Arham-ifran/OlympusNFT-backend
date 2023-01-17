<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('event_types')->delete();

        \DB::table('event_types')->insert([
            [
                'id' => 1,
                'event_name' => 'Admin Login',
            ],
            [
                'id' => 2,
                'event_name' => 'Admin Logout',
            ],
            [
                'id' => 3,
                'event_name' => 'Admin Profile Update',
            ],
            [
                'id' => 4,
                'event_name' => 'Admin Site Setting Update',
            ],
            [
                'id' => 5,
                'event_name' => 'Category Add',
            ],
            [
                'id' => 6,
                'event_name' => 'Category Update',
            ],
            [
                'id' => 7,
                'event_name' => 'Category Delete',
            ],
            [
                'id' => 8,
                'event_name' => 'Category Status Change',
            ],
            [
                'id' => 9,
                'event_name' => 'Role Add',
            ],
            [
                'id' => 10,
                'event_name' => 'Role Update',
            ],
            [
                'id' => 11,
                'event_name' => 'Role Delete',
            ],
            [
                'id' => 12,
                'event_name' => 'Role Status Change',
            ],
            [
                'id' => 13,
                'event_name' => 'Permission Add',
            ],
            [
                'id' => 14,
                'event_name' => 'Permission Update',
            ],
            [
                'id' => 15,
                'event_name' => 'Permissions Delete',
            ],
            [
                'id' => 16,
                'event_name' => 'Permissions Status Change',
            ],
            [
                'id' => 17,
                'event_name' => 'Admin User Add',
            ],
            [
                'id' => 18,
                'event_name' => 'Admin User Update',
            ],
            [
                'id' => 19,
                'event_name' => 'Admin User Delete',
            ],
            [
                'id' => 20,
                'event_name' => 'Admin User Status Change',
            ],
           
            [
                'id' => 21,
                'event_name' => 'Store Delete',
            ],
            [
                'id' => 22,
                'event_name' => 'Investor Update',
            ],
           
            [
                'id' => 23,
                'event_name' => 'Investor Delete',
            ],
            [
                'id' => 24,
                'event_name' => 'Investor Status Change',
            ],
            [
                'id' => 25,
                'event_name' => 'Artist Update',
            ],
            [
                'id' => 26,
                'event_name' => 'Artist Delete',
            ],
            [
                'id' => 27,
                'event_name' => 'Artist Status Change',
            ],
            [
                'id' => 28,
                'event_name' => 'Musician Update',
            ],
            [
                'id' => 29,
                'event_name' => 'Musician Delete',
            ],
            [
                'id' => 30,
                'event_name' => 'Musician Status Change',
            ],
            [
                'id' => 31,
                'event_name' => 'Product Delete',
            ],
            [
                'id' => 32,
                'event_name' => 'Auction Product Delete',
            ],
            [
                'id' => 33,
                'event_name' => 'Product Report Abuses Add',
            ],
            [
                'id' => 34,
                'event_name' => 'Product Report Abuses Update',
            ],
            [
                'id' => 35,
                'event_name' => 'Product Report Abuses Delete',
            ],
            [
                'id' => 36,
                'event_name' => 'Product Report Status Change',
            ],
            [
                'id' => 37,
                'event_name' => 'Set winner bid',
            ],
            [
                'id' => 38,
                'event_name' => 'Ad Delete',
            ],
            [
                'id' => 39,
                'event_name' => 'Ad Status Change',
            ],
            [
                'id' => 40,
                'event_name' => 'CMS Add',
            ],
            [
                'id' => 41,
                'event_name' => 'CMS Update',
            ],
            [
                'id' => 42,
                'event_name' => 'CMS Delete',
            ],
            [
                'id' => 43,
                'event_name' => 'CMS Status Change',
            ],
            [
                'id' => 44,
                'event_name' => 'Faq Category Add',
            ],
            [
                'id' => 45,
                'event_name' => 'Faq Category Update',
            ],
            [
                'id' => 46,
                'event_name' => 'Faq Category Delete',
            ],
            [
                'id' => 47,
                'event_name' => 'Faq Category Status Change',
            ],
            [
                'id' => 48,
                'event_name' => 'Faq Add',
            ],
            [
                'id' => 49,
                'event_name' => 'Faq Update',
            ],
            [
                'id' => 50,
                'event_name' => 'Faq  Delete',
            ],
            [
                'id' => 51,
                'event_name' => 'Faq Status Change',
            ],
            [
                'id' => 52,
                'event_name' => 'Blog Category Add',
            ],
            [
                'id' => 53,
                'event_name' => 'Blog Category Update',
            ],
            [
                'id' => 54,
                'event_name' => 'Blog Category Delete',
            ],
            [
                'id' => 55,
                'event_name' => 'Blog Category Status Change',
            ],
            [
                'id' => 56,
                'event_name' => 'Blog Add',
            ],
            [
                'id' => 57,
                'event_name' => 'Blog Update',
            ],
            [
                'id' => 58,
                'event_name' => 'Blog  Delete',
            ],
            [
                'id' => 59,
                'event_name' => 'Blog Status Change',
            ],
            [
                'id' => 60,
                'event_name' => 'Language Add',
            ],
            [
                'id' => 61,
                'event_name' => 'Language Update',
            ],
            [
                'id' => 62,
                'event_name' => 'Language  Delete',
            ],
            [
                'id' => 63,
                'event_name' => 'Language Status Change',
            ],
            [
                'id' => 64,
                'event_name' => 'Video Guide Add',
            ],
            [
                'id' => 65,
                'event_name' => 'Video Guide Update',
            ],
            [
                'id' => 66,
                'event_name' => 'Video Guide  Delete',
            ],
            [
                'id' => 67,
                'event_name' => 'Video Status Change',
            ],
            [
                'id' => 68,
                'event_name' => 'Email Template Add',
            ],
            [
                'id' => 69,
                'event_name' => 'Email Template Update',
            ],
            [
                'id' => 70,
                'event_name' => 'Email Template Delete',
            ],
            [
                'id' => 71,
                'event_name' => 'Email Template Status Change',
            ],
            [
                'id' => 72,
                'event_name' => 'Product Status Change',
            ],
            [
                'id' => 73,
                'event_name' => 'Auction Product Status Change',
            ],
        ]);
    }
}
