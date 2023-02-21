<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Api\BoardModel;
use CodeIgniter\API\ResponseTrait;

class BoardController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->board = model(BoardModel::class);
    }
    
    public function index(){
        return view('boards/board');
    }

    public function getList(){
        $getData = $this->request->getGet();
        $data = $this->board->getBoards($getData);

        return $this->respond($data);
    }
}
