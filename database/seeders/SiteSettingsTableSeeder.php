<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SiteSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('site_settings')->delete();

        \DB::table('site_settings')->insert([
            [
                'id' => 1,
                'site_name' => 'NFT Art Minting',
                'site_title' => 'NFT Art Minting',
                'site_keywords' => 'NFT Art Minting',
                'site_description' => 'NFT Art Minting'
            ]
        ]);
    }
}
