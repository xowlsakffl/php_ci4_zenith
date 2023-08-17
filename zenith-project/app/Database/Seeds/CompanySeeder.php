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
        $db = \Config\Database::connect();
        $builder = $db->table('event_advertiser');
        $builder->select('
			name
		');
        $adv = $builder->get()->getResultArray();
        foreach($adv as $v){
        $data = [
            'type' =>    '',
            'name' =>    $v['name'],
            'tel'  =>    '',
            'created_at'  =>    Time::createFromTimestamp($faker->unixTime()),
            'updated_at'  =>    Time::now(),
            'deleted_at'  =>    NULL,
        ];

        $builder = $db->table('companies');
        $builder->insert($data);
        }
    }
}
