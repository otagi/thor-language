<?php

namespace Thor\Language;

class LanguagesTableSeeder extends \Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->delete();

        Language::create(array('name' => 'English', 'code' => 'en', 'locale' => 'en_US', 'is_active' => true, 'sorting' => 1));
        Language::create(array('name' => 'Español', 'code' => 'es', 'locale' => 'es_ES', 'is_active' => true, 'sorting' => 2));
        Language::create(array('name' => 'Français', 'code' => 'fr', 'locale' => 'fr_FR', 'is_active' => true, 'sorting' => 3));
        Language::create(array('name' => 'Deutsch', 'code' => 'de', 'locale' => 'de_DE', 'is_active' => true, 'sorting' => 4));
        Language::create(array('name' => 'Italiano', 'code' => 'it', 'locale' => 'it_IT', 'is_active' => true, 'sorting' => 5));
    }

}
