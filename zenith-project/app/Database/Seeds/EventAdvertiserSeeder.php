<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventAdvertiserSeeder extends Seeder
{
    public function run()
    {
        //companies와 event_advertiser name 같은것 company_seq 연결
        $db = \Config\Database::connect();
        $builder = $db->table('companies');
        $builder->select('id, name');
        $adv = $builder->get()->getResultArray();
        
        $compBuilder = $db->table('event_advertiser');
        foreach($adv as $v){
            $compBuilder->where('name', $v['name']);
            //$compBuilder->where('company_seq', null);
            $data = [
                'company_seq' => $v['id'],
            ];
            $compBuilder->update($data);
        }
    }
}
