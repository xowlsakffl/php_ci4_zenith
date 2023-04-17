<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvFacebookManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvFacebookManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $facebook;
    public function __construct() 
    {
        $this->facebook = model(AdvFacebookManagerModel::class);
    }

    public function index()
    {
        return view('advertisements/facebook/facebook');
    }

    public function getAdAccount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $result = $this->facebook->getAdAccounts();

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
