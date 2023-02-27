<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'companies';
    protected $primaryKey       = 'cdx';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['companyType', 'companyName', 'companyTel'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'companyType' => 'required',
        'companyName' => 'required',
        'companyTel' => 'required',
    ];
    protected $validationMessages   = [
        'companyType' => [
            'required' => '타입은 필수 입력사항입니다.',
        ],
        'companyName' => [
            'required' => '이름은 필수 입력사항입니다.',
        ],
        'companyTel' => [
            'required' => '전화번호는 필수 입력사항입니다.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function users()
    {
        return $this->hasMany('users', 'App\Models\Api\UserModel');
    }
}
