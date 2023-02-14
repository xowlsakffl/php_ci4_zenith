<?php
namespace App\Models\Api;

use CodeIgniter\Model;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $useTimestaps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields  = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
    ];

    public function getUsersGroups()
    {
        $builder = $this->table('users');
        $builder->select('*');
        $builder->join('auth_groups_users', 'users.id = auth_groups_users.user_id');
        return $builder;
    }
}