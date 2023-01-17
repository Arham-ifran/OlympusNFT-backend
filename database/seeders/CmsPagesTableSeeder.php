<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CmsPagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('cms_pages')->delete();

        \DB::table('cms_pages')->insert([
            [
                'id' => 1,
                'title' => 'About Us',
                'seo_url' => 'about-us',
                'description' => 'About Us',
                'meta_description' => 'About Us',
                'meta_title' => 'About Us',
                'meta_keywords' => 'About Us',
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Contact Us',
                'seo_url' => 'contact-us',
                'description' => 'Contact Us',
                'meta_description' => 'Contact Us',
                'meta_title' => 'Contact Us',
                'meta_keywords' => 'Contact Us',
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'is_active' => 1
            ],
            [
                'id' => 3,
                'title' => 'Privacy Policy',
                'seo_url' => 'privacy-policy',
                'description' => 'Privacy Policy',
                'meta_description' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy',
                'meta_keywords' => 'Privacy Policy',
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'is_active' => 1
            ],
            [
                'id' => 4,
                'title' => 'Term Of Use',
                'seo_url' => 'term-of-use',
                'description' => 'Term Of Use',
                'meta_description' => 'Term Of Use',
                'meta_title' => 'Term Of Use',
                'meta_keywords' => 'Term Of Use',
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'is_active' => 1
            ],
        ]);
    }
}
