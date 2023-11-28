<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Api\CompanyModel;
use CodeIgniter\I18n\Time;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('event_advertiser');
        $builder->select('name');
        $adv = $builder->get()->getResultArray();
        
        $compBuilder = $db->table('companies');
        foreach($adv as $v){
            $compBuilder->where('name', $v['name']);
            $existing = $compBuilder->get()->getRow();
            if(!$existing) {
                $data = [
                    'type' =>    '',
                    'name' =>    $v['name'],
                    'tel'  =>    '',
                    'created_at'  =>    Time::now(),
                    'updated_at'  =>    Time::now(),
                ];
                $compBuilder->insert($data);
            }
        }
    }
}
