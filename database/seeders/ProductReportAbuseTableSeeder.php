<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductReportAbuseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {     
        \DB::table('product_report_abuses')->delete();
        \DB::table('product_report_abuses')->insert([
            [
                'id' => 1,
                'title' => 'Copyright Infringement',
                'description' => 'This listing is stolen content or the user selling this does not own the rights to the content.',
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Illegal content, or illegal services offered',
                'description' => 'This listing is breaking some sort of law and is illegal.',
                'is_active' => 1
            ],
            [
                'id' => 3,
                'title' => 'This listing is rude or offensive',
                'description' => 'A reasonable person would find this listing vulgar and offensive. This type of content should not be allowed on the site.',
                'is_active' => 1
            ],
            [
                'id' => 4,
                'title' => 'Other - needs human review',
                'description' => 'A problem not listed above that requires action by a moderator. Be specific and detailed!',
                'is_active' => 1
            ],
        ]);
    }
}
