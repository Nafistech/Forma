<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            "key"=>"page_header",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"page_outro",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"logo",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"fb_link",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"twitter_link",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"instagram_link",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"Header_Color",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"font_family",
            "value"=>""
        ]);
        Setting::create([
            "key"=>"font_size",
            "value"=>""
        ]);


    }
}
