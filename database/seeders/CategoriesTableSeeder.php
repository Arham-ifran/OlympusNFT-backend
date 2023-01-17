<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->delete();

        \DB::table('categories')->insert(
            array(
            0 =>
            array(
                'id' => 1,
                'title' => 'Music',
                'url' => 'music',
                'is_active' => 1,
            ),
            1 =>
            array(
                'id' => 2,
                'title' => 'Art',
                'url' => 'art',
                'is_active' => 1,
            ),
            2 =>
            array(
                'id' => 3,
                'title' => 'Film',
                'url' => 'film',
                'is_active' => 1,
            ),
        ));
    }
}
