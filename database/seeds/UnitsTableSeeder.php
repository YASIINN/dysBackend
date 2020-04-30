<?php

use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Units::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Units::create([
            'uName'=> 'IT Birimi',
            'uCode'=>'ITBRM'
        ]);

        foreach (range(1,10) as $i) {
            \App\Models\Units::create([
                'uName'=>$faker->city,
                'uCode'=>$faker->languageCode
            ]);
            # code...
        }
    }
}
