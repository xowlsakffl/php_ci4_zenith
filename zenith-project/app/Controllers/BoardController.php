<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Api\BoardModel;

class BoardController extends BaseController
{
    public function index(){
        return view('boards/board');
    }
}
