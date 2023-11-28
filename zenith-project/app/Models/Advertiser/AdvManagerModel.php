<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvManagerModel extends Model
{
    protected $zenith, $facebook, $google, $kakao;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
		$this->facebook = model(AdvFacebookManagerModel::class);
        $this->google = model(AdvGoogleManagerModel::class);
        $this->kakao = model(AdvKakaoManagerModel::class);
    }

    public function getAccounts($data)
    {
        if(!empty($data['check'])){
            $data = $this->setArgs($data);
        }
        
        return $this->getQueryResults($data, 'getAccounts');
    }

    public function getMediaAccounts($data)
    {
        if(!empty($data['check'])){
            $data = $this->setArgs($data);
        }
        
        return $this->getQueryResults($data, 'getMediaAccounts');
    }

    public function getCampaigns($data)
    {
        return $this->getQueryResults($data, 'getCampaigns');
    }

    public function getAdSets($data)
    {
        return $this->getQueryResults($data, 'getAdsets');
    }

    public function getAds($data)
    {
        return $this->getQueryResults($data, 'getAds');
    }

    public function getReport($data)
    {
        if(!empty($data['check'])){
            $data = $this->setArgs($data);
        }

        return $this->getQueryResults($data, 'getReport');
    }

    private function setArgs($data)
    {
        $kakaoNumbers = array();
        $googleNumbers = array();
        $facebookNumbers = array();

        foreach ($data['check'] as $value) {
            $parts = explode('_', $value);
            $id = $parts[1];
            
            switch ($parts[0]) {
                case 'kakao':
                    $kakaoNumbers[] = $id;
                    break;
                case 'google':
                    $googleNumbers[] = $id;
                    break;
                case 'facebook':
                    $facebookNumbers[] = $id;
                    break;
                default:
                    break;
            }
        }

        $data['searchData']['facebookCheck'] = $facebookNumbers;
        $data['searchData']['googleCheck'] = $googleNumbers;
        $data['searchData']['kakaoCheck'] = $kakaoNumbers;
        unset($facebookNumbers, $googleNumbers, $kakaoNumbers, $data['searchData']['media']);
        $data['searchData']['media'] = [];

        if(!empty($data['searchData']['facebookCheck'])){
            array_push($data['searchData']['media'], 'facebook');
        }
        
        if(!empty($data['searchData']['googleCheck'])){
            array_push($data['searchData']['media'], 'google');
        }
        
        if(!empty($data['searchData']['kakaoCheck'])){
            array_push($data['searchData']['media'], 'kakao');
        }

        $data['searchData']['media'] = implode("|", $data['searchData']['media']);
        return $data;
    }

    private function getQueryResults($data, $type)
    {
        $builders = [];
        $media = explode("|", $data['searchData']['media']);
        if(empty($data['searchData']['media'])){
            $media = ['facebook', 'google', 'kakao'];
        }

        if (in_array('facebook', $media)) {
            $facebookBuilder = $this->facebook->$type($data['searchData']);
            $builders[] = $facebookBuilder;
        }

        if (in_array('google', $media)) {
            $googleBuilder = $this->google->$type($data['searchData']);
            $builders[] = $googleBuilder;
        }

        if (in_array('kakao', $media)) {
            $kakaoBuilder = $this->kakao->$type($data['searchData']);
            $builders[] = $kakaoBuilder;
        }

        $unionBuilder = null;
        foreach ($builders as $builder) {
            if ($unionBuilder) {
                $unionBuilder->union($builder);
                
            } else {
                $unionBuilder = $builder;
            }
        }

        if ($unionBuilder) {
            $resultQuery = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
            if($type == 'getAccounts'){   
                $resultQuery->groupBy('adv.company_id');
                $resultQuery->orderBy('adv.company_name', 'asc');
            }

            if($type == 'getMediaAccounts'){   
                $resultQuery->groupBy('adv.media_account_id');
                $resultQuery->orderBy('adv.media_account_name', 'asc');
            }
            
            if($type == 'getAdsets' || $type == 'getAds') {
                $ids = [];
                if($type == 'getAdsets' && (isset($data['searchData']['data']['campaigns']) && count($data['searchData']['data']['campaigns']))) {
                    $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['campaigns']);                 
                    $resultQuery->whereIn('campaign_id', $ids);
                } else if($type == 'getAds') {
                    if(isset($data['searchData']['data']['campaigns']) && count($data['searchData']['data']['campaigns'])) {
                        $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['campaigns']);
                        $resultQuery->whereIn('campaign_id', $ids);
                    }
                    if(isset($data['searchData']['data']['adsets']) && count($data['searchData']['data']['adsets'])) {
                        $ids = array_map(function($v) { $v=explode('_', $v); return (integer)array_pop($v); }, $data['searchData']['data']['adsets']);
                        $resultQuery->whereIn('adset_id', $ids);
                    }
                }
            }
            /* if($type == 'getCampaigns' || $type == 'getAdsets' || $type == 'getAds'){   
                $resultQuery->groupBy('id');
                $resultQuery->orderBy('id', 'asc');
                $result = $resultQuery->get()->getResultArray();
                dd($result);
            } */
            $result = $resultQuery->get()->getResultArray();
        } else {
            $result = null;
        }

        return $result;
    }

    public function getMemo()
    {
        $fbBuilder = $this->zenith->table('z_facebook.fb_ad_account AS faa');
        $fbBuilder->select('am.*, faa.name AS account_name, fc.campaign_name AS campaign_name, fas.adset_name AS adset_name, fa.ad_name AS ad_name');   
        $fbBuilder->join('z_facebook.fb_campaign AS fc', 'faa.ad_account_id = fc.account_id');
        $fbBuilder->join('z_facebook.fb_adset AS fas', 'fc.campaign_id = fas.campaign_id');
        $fbBuilder->join('z_facebook.fb_ad AS fa', 'fas.adset_id = fa.adset_id');
        $fbBuilder->join('zenith.advertisement_memo AS am', "am.media = 'facebook' AND ((fa.ad_id = am.id AND am.type = 'ads') OR (fas.adset_id = am.id AND am.type = 'adsets') OR (fc.campaign_id = am.id AND am.type = 'campaigns'))");

        $ggBuilder = $this->zenith->table('z_adwords.aw_ad_account AS aaa');
        $ggBuilder->select('am.*, aaa.name AS account_name, ac.name AS campaign_name, aag.name AS adset_name, aa.name AS ad_name');
        $ggBuilder->join('z_adwords.aw_campaign ac', 'aaa.customerId = ac.customerId');
        $ggBuilder->join('z_adwords.aw_adgroup aag', 'ac.id = aag.campaignId');
        $ggBuilder->join('z_adwords.aw_ad aa', 'aag.id = aa.adgroupId');
        $ggBuilder->join('zenith.advertisement_memo AS am', "am.media = 'google' AND ((aa.id = am.id AND am.type = 'ads') OR (aag.id = am.id AND am.type = 'adsets') OR (ac.id = am.id AND am.type = 'campaigns'))");

        $kkBuilder = $this->zenith->table('z_moment.mm_ad_account AS maa');
        $kkBuilder->select('am.*, maa.name AS account_name, mc.name AS campaign_name, mag.name AS adset_name, mct.name AS ad_name');
        $kkBuilder->join('z_moment.mm_campaign mc', 'maa.id = mc.ad_account_id');
        $kkBuilder->join('z_moment.mm_adgroup mag', 'mc.id = mag.campaign_id');
        $kkBuilder->join('z_moment.mm_creative mct', 'mag.id = mct.adgroup_id');
        $kkBuilder->join('zenith.advertisement_memo AS am', "am.media = 'kakao' AND ((mct.id = am.id AND am.type = 'ads') OR (mag.id = am.id AND am.type = 'adsets') OR (mc.id = am.id AND am.type = 'campaigns'))");

        $fbBuilder->union($ggBuilder)->union($kkBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($fbBuilder, 'memo');
        $resultQuery->where("(memo.datetime >= DATE_SUB(NOW(), INTERVAL 3 DAY) OR (memo.datetime <= DATE_SUB(NOW(), INTERVAL 3 DAY) AND memo.is_done = 0))");
        $resultQuery->groupBy('memo.seq');
        $resultQuery->orderBy('memo.datetime', 'desc');
        $result = $resultQuery->get()->getResultArray();
        return $result;
    }

    public function addMemo($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('advertisement_memo');
        $builder->insert($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function checkMemo($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('advertisement_memo');
        $builder->set('is_done', $data['is_done']);
        $builder->set('done_nickname', $data['done_nickname']);
        $builder->where('seq', $data['seq']);
        $builder->update();
        $result = $this->zenith->transComplete();
        return $result;
    }
}
