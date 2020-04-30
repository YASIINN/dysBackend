<?php

use Illuminate\Database\Seeder;

class SchoolTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\School::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\School::create([
            'sName' => 'Ana Okulu',
            'sCode' => 'ANA001',
            'company_id' => 1
        ]);
        \App\Models\School::create([
            'sName' => 'İlk Okulu',
            'sCode' => 'İLK001',
            'company_id' => 1
        ]);
        \App\Models\School::create([
            'sName' => 'Orta Okulu',
            'sCode' => 'ORT001',
            'company_id' => 1
        ]);
    }
}
