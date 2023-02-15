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

    protected $allowedFields = ['user_id', 'user_pw', 'name'];

    public function getUserGroups($id = NULL)
    {
        $sql = "SELECT u.*, GROUP_CONCAT(agu.group) as groups FROM users AS u           
        LEFT OUTER JOIN auth_groups_users agu                  
            ON u.id = agu.user_id";                              

        if($id){
            $sql .= " WHERE u.id = $id";
        }
        $sql .= " GROUP BY u.id";

        $query = $this->db->query($sql);
        $result = $query->getResult();
        return $result;
    }
}