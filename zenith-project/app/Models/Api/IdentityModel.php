<?php

namespace App\Models\Api;

use CodeIgniter\Shield\Models\UserIdentityModel as ShieldUserIdentityModelModel;

class IdentityModel extends ShieldUserIdentityModelModel
{
    protected $allowedFields  = [
        'user_id',
        'type',
        'name',
        'secret',
        'secret2',
        'expires',
        'extra',
        'force_reset',
        'password_changed_at',
        'last_used_at',
    ];

    public function setPasswordChangedAt($id, $date)
    {
        $this->db->transStart();
        $builder = $this->db->table('auth_identities');
        $builder->set('password_changed_at', $date);
        $builder->where('user_id', $id);
        $builder->where('type', 'email_password');
        $builder->update();
        $result = $this->db->transComplete();
        return $result;
    }
}
