<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\BlackListModel;
use CodeIgniter\API\ResponseTrait;

class BlackListController extends BaseController
{
    use ResponseTrait;
    
    protected $blacklist;
    public function __construct() 
    {
        $this->blacklist = model(BlackListModel::class);
    }
    
    public function index()
    {
        return view('/events/blacklist/blacklist');
    }
}
