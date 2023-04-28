<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class AdvAllManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $all;
    public function __construct() 
    {
        $this->all = model(AdvAllManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/manage');
    }
}
