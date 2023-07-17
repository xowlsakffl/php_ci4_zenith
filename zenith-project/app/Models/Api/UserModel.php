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

    public function getUsers($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('users AS u');
        $builder->select('c.name AS belong, u.id AS user_id, u.username, u.nickname, ai.secret AS email, u.active as active, GROUP_CONCAT(DISTINCT agu.group) as groups, u.created_at');
        $builder->join('companies_users as cu', 'u.id = cu.user_id', 'left');
        $builder->join('companies as c', 'c.id = cu.company_id', 'left');
        $builder->join('auth_identities as ai', 'u.id = ai.user_id', 'left');
        $builder->join('auth_groups_users as agu', 'u.id = agu.user_id', 'left');
        $builder->where('u.deleted_at =', NULL);
        if(!empty($srch['sdate'] && $srch['edate'])){
            $builder->where('DATE(u.created_at) >=', $srch['sdate']);
            $builder->where('DATE(u.created_at) <=', $srch['edate']);
        }

        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('u.username', $srch['stx']);
            $builder->orLike('ai.secret', $srch['stx']);
            $builder->groupEnd();
        }
        $builder->groupBy('u.id');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "created_at DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);

        if($data['length'] > 0){
            $builder->limit($data['length'], $data['start']);
        }
    
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getUser($data)
    {
        $builder = $this->db->table('users AS u');
        $builder->select('cu.company_id AS company_id, c.name AS belong, u.id AS user_id, u.username, u.nickname, up.division, up.team, up.position, ai.secret AS email, u.active, GROUP_CONCAT(DISTINCT agu.group) AS groups, GROUP_CONCAT(DISTINCT apu.permission) AS permissions, u.created_at');
        $builder->join('companies_users as cu', 'u.id = cu.user_id', 'left');
        $builder->join('companies as c', 'c.id = cu.company_id', 'left');
        $builder->join('auth_identities as ai', 'u.id = ai.user_id', 'left');
        $builder->join('auth_groups_users as agu', 'u.id = agu.user_id', 'left');
        $builder->join('auth_permissions_users as apu', 'u.id = apu.user_id', 'left');
        $builder->join('users_department AS up', 'u.id = up.user_id', 'left');
        $builder->where('u.id', $data['user_id']);
        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function getSearchUsers($data)
    {
        $builder = $this->db->table('users');
        $builder->select('id, username');
        if(!empty($data['stx'])){        
            $builder->like('username', $data['stx']);
            $builder->limit(10);
        }
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getBelongUsers($companyId)
    {
        $builder = $this->db->table('users AS u');
        $builder->select('u.id, u.username, u.nickname, u.created_at');
        $builder->join('companies_users as cu', 'u.id = cu.user_id', 'left');
        $builder->where('cu.company_id', $companyId);
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getBelongUser($data)
    {
        $builder = $this->db->table('companies_users');
        $builder->select('company_id, user_id');
        $builder->where('user_id', $data['user_id']);
        $builder->where('company_id', $data['company_id']);
        $result = $builder->get()->getResult();
        return $result;
    }

    public function setUser($data)
    {
        $user = $this->findById($data['user_id']);
        $this->db->transStart();
        if(!empty($data['active'])){
            $builder_1 = $this->db->table('users');
            $builder_1->set('active', $data['active']);
            $builder_1->where('id', $data['user_id']);
            $builder_1->update();
        }

        if(!empty($data['company_id'])){
            $builder_2 = $this->db->table('companies_users');
            $builder_2->where('company_id', $data['company_id']);
            $builder_2->where('user_id', $data['user_id']);
            $result = $builder_2->get()->getResult();
            
            if(empty($result)){
                $newRecord = [
                    'company_id' => $data['company_id'],
                    'user_id' => $data['user_id']
                ];
                $builder_2->insert($newRecord);
            }
            $builder_2->set('company_id', $data['company_id']);
            $builder_2->where('company_id', $data['company_id']);
            $builder_2->where('user_id', $data['user_id']);
            $builder_2->update();
        }

        if(empty($data['company_name'])){
            $builder_2 = $this->db->table('companies_users');
            $builder_2->where('user_id', $data['user_id']);
            $builder_2->delete();
        }

        if(!empty($data['group'])){
            if ($user->inGroup('superadmin')) {
                array_push($data['group'], 'superadmin');
            }
            $user->syncGroups(...$data['group']);
        }
        
        if(!empty($data['permission'])){
            $user->syncPermissions(...$data['permission']);
        }
        
        $result = $this->db->transComplete();

        return $result;
    }

    public function setBelongUser($data)
    {
        $this->db->transStart();
        $builder = $this->db->table('companies_users');
        $newRecord = [
            'company_id' => $data['company_id'],
            'user_id' => $data['user_id'],
        ];
        $builder->insert($newRecord);
        $result = $this->db->transComplete();

        return $result;
    }

    public function exceptBelongUser($data)
    {
        $builder = $this->db->table('companies_users');
        $builder->where('user_id', $data['user_id']);
        $builder->where('company_id', $data['company_id']);
        $result = $builder->delete();

        return $result;
    }

    public function getUserByEmail($email)
    {
        $builder = $this->db->table('users AS u');
        $builder->select('u.id, u.username, u.nickname, ai.secret as email, u.created_at');
        $builder->join('auth_identities as ai', 'u.id = ai.user_id', 'left');
        $builder->where('ai.secret', $email);
        $builder->where('ai.type', 'email_password');
        $result = $builder->get()->getRowArray();

        return $result;
    }
}