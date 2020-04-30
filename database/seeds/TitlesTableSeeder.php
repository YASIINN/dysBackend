<?php

use Illuminate\Database\Seeder;

class TitlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Title::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Title::create([
          'tName'=> 'Bilgisayar Mühendisi',
          'tCode'=>'BM'
         ]);

         foreach (range(1,10) as $i) {
             \App\Models\Title::create([
                 'tName'=>$faker->name,
                 'tCode'=>$faker->realText($maxNbChars = 11, $indexSize = 4)
             ]);
             # code...
         }
    }
}
