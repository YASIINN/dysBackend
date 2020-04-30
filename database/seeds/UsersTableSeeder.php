<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::truncate();
        $faker = \Faker\Factory::create();
         \App\User::create([
          'name'=> 'adem özgen',
          'email'=>'aozgen@gmail.com',
          'password'=>bcrypt('secret')
         ]);

         foreach (range(1,10) as $i) {
             \App\User::create([
                 'name'=>$faker->name,
                 'email'=>$faker->email,
                 'password'=> str_random()
             ]);
             # code...
         }
        //
    }
}
