<?php

use Illuminate\Database\Seeder;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Province::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Province::create([
          'pName'=> 'Web Geliştirme',
          'pCode'=>'WG'
         ]);

         foreach (range(1,10) as $i) {
             \App\Models\Province::create([
                 'pName'=>$faker->name,
                 'pCode'=>$faker->realText($maxNbChars = 11, $indexSize = 4)
             ]);
             # code...
         }
    }
}
