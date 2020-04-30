<?php

use Illuminate\Database\Seeder;
class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Company::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Company::create([
            'cName'=> 'Zafer Koleji',
        ]);


    }
}
