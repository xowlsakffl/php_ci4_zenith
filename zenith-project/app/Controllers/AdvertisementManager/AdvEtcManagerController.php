<?php

namespace App\Controllers\AdvertisementManager;

use App\Controllers\BaseController;
use App\Models\Advertiser\AdvEtcManagerModel;
use CodeIgniter\API\ResponseTrait;

class AdvEtcManagerController extends BaseController
{
    use ResponseTrait;
    
    protected $etcmanager;
    public function __construct() 
    {
        $this->etcmanager = model(AdvEtcManagerModel::class);
    }

    public function index()
    {
        return view('advertisements/etc/etc');
    }

    public function getData(){
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            /* $arg = $this->request->getGet();
            if(!empty($arg['searchData']['media']) || 
            !empty($arg['searchData']['advertiser']) || 
            !empty($arg['searchData']['group']) ||
            !empty($arg['searchData']['description'])) {

                $result = $this->getCampaigns($arg);
				$orderBy = [];
				if(!empty($arg['order'])) {
					foreach($arg['order'] as $row) {
						if($row['dir'] == 'desc'){
							$sort = SORT_DESC;
						}else{
							$sort = SORT_ASC;
						}
						$col = $arg['columns'][$row['column']]['data'];
						if($col) $orderBy[$col] = $sort;
					}
					array_sort_by_multiple_keys($result['data'], $orderBy);
				}

				foreach ($result['data'] as &$value) {
                    $value['campaign_bidamount'] = number_format($value['campaign_bidamount'] ?? 0);
                    $value['bidamount'] = number_format($value['bidamount']);
					$value['budget'] = number_format($value['budget']);
					$value['impressions'] = number_format($value['impressions']);
					$value['click'] = number_format($value['click']);
					$value['spend'] = number_format($value['spend']);
					$value['sales'] = number_format($value['sales']);
					$value['unique_total'] = number_format($value['unique_total']);
					$value['margin'] = number_format($value['margin']);
					$value['margin_ratio'] = number_format($value['margin_ratio']);
					$value['cpa'] = number_format($value['cpa']);
					$value['cpc'] = number_format($value['cpc']);
				}
				if(isset($arg['noLimit'])) {
					return $this->respond($result['data']);
				}
            }
            $result['report'] = $this->getReport($arg);
            $result['accounts'] = $this->getAccounts($arg);
            $result['media_accounts'] = $this->getMediaAccounts($arg);

            return $this->respond($result); */
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    
}
