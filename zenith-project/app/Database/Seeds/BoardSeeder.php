<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Api\BoardModel;
use CodeIgniter\I18n\Time;

class BoardSeeder extends Seeder
{
    public function run()
    {
        $board = new BoardModel;
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
          $board->save(
                [
                    'board_title'        =>    $faker->sentence(),
                    'board_description'       =>    $faker->realText(),
                    'created_at'  =>    Time::createFromTimestamp($faker->unixTime()),
                    'updated_at'  =>    Time::now(),
                    'deleted_at' => NULL,
                ]
            );
        }
    }
}
