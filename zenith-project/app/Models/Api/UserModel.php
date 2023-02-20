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

    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
    ];

    public function getUserGroups($id = NULL)
    {
        $builder = $this->db->table('users');
        $builder->select('u.*, GROUP_CONCAT(DISTINCT agu.group) as groups', false);
        $builder->from('users as u');
        $builder->join('auth_groups_users as agu', 'u.id = agu.user_id', 'left');
        if($id){
            $builder->where('u.id', $id);
        }
        $builder->groupBy('u.id');

        $result = $builder->get()->getResult();
        return $result; 
    }
}