<?php

namespace App\Models\HumanResource;

use CodeIgniter\Model;

class HumanResourceModel extends Model
{
    protected $zenith;

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getUserByEmail($email) {
        $builder = $this->zenith->table('users as usr');
        $builder->select('usr.*, ai.secret');
        $builder->join('auth_identities as ai', 'usr.id = ai.user_id AND ai.type = "email_password"', 'left');
        $builder->where('ai.secret', $email);
        $builder->groupBy('usr.id');
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function updateUserByEmail($data) {
        $usr = $this->getUserByEmail($data['email']);
        if(is_null($usr)) return;
        $name = $data['name'];
        $data = [
            'user_id' => $usr['id'],
            'division' => $data['division'],
            'team' => $data['team'],
            'position' => $data['position']
        ];
        $builder = $this->zenith->table('users_department');
        $builder->setData($data)->upsert();
        $builder = $this->zenith->table('users');
        $builder->set('nickname', $name)->where('id', $usr['id'])->update();
    }
}
