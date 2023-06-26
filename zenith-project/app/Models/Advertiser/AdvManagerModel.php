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
        return $this->getQueryResults($data['searchData'], 'getAccounts');
    }

    public function getCampaigns($data)
    {
        return $this->getQueryResults($data['searchData'], 'getCampaigns');
    }

    public function getAdSets($data)
    {
        return $this->getQueryResults($data['searchData'], 'getAdsets');
    }

    public function getAds($data)
    {
        return $this->getQueryResults($data['searchData'], 'getAds');
    }

    public function getReport($data)
    {
        return $this->getQueryResults($data['searchData'], 'getReport');
    }

    private function getQueryResults($data, $type)
    {
        $builders = [];
        $media = explode("|", $data['media']);
        if (in_array('facebook', $media)) {
            $facebookBuilder = $this->facebook->$type($data);
            $builders[] = $facebookBuilder;
        }

        if (in_array('google', $media)) {
            $googleBuilder = $this->google->$type($data);
            $builders[] = $googleBuilder;
        }

        if (in_array('kakao', $media)) {
            $kakaoBuilder = $this->kakao->$type($data);
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
            $result = $unionBuilder->get()->getResultArray();
        } else {
            $result = null;
        }

        return $result;
    }
}
