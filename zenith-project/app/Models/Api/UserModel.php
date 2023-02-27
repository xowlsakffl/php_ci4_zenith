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
        'groups' => 'required'
    ];
    protected $validationMessages   = [
        'username' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
        'groups' => [
            'required' => '그룹은 필수 선택사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


    public function company()
    {
        return $this->belongsTo('companies', 'App\Models\Api\CompanyModel');
    }
}