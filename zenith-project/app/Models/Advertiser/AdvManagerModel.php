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
        return $this->getQueryResults($data, 'getAccounts');
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
        if(!empty($data['searchData']['check'])){
            $kakaoNumbers = array();
            $googleNumbers = array();
            $facebookNumbers = array();

            foreach ($data['searchData']['check'] as $value) {
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
        }

        return $this->getQueryResults($data, 'getReport');
    }

    private function getQueryResults($data, $type)
    {
        $builders = [];
        $media = explode("|", $data['searchData']['media']);
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
            if($type == 'getAccounts'){   
                $unionBuilder->groupBy('G.id');
                $unionBuilder->orderBy('G.id', 'asc');
            }
            
            if($type == 'getCampaigns' || $type == 'getAdsets' || $type == 'getAds'){   
                $unionBuilder->groupBy('G.id');
                $unionBuilder->orderBy('G.id', 'asc');
            }

            $result = $unionBuilder->get()->getResultArray();
        } else {
            $result = null;
        }

        return $result;
    }
}
