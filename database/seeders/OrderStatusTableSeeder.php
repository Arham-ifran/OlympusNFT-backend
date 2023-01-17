<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('order_statuses')->delete();

        \DB::table('order_statuses')->insert(array(
            0 =>
            array(
                'id' => 1,
                'title' => 'Pending',
                'is_active' => 1,

            ),
            1 => array(
                'id' => 2,
                'title' => 'In progress',
                'is_active' => 1,

            ),
            2 => array(
                'id' => 3,
                'title' => 'Completed',
                'is_active' => 1,

            ),
            3 => array(
                'id' => 4,
                'title' => 'Cancel',
                'is_active' => 1,

            ),

        ));

    }
}
