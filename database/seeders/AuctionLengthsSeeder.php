<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuctionLengthsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('auction_lengths')->delete();

        \DB::table('auction_lengths')->insert(array(
            0 =>
            array(
                'id' => 1,
                'title' => '12 hours',
                'is_active' => 1,
               
            ),
            1 => array(
                'id' => 2,
                'title' => '24 hours',
                'is_active' => 1,
                
            ),
            2 => array(
                'id' => 3,
                'title' => '2 days',
                'is_active' => 1,
                
            ),
            3 => array(
                'id' => 4,
                'title' => '3 days',
                'is_active' => 1,
                
            ),
            4 => array(
                'id' => 5,
                'title' => '7 days',
                'is_active' => 1,
                
            ),
            4 => array(
                'id' => 5,
                'title' => '7 days',
                'is_active' => 1,
                
            ),
            5 => array(
                'id' => 6,
                'title' => '15 days',
                'is_active' => 1,
                
            ),
            6 => array(
                'id' => 7,
                'title' => '30 days',
                'is_active' => 1,
                
            ),
            7 => array(
                'id' => 8,
                'title' => '60 days',
                'is_active' => 1,
                
            )
        ));
    }
}
