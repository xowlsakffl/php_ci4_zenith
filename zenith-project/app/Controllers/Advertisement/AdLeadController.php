<?php

namespace App\Controllers\Advertisement;

use App\Controllers\BaseController;
use App\Models\Api\AdLeadModel;

class AdLeadController extends BaseController
{
    protected $adlead;

    public function __construct()
    {
        $this->adlead = model(AdLeadModel::class); 
    }

    public function sendToEventLead()
    {
        $facebook_ads = $this->adlead->getFBAdLead()->getResultArray();

        foreach($facebook_ads as $row){
            
        }

        //$kakao_ads = $this->adlead->getBizFormUserResponse();


    }
}
