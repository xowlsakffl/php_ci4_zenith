<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'boards';
    protected $primaryKey       = 'bdx';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['board_title', 'board_description'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'board_title' => 'required',
        'board_description' => 'required',
    ];
    protected $validationMessages   = [
        'board_title' => [
            'required' => '제목은 필수 입력사항입니다.',
        ],
        'board_description' => [
            'required' => '본문은 필수 입력사항입니다.',
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

    
}
