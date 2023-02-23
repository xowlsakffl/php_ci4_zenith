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

    /* public function getBoards($getData = NULL){
        $response = array();
        $table_name = 'boards';
        $builder = $this->db->table($table_name);

        $draw = $getData['draw'];
        $start = $getData['start'];// 페이지 첫번째 값
        $rowperpage = $getData['length']; //현재페이지 레코드 수
        $columnIndex = $getData['order'][0]['column']; // 정렬
        $columnName = $getData['columns'][$columnIndex]['data']; // 컬럼의 데이터값
        $columnSortOrder = $getData['order'][0]['dir']; // asc || desc
        $searchValue = $getData['search']['value']; // 검색
        
        ## 검색   
        $searchQuery = '';   
        //전체 게시글 수
        $builder->select('count(*) as allcount');
        $records = $builder->get()->getResult();
        $recordsCount = $builder->countAllResults();

        
        //필터된 게시글 수
        $builder->select('count(*) as allcount');
        if($searchValue != ''){
            $builder->like('board_title', $searchValue);
            $builder->orLike('board_description', $searchValue);
        }
        $filteredRecordsCount = $builder->countAllResults();

        //게시글
        $builder->select('*');
        if($searchValue != ''){
            $builder->like('board_title', $searchValue);
            $builder->orLike('board_description', $searchValue);
        }       
        $builder->orderBy($columnName, $columnSortOrder);
        $builder->limit($rowperpage, $start);
        
        $records = $builder->get()->getResult();

        $data = array();
        foreach($records as $key => $record) {
            $data[] = [
                "bdx" => $record->bdx,
                "board_title" => $record->board_title,
                "board_description" => $record->board_description,
                "created_at" => date("Y-m-d", strtotime($record->created_at)),
            ]; 
        }

        ## Response
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsCount,
            "recordsFiltered" => $filteredRecordsCount,
            "data" => $data
        ];

        return $response;
    } */
}
