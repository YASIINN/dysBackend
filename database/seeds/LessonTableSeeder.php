<?php

use Illuminate\Database\Seeder;

class LessonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Lessons::truncate();
        $faker = \Faker\Factory::create();
        \App\Models\Lessons::create([
            'lName'=> "Türkçe",
            'lCode'=> 'ZFR-TR',
            "parent_id"=>0,
        ]);
        foreach (range(1,10) as $i) {
            \App\Models\Lessons::create([
                'lName' =>$faker->colorName,
                'lCode' => $faker->text,
                "parent_id"=>0,
            ]);
        }
    }
}
