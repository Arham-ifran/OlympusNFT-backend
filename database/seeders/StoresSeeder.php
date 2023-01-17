<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('stores')->delete();

        \DB::table('stores')->insert(array(
            0 =>
            array(
                'id' => 0,
                'store_title' => 'NFT Store',
                'sub_title' => 'NFT Store',
                'store_tags' => 'NFT Store',
                'slug' => 'nft-store-1',
                'is_active' => 1,
            ),
        ));
    }
}
