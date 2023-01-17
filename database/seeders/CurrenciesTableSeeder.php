<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('currencies')->delete();

        \DB::table('currencies')->insert(array(
            0 =>
            array(
                'id' => 1,
                'currency' => 'Bitcoin',
                'code' => 'BTC',
                'is_active' => 1,
                'updated_at' => NULL,
                'created_at' => NULL
            ),
            1 => array(
                'id' => 2,
                'currency' => 'Monero',
                'code' => 'XMR',
                'is_active' => 1,
                'updated_at' => NULL,
                'created_at' => NULL
            )
        ));
    }
}
