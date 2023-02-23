<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Api\CompanyModel;
use CodeIgniter\I18n\Time;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $company = new CompanyModel;
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
          $company->save(
                [
                    'companyType' =>    $faker->randomElement(['광고주', '광고대행사']),
                    'companyName' =>    $faker->company(),
                    'companyTel'  =>    $faker->phoneNumber(),
                    'created_at'  =>    Time::createFromTimestamp($faker->unixTime()),
                    'updated_at'  =>    Time::now(),
                    'deleted_at'  =>    NULL,
                ]
            );
        }
    }
}
