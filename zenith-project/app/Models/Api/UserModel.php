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
        $builder = $this->zenith->table('users');
        $builder->select('id, username');
        $builder->limit(10);
        if(!empty($data['stx'])){
            $builder->like('username', $data['stx']);
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