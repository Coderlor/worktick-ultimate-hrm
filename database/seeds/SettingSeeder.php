<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Insert some stuff
        DB::table('settings')->insert(
            array(
                'id' => 1,
                'email' => 'admin@example.com',
                'currency_id' => 1,
                'CompanyName' => 'WorkTick',
                'CompanyPhone' => '6315996770',
                'CompanyAdress' => '3618 Abia Martin Drive',
                'footer' => 'WorkTick - Ultimate HRM & Project Management',
                'developed_by' => 'Ui-Lib',
                'logo' => 'logo-default.png',
                'default_language' => 'en',
            )
            
        );
    }
}
