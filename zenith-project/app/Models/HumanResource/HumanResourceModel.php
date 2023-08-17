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

    public function getMemberList() {
        $builder = $this->zenith->table('users as usr');
        $builder->select('usr.*, ai.secret, up.*');
        $builder->join('auth_identities as ai', 'usr.id = ai.user_id AND ai.type = "email_password"', 'left');
        $builder->join('users_department as up', 'usr.id = up.user_id');
        $builder->groupBy('usr.id');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getUserByEmail($email) {
        $builder = $this->zenith->table('users as usr');
        $builder->select('usr.*, ai.secret, up.*');
        $builder->join('auth_identities as ai', 'usr.id = ai.user_id AND ai.type = "email_password"', 'left');
        $builder->join('users_department as up', 'usr.id = up.user_id', 'left');
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
        $result = $builder->set('nickname', $name)->where('id', $usr['id'])->update();

        return $result;
    }

    public function getHourTicketUse() {
        $builder = $this->zenith->table('chainsaw_old.hourticket_use AS hu');
        $builder->select('mem.mb_email AS email, mem.mb_name AS name, hu.*');
        $builder->join('chainsaw_old.g5_member AS mem', 'hu.mb_id = mem.mb_id');
        $builder->where('hu.date >= DATE_SUB(NOW(), INTERVAL 2 DAY)');
        $builder->orderBy("hu.seq", "DESC");
        $result = $builder->get()->getResultArray();

        return $result;
    }
}
