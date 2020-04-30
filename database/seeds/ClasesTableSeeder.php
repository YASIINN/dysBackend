<?php

use Illuminate\Database\Seeder;

class ClasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Clases::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Clases::create([
            'cName'=> '9',
            'cCode'=> 'L1',
        ]);
        foreach (range(1,10) as $i) {
            \App\Models\Clases::create([
                'cName' => $faker->numberBetween(1, 12),
                'cCode' => $faker->text
            ]);
        }
    }
}
