<?php

use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Branches::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Branches::create([
            'bName' => 'A',
            'bCode' => 'ZFR',
        ]);
        foreach (range(1, 10) as $i) {
            \App\Models\Branches::create([
                'bName' => $faker->text,
                'bCode' => $faker->postcode
            ]);
        }
    }
}
