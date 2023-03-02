<?php
namespace App\Models\Api;

use App\Models\BaseModel;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $useTimestaps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
    ];

    // Validation
    protected $validationRules      = [
        'username' => 'required',
        'groups' => 'required',
        'permission' => 'required',
    ];
    protected $validationMessages   = [
        'username' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
        'groups' => [
            'required' => '그룹은 필수 선택사항입니다.',
        ],
        'permission' => [
            'required' => '세부 권한은 필수 선택사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getUser($id){
        $builder = $this->select('u.*, GROUP_CONCAT(DISTINCT agu.group) as groups, GROUP_CONCAT(DISTINCT apu.permission) as permission, c.companyType, c.companyName');

        $builder->from('users as u');
        $builder->join('auth_groups_users as agu', 'u.id = agu.user_id');
        $builder->join('auth_permissions_users as apu', 'u.id = apu.user_id');
        $builder->join('companies_users as cu', 'u.id = cu.user_id');
        $builder->join('companies as c', 'cu.company_id = c.cdx');
        $builder->where('u.id', $id);
        $builder->groupBy('u.id');              
        $result = $builder->get()->getRow();

        return $result;
    }
}