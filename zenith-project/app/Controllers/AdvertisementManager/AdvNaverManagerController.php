<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvNaverManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvNaverManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $naver;
    public function __construct() 
    {
        $this->naver = model(AdvNaverManagerModel::class);
    }
    
    public function index()
    {
        return view('advertisements/naver/naver');
    }

    public function getAccounts()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = [
                'dates' => [
                    'sdate' => $this->request->getGet('sdate') ? $this->request->getGet('sdate') : date('Y-m-d'),
                    'edate' => $this->request->getGet('edate') ? $this->request->getGet('edate') : date('Y-m-d'),
                ],
            ];

            $accounts = $this->naver->getAccounts($arg);
            foreach ($accounts as &$account) {
                $account['class'] = [];
                if($account['config'] == 'OFF')
                    array_push($account['class'], 'tag-inactive');
            }

            return $this->respond($accounts);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
