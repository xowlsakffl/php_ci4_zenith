<?php
namespace App\Models\Api;

use App\Models\BaseModel;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $zenith;
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

    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getUsers($data)
    {
        $srch = $data['searchData'];
        $builder = $this->zenith->table('users AS u');
        $builder->select('c.name AS belong, u.id AS user_id, u.username, ai.secret AS email, u.status, agu.group, u.created_at');
        $builder->join('companies_users as cu', 'u.id = cu.user_id', 'left');
        $builder->join('companies as c', 'c.id = cu.company_id', 'left');
        $builder->join('auth_identities as ai', 'u.id = ai.user_id', 'left');
        $builder->join('auth_groups_users as agu', 'u.id = agu.user_id', 'left');
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
        
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        // limit 적용한 쿼리
        $builder->groupBy('u.id');
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

    public function getSearchUsers($data)
    {
        $builder = $this->zenith->table('users');
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
        $builder = $this->zenith->table('users AS u');
        $builder->select('u.id, u.username, u.created_at');
        $builder->join('companies_users as cu', 'u.id = cu.user_id', 'left');
        $builder->where('cu.company_id', $companyId);
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getBelongUser($data)
    {
        $builder = $this->zenith->table('companies_users');
        $builder->select('company_id, user_id');
        $builder->where('user_id', $data['user_id']);
        $builder->where('company_id', $data['company_id']);
        $result = $builder->get()->getResult();
        return $result;
    }

    public function setBelongUser($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('companies_users');
        $newRecord = [
            'company_id' => $data['company_id'],
            'user_id' => $data['user_id'],
        ];
        $builder->insert($newRecord);
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function exceptBelongUser($data)
    {
        $builder = $this->zenith->table('companies_users');
        $builder->where('user_id', $data['user_id']);
        $builder->where('company_id', $data['company_id']);
        $result = $builder->delete();

        return $result;
    }
}