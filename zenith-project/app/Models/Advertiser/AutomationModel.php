<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AutomationModel extends Model
{
    protected $zenith, $test,$t;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function getAutomationList($data)
    {
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('
        aa.seq as aa_seq, 
        aa.subject as aa_subject,
        aa.nickname as aa_nickname,
        aa.status as aa_status,
        aa.mod_datetime as aa_mod_datetime, 
        aas.schedule_value as aas_schedule_value,
        aas.exec_once as aas_exec_once,
        (SELECT MAX(aar.exec_timestamp) 
        FROM aa_result aar 
        WHERE aar.idx = aa.seq AND aar.result = "success") as aar_exec_timestamp_success,
        ');
        
        $builder->join('aa_schedule_new aas', 'aas.idx = aa.seq', 'left');
        //$builder->join('aa_result aar', 'aar.idx = aa.seq', 'left');

        if(!empty($data['searchData']['sdate']) && !empty($data['searchData']['edate'])){
            $builder->where('DATE(aa.mod_datetime) >=', $data['searchData']['sdate']);
            $builder->where('DATE(aa.mod_datetime) <=', $data['searchData']['edate']);
        }

        if(!empty($data['searchData']['stx'])){
            $builder->groupStart();
            $builder->like('aa.subject', $data['searchData']['stx']);
            $builder->orLike('aa.description', $data['searchData']['stx']);
            $builder->groupEnd();
        }

        $builder->groupBy('aa.seq');
        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "aa.seq DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);

        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getAutomationBySeq($seq)
    {
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('
        aa.seq as aa_seq, 
        aa.slack_webhook as aa_slack_webhook,
        aa.slack_msg as aa_slack_msg,
        ');
        $builder->where('aa.seq', $seq);
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getSearchCompanies($data)
    {
        $builder = $this->zenith->table('companies A');
        $builder->select('A.id, "광고주" AS media, A.type, A.name, A.status AS status');
        $builder->join('company_adaccounts AS B', 'A.id = B.company_id');
        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('A.name', $data['stx']);
            $builder->orLike('A.id', $data['stx']);
            $builder->groupEnd();
        }
        
        $builder->groupBy('A.id');

        $builderNoLimit = clone $builder;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchCompaniesById($id)
    {
        $builder = $this->zenith->table('companies A');
        $builder->select('A.id, "광고주" AS media, A.type, A.name, A.status AS status');
        $builder->where('A.id', $id);

        $result = $builder->get()->getRowArray();
        return $result;
        
    }

    public function getSearchCompaniesWithAdv($data)
    {
        if(empty($data['adv'])){return false;}

        list($media, $type, $id) = explode("_", $data['adv'][0]);

        $builder = $this->zenith->table('companies A');
        $builder->select('A.id, "광고주" AS media, A.type, A.name, A.status AS status');
        if($media == '광고주'){
            $builder->where('A.id', $id);
        }else{
            $builder->join('company_adaccounts AS B', 'A.id = B.company_id');
            if($media == '페이스북'){
                $builder->join('z_facebook.fb_ad_account AS C', 'B.ad_account_id = C.ad_account_id', 'left');
                $builder->join('z_facebook.fb_campaign AS D', 'C.ad_account_id = D.account_id', 'left');
                $builder->join('z_facebook.fb_adset AS E', 'D.campaign_id = E.campaign_id', 'left');
                $builder->join('z_facebook.fb_ad AS F', 'E.adset_id = F.adset_id', 'left');
                if($type == '매체광고주'){
                    $builder->where('C.ad_account_id', $id);
                }else if($type == '캠페인'){
                    $builder->where('D.campaign_id', $id);
                }else if($type == '광고그룹'){
                    $builder->where('E.adset_id', $id);
                }else if($type == '광고'){
                    $builder->where('F.ad_id', $id);
                }
            }
            
            if($media == '구글'){
                $builder->join('z_adwords.aw_ad_account AS C', 'B.ad_account_id = C.customerId', 'left');
                $builder->join('z_adwords.aw_campaign AS D', 'C.customerId = D.customerId', 'left');
                $builder->join('z_adwords.aw_adgroup AS E', 'D.id = E.campaignId', 'left');
                $builder->join('z_adwords.aw_ad AS F', 'E.id = F.adgroupId', 'left');
                if($type == '매체광고주'){
                    $builder->where('C.customerId', $id);
                }else if($type == '캠페인'){
                    $builder->where('D.id', $id);
                }else if($type == '광고그룹'){
                    $builder->where('E.id', $id);
                }else if($type == '광고'){
                    $builder->where('F.id', $id);
                }
            }

            if($media == '카카오'){
                $builder->join('z_moment.mm_ad_account AS C', 'B.ad_account_id = C.id', 'left');
                $builder->join('z_moment.mm_campaign AS D', 'C.id = D.ad_account_id', 'left');
                $builder->join('z_moment.mm_adgroup AS E', 'D.id = E.campaign_id', 'left');
                $builder->join('z_moment.mm_creative AS F', 'E.id = F.adgroup_id', 'left');
                if($type == '매체광고주'){
                    $builder->where('C.id', $id);
                }else if($type == '캠페인'){
                    $builder->where('D.id', $id);
                }else if($type == '광고그룹'){
                    $builder->where('E.id', $id);
                }else if($type == '광고'){
                    $builder->where('F.id', $id);
                }
            }
        }

        $builder->groupBy('A.id');

        $builderNoLimit = clone $builder;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAccounts($data)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad_account A');
        $facebookBuilder->select('A.ad_account_id AS id, "페이스북" AS media, "매체광고주" AS type, A.name AS name, A.status AS status');
        
        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.name', $data['stx']);
            $facebookBuilder->orLike('A.ad_account_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_ad_account A');
        $googleBuilder->select('A.customerId as id, "구글" AS media, "매체광고주" AS type, A.name, A.status AS status');
        
        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.customerId', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_ad_account A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "매체광고주" AS type, A.name, A.config AS status');
        

        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAccountsById($id)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad_account A');
        $facebookBuilder->select('A.ad_account_id AS id, "페이스북" AS media, "매체광고주" AS type, A.name AS name, A.status AS status');
        $facebookBuilder->where('A.ad_account_id', $id);

        $googleBuilder = $this->zenith->table('z_adwords.aw_ad_account A');
        $googleBuilder->select('A.customerId as id, "구글" AS media, "매체광고주" AS type, A.name, A.status AS status');
        $googleBuilder->where('A.customerId', $id);
        
        $kakaoBuilder = $this->zenith->table('z_moment.mm_ad_account A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "매체광고주" AS type, A.name, A.config AS status');
        $kakaoBuilder->where('A.id', $id);

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $result = $resultQuery->get()->getRowArray();
        return $result;
        
    }

    public function getSearchAccountsWithAdv($data)
    {
        if(!empty($data['adv'])){
            list($media, $type, $id) = explode("_", $data['adv'][0]);
        }else{
            return false;
        }

        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad_account A');
        $facebookBuilder->select('A.ad_account_id AS id, "페이스북" AS media, "매체광고주" AS type, A.name AS name, A.status AS status');
        if($type == '광고주'){
            $facebookBuilder->join('zenith.company_adaccounts AS E', 'A.ad_account_id = E.ad_account_id', 'left');
            $facebookBuilder->where('E.company_id', $id);
        }else{
            $facebookBuilder->join('z_facebook.fb_campaign AS B', 'A.ad_account_id = B.account_id', 'left');
            $facebookBuilder->join('z_facebook.fb_adset AS C', 'B.campaign_id = C.campaign_id', 'left');
            $facebookBuilder->join('z_facebook.fb_ad AS D', 'C.adset_id = D.adset_id', 'left');

            if($media == '페이스북'){
                if($type == '매체광고주'){
                    $facebookBuilder->where('A.ad_account_id', $id);
                }else if($type == '캠페인'){
                    $facebookBuilder->where('B.campaign_id', $id);
                }else if($type == '광고그룹'){
                    $facebookBuilder->where('C.adset_id', $id);
                }else if($type == '광고'){
                    $facebookBuilder->where('D.ad_id', $id);
                }
            }else{
                $facebookBuilder->where('1 = 2');
            }
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_ad_account A');
        $googleBuilder->select('A.customerId as id, "구글" AS media, "매체광고주" AS type, A.name, A.status AS status');
        if($type == '광고주'){
            $googleBuilder->join('zenith.company_adaccounts AS E', 'A.customerId = E.ad_account_id', 'left');
            $googleBuilder->where('E.company_id', $id);
        }else{
            $googleBuilder->join('z_adwords.aw_campaign AS B', 'A.customerId = B.customerId', 'left');
            $googleBuilder->join('z_adwords.aw_adgroup AS C', 'B.id = C.campaignId', 'left');
            $googleBuilder->join('z_adwords.aw_ad AS D', 'C.id = D.adgroupId', 'left');

            if($media == '구글'){
                if($type == '매체광고주'){
                    $googleBuilder->where('A.customerId', $id);
                }else if($type == '캠페인'){
                    $googleBuilder->where('B.id', $id);
                }else if($type == '광고그룹'){
                    $googleBuilder->where('C.id', $id);
                }else if($type == '광고'){
                    $googleBuilder->where('D.id', $id);
                }
            }else{
                $googleBuilder->where('1 = 2');
            }
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_ad_account A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "매체광고주" AS type, A.name, A.config AS status');
        if($type == '광고주'){
            $kakaoBuilder->join('zenith.company_adaccounts AS E', 'A.id = E.ad_account_id', 'left');
            $kakaoBuilder->where('E.company_id', $id);
        }else{
            $kakaoBuilder->join('z_moment.mm_campaign AS B', 'A.id = B.ad_account_id', 'left');
            $kakaoBuilder->join('z_moment.mm_adgroup AS C', 'B.id = C.campaign_id', 'left');
            $kakaoBuilder->join('z_moment.mm_creative AS D', 'C.id = D.adgroup_id', 'left');

            if($media == '카카오'){
                if($type == '매체광고주'){
                    $kakaoBuilder->where('A.id', $id);
                }else if($type == '캠페인'){
                    $kakaoBuilder->where('B.id', $id);
                }else if($type == '광고그룹'){
                    $kakaoBuilder->where('C.id', $id);
                }else if($type == '광고'){
                    $kakaoBuilder->where('D.id', $id);
                }
            }else{
                $kakaoBuilder->where('1 = 2');
            }
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchCampaigns($data)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_campaign A');
        $facebookBuilder->select('A.campaign_id AS id, "페이스북" AS media, "캠페인" AS type, A.campaign_name AS name, A.status AS status');
        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.campaign_name', $data['stx']);
            $facebookBuilder->orLike('A.campaign_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_campaign A');
        $googleBuilder->select('A.id, "구글" AS media, "캠페인" AS type, A.name, A.status AS status');
        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_campaign A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "캠페인" AS type, A.name, A.config AS status');
        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchCampaignsById($id)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_campaign A');
        $facebookBuilder->select('A.campaign_id AS id, "페이스북" AS media, "캠페인" AS type, A.campaign_name AS name, A.status AS status');
        $facebookBuilder->where('A.campaign_id', $id);
        
        $googleBuilder = $this->zenith->table('z_adwords.aw_campaign A');
        $googleBuilder->select('A.id, "구글" AS media, "캠페인" AS type, A.name, A.status AS status');
        $googleBuilder->where('A.id', $id);

        $kakaoBuilder = $this->zenith->table('z_moment.mm_campaign A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "캠페인" AS type, A.name, A.config AS status');
        $kakaoBuilder->where('A.id', $id);

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $result = $resultQuery->get()->getRowArray();
        return $result;
    }

    public function getSearchCampaignsWithAdv($data)
    {
        $unionBuilder = null;
        foreach ($data['adv'] as $adv) {
            list($media, $type, $id) = explode("_", $adv);
            $facebookBuilder = $this->zenith->table('z_facebook.fb_campaign A');
            $facebookBuilder->select('A.campaign_id AS id, "페이스북" AS media, "캠페인" AS type, A.campaign_name AS name, A.status AS status');
            
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'A.account_id = D.ad_account_id', 'left');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id', 'left');
                $facebookBuilder->where('E.company_id', $id);
            }else{
                $facebookBuilder->join('z_facebook.fb_ad_account AS F', 'A.account_id = F.ad_account_id', 'left');
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.campaign_id = B.campaign_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad AS C', 'B.adset_id = C.adset_id', 'left');

                if($media == '페이스북'){
                    if($type == '매체광고주'){
                        $facebookBuilder->where('F.ad_account_id', $id);
                    }else if($type == '캠페인'){
                        $facebookBuilder->where('A.campaign_id', $id);
                    }else if($type == '광고그룹'){
                        $facebookBuilder->where('B.adset_id', $id);
                    }else if($type == '광고'){
                        $facebookBuilder->where('C.ad_id', $id);
                    }
                }else{
                    $facebookBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $facebookBuilder->groupStart();
                $facebookBuilder->like('A.campaign_name', $data['stx']);
                $facebookBuilder->orLike('A.campaign_id', $data['stx']);
                $facebookBuilder->groupEnd();
            }

            $googleBuilder = $this->zenith->table('z_adwords.aw_campaign A');
            $googleBuilder->select('A.id, "구글" AS media, "캠페인" AS type, A.name, A.status AS status');
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'A.customerId = D.customerId', 'left');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id', 'left');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_ad_account AS F', 'A.customerId = F.customerId', 'left');
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.id = B.campaignId', 'left');
                $googleBuilder->join('z_adwords.aw_ad AS C', 'B.id = C.adgroupId', 'left');

                if($media == '구글'){
                    if($type == '매체광고주'){
                        $googleBuilder->where('F.customerId', $id);
                    }else if($type == '캠페인'){
                        $googleBuilder->where('A.id', $id);
                    }else if($type == '광고그룹'){
                        $googleBuilder->where('B.id', $id);
                    }else if($type == '광고'){
                        $googleBuilder->where('C.id', $id);
                    }
                }else{
                    $googleBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $googleBuilder->groupStart();
                $googleBuilder->like('A.name', $data['stx']);
                $googleBuilder->orLike('A.id', $data['stx']);
                $googleBuilder->groupEnd();
            }

            $kakaoBuilder = $this->zenith->table('z_moment.mm_campaign A');
            $kakaoBuilder->select('A.id, "카카오" AS media, "캠페인" AS type, A.name, A.config AS status');
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'A.ad_account_id = D.id', 'left');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id', 'left');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_ad_account AS F', 'A.ad_account_id = F.id', 'left');
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.id = B.campaign_id', 'left');
                $kakaoBuilder->join('z_moment.mm_creative AS C', 'B.id = C.adgroup_id', 'left');

                if($media == '카카오'){
                    if($type == '매체광고주'){
                        $kakaoBuilder->where('F.id', $id);
                    }else if($type == '캠페인'){
                        $kakaoBuilder->where('A.id', $id);
                    }else if($type == '광고그룹'){
                        $kakaoBuilder->where('B.id', $id);
                    }else if($type == '광고'){
                        $kakaoBuilder->where('C.id', $id);
                    }
                }else{
                    $kakaoBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $kakaoBuilder->groupStart();
                $kakaoBuilder->like('A.name', $data['stx']);
                $kakaoBuilder->orLike('A.id', $data['stx']);
                $kakaoBuilder->groupEnd();
            }
            
            if ($unionBuilder === null) {
                $unionBuilder = $facebookBuilder;
            } else {
                $unionBuilder = $unionBuilder->union($facebookBuilder);
            }
        
            $unionBuilder = $unionBuilder->union($googleBuilder);
            $unionBuilder = $unionBuilder->union($kakaoBuilder);
        }

        $resultQuery = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
        $resultQuery->groupBy('adv.id');
        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAdsets($data)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_adset A');
        $facebookBuilder->select('A.adset_id AS id, "페이스북" AS media, "광고그룹" AS type, A.adset_name AS name, A.status AS status');
        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.adset_name', $data['stx']);
            $facebookBuilder->orLike('A.adset_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_adgroup A');
        $googleBuilder->select('A.id, "구글" AS media, "광고그룹" AS type, A.name, A.status AS status');
        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_adgroup A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고그룹" AS type, A.name, A.config AS status');
        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAdsetsById($id)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_adset A');
        $facebookBuilder->select('A.adset_id AS id, "페이스북" AS media, "광고그룹" AS type, A.adset_name AS name, A.status AS status');
        $facebookBuilder->where('A.adset_id', $id);

        $googleBuilder = $this->zenith->table('z_adwords.aw_adgroup A');
        $googleBuilder->select('A.id, "구글" AS media, "광고그룹" AS type, A.name, A.status AS status');
        $googleBuilder->where('A.id', $id);
        

        $kakaoBuilder = $this->zenith->table('z_moment.mm_adgroup A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고그룹" AS type, A.name, A.config AS status');
        $kakaoBuilder->where('A.id', $id);

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $result = $resultQuery->get()->getRowArray();
        return $result;
    }

    public function getSearchAdsetsWithAdv($data)
    {
        $unionBuilder = null;
        foreach ($data['adv'] as $adv) {
            list($media, $type, $id) = explode("_", $adv);

            $facebookBuilder = $this->zenith->table('z_facebook.fb_adset A');
            $facebookBuilder->select('A.adset_id AS id, "페이스북" AS media, "광고그룹" AS type, A.adset_name AS name, A.status AS status');
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_campaign AS B', 'A.campaign_id = B.campaign_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'B.account_id = D.ad_account_id', 'left');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id', 'left');
                $facebookBuilder->where('E.company_id', $id);
            }else{              
                $facebookBuilder->join('z_facebook.fb_campaign AS B', 'A.campaign_id = B.campaign_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'B.account_id = D.ad_account_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad AS C', 'A.adset_id = C.adset_id', 'left');

                if($media == '페이스북'){
                    if($type == '매체광고주'){
                        $facebookBuilder->where('D.ad_account_id', $id);
                    }else if($type == '캠페인'){
                        $facebookBuilder->where('B.campaign_id', $id);
                    }else if($type == '광고그룹'){
                        $facebookBuilder->where('A.adset_id', $id);
                    }else if($type == '광고'){
                        $facebookBuilder->where('C.ad_id', $id);
                    }
                }else{
                    $facebookBuilder->where('1 = 2');
                }
            }
            
            if(!empty($data['stx'])){
                $facebookBuilder->groupStart();
                $facebookBuilder->like('A.adset_name', $data['stx']);
                $facebookBuilder->orLike('A.adset_id', $data['stx']);
                $facebookBuilder->groupEnd();
            }

            $googleBuilder = $this->zenith->table('z_adwords.aw_adgroup A');
            $googleBuilder->select('A.id, "구글" AS media, "광고그룹" AS type, A.name, A.status AS status');
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_campaign AS B', 'A.campaignId = B.id', 'left');
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'B.customerId = D.customerId', 'left');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id', 'left');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_campaign AS B', 'A.campaignId = B.id', 'left');
                $googleBuilder->join('z_adwords.aw_ad_account AS F', 'B.customerId = F.customerId', 'left');
                $googleBuilder->join('z_adwords.aw_ad AS C', 'A.id = C.adgroupId', 'left');

                if($media == '구글'){
                    if($type == '매체광고주'){
                        $googleBuilder->where('F.customerId', $id);
                    }else if($type == '캠페인'){
                        $googleBuilder->where('B.id', $id);
                    }else if($type == '광고그룹'){
                        $googleBuilder->where('A.id', $id);
                    }else if($type == '광고'){
                        $googleBuilder->where('C.id', $id);
                    }
                }else{
                    $googleBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $googleBuilder->groupStart();
                $googleBuilder->like('A.name', $data['stx']);
                $googleBuilder->orLike('A.id', $data['stx']);
                $googleBuilder->groupEnd();
            }

            $kakaoBuilder = $this->zenith->table('z_moment.mm_adgroup A');
            $kakaoBuilder->select('A.id, "카카오" AS media, "광고그룹" AS type, A.name, A.config AS status');
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_campaign AS B', 'A.campaign_id = B.id', 'left');
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'B.ad_account_id = D.id', 'left');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id', 'left');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_campaign AS B', 'A.campaign_id = B.id', 'left');
                $kakaoBuilder->join('z_moment.mm_ad_account AS F', 'B.ad_account_id = F.id', 'left');
                $kakaoBuilder->join('z_moment.mm_creative AS C', 'A.id = C.adgroup_id', 'left');

                if($media == '카카오'){
                    if($type == '매체광고주'){
                        $kakaoBuilder->where('F.id', $id);
                    }else if($type == '캠페인'){
                        $kakaoBuilder->where('B.id', $id);
                    }else if($type == '광고그룹'){
                        $kakaoBuilder->where('A.id', $id);
                    }else if($type == '광고'){
                        $kakaoBuilder->where('C.id', $id);
                    }
                }else{
                    $kakaoBuilder->where('1 = 2');
                }
            }
            
            if(!empty($data['stx'])){
                $kakaoBuilder->groupStart();
                $kakaoBuilder->like('A.name', $data['stx']);
                $kakaoBuilder->orLike('A.id', $data['stx']);
                $kakaoBuilder->groupEnd();
            }

            if ($unionBuilder === null) {
                $unionBuilder = $facebookBuilder;
            } else {
                $unionBuilder = $unionBuilder->union($facebookBuilder);
            }
        
            $unionBuilder = $unionBuilder->union($googleBuilder);
            $unionBuilder = $unionBuilder->union($kakaoBuilder);
        }

        $resultQuery = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAds($data)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad A');
        $facebookBuilder->select('A.ad_id AS id, "페이스북" AS media, "광고" AS type, A.ad_name AS name, A.status AS status');

        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.ad_name', $data['stx']);
            $facebookBuilder->orLike('A.ad_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_ad A');
        $googleBuilder->select('A.id, "구글" AS media, "광고" AS type, A.name, A.status AS status');

        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_creative A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고" AS type, A.name, A.config AS status');

        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAdsById($id)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad A');
        $facebookBuilder->select('A.ad_id AS id, "페이스북" AS media, "광고" AS type, A.ad_name AS name, A.status AS status');
        $facebookBuilder->where('A.ad_id', $id);
        
        $googleBuilder = $this->zenith->table('z_adwords.aw_ad A');
        $googleBuilder->select('A.id, "구글" AS media, "광고" AS type, A.name, A.status AS status');
        $googleBuilder->where('A.id', $id);

        $kakaoBuilder = $this->zenith->table('z_moment.mm_creative A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고" AS type, A.name, A.config AS status');
        $kakaoBuilder->where('A.id', $id);

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        $result = $resultQuery->get()->getRowArray();
        return $result;
    }

    public function getSearchAdsWithAdv($data)
    {
        $unionBuilder = null;
        foreach ($data['adv'] as $adv) {
            list($media, $type, $id) = explode("_", $adv);
            $facebookBuilder = $this->zenith->table('z_facebook.fb_ad A');
            $facebookBuilder->select('A.ad_id AS id, "페이스북" AS media, "광고" AS type, A.ad_name AS name, A.status AS status');
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.adset_id = B.adset_id', 'left');
                $facebookBuilder->join('z_facebook.fb_campaign AS C', 'B.campaign_id = C.campaign_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'C.account_id = D.ad_account_id', 'left');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id', 'left');
                $facebookBuilder->where('E.company_id', $id);
            }else{
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.adset_id = B.adset_id', 'left');
                $facebookBuilder->join('z_facebook.fb_campaign AS C', 'B.campaign_id = C.campaign_id', 'left');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'C.account_id = D.ad_account_id', 'left');

                if($media == '페이스북'){
                    if($type == '매체광고주'){
                        $facebookBuilder->where('D.ad_account_id', $id);
                    }else if($type == '캠페인'){
                        $facebookBuilder->where('C.campaign_id', $id);
                    }else if($type == '광고그룹'){
                        $facebookBuilder->where('B.adset_id', $id);
                    }else if($type == '광고'){
                        $facebookBuilder->where('A.ad_id', $id);
                    }
                }else{
                    $facebookBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $facebookBuilder->groupStart();
                $facebookBuilder->like('A.ad_name', $data['stx']);
                $facebookBuilder->orLike('A.ad_id', $data['stx']);
                $facebookBuilder->groupEnd();
            }

            $googleBuilder = $this->zenith->table('z_adwords.aw_ad A');
            $googleBuilder->select('A.id, "구글" AS media, "광고" AS type, A.name, A.status AS status');
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.adgroupId = B.id', 'left');
                $googleBuilder->join('z_adwords.aw_campaign AS C', 'B.campaignId = C.id', 'left');
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'C.customerId = D.customerId', 'left');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id', 'left');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.adgroupId = B.id', 'left');
                $googleBuilder->join('z_adwords.aw_campaign AS C', 'B.campaignId = C.id', 'left');
                $googleBuilder->join('z_adwords.aw_ad_account AS F', 'C.customerId = F.customerId', 'left');
                if($media == '구글'){
                    if($type == '매체광고주'){
                        $googleBuilder->where('F.customerId', $id);
                    }else if($type == '캠페인'){
                        $googleBuilder->where('C.id', $id);
                    }else if($type == '광고그룹'){
                        $googleBuilder->where('B.id', $id);
                    }else if($type == '광고'){
                        $googleBuilder->where('A.id', $id);
                    }
                }else{
                    $googleBuilder->where('1 = 2');
                }
            }

            if(!empty($data['stx'])){
                $googleBuilder->groupStart();
                $googleBuilder->like('A.name', $data['stx']);
                $googleBuilder->orLike('A.id', $data['stx']);
                $googleBuilder->groupEnd();
            }

            $kakaoBuilder = $this->zenith->table('z_moment.mm_creative A');
            $kakaoBuilder->select('A.id, "카카오" AS media, "광고" AS type, A.name, A.config AS status');
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.adgroup_id = B.id', 'left');
                $kakaoBuilder->join('z_moment.mm_campaign AS C', 'B.campaign_id = C.id', 'left');
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'C.ad_account_id = D.id', 'left');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id', 'left');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.adgroup_id = B.id', 'left');
                $kakaoBuilder->join('z_moment.mm_campaign AS C', 'B.campaign_id = C.id', 'left');
                $kakaoBuilder->join('z_moment.mm_ad_account AS F', 'C.ad_account_id = F.id', 'left');
                if($media == '카카오'){
                    if($type == '매체광고주'){
                        $kakaoBuilder->where('F.id', $id);
                    }else if($type == '캠페인'){
                        $kakaoBuilder->where('C.id', $id);
                    }else if($type == '광고그룹'){
                        $kakaoBuilder->where('B.id', $id);
                    }else if($type == '광고'){
                        $kakaoBuilder->where('A.id', $id);
                    }
                }else{
                    $kakaoBuilder->where('1 = 2');
                }
            }
            
            if(!empty($data['stx'])){
                $kakaoBuilder->groupStart();
                $kakaoBuilder->like('A.name', $data['stx']);
                $kakaoBuilder->orLike('A.id', $data['stx']);
                $kakaoBuilder->groupEnd();
            }
            
            if ($unionBuilder === null) {
                $unionBuilder = $facebookBuilder;
            } else {
                $unionBuilder = $unionBuilder->union($facebookBuilder);
            }
        
            $unionBuilder = $unionBuilder->union($googleBuilder);
            $unionBuilder = $unionBuilder->union($kakaoBuilder);
        }

        $resultQuery = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
        $resultQuery->groupBy('adv.id');
        $builderNoLimit = clone $resultQuery;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $resultQuery->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getAutomation($data)
    {
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('
        aa.seq as aa_seq, 
        aa.subject as aa_subject,
        aa.description as aa_description,
        aa.slack_webhook as aa_slack_webhook,
        aa.slack_msg as aa_slack_msg,
        aas.schedule_value as aas_schedule_value,
        aas.exec_once as aas_exec_once,
        ');
        $builder->join('aa_schedule_new aas', 'aas.idx = aa.seq', 'left');
        $builder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $builder->where('aa.seq', $data['id']);
        $builder->groupBy('aa.seq');
        $result  = $builder->get()->getRowArray();

        $targetBuilder = $this->zenith->table('aa_target aat');
        $targetBuilder->where('aat.idx', $result['aa_seq']);
        $result['targets'] = $targetBuilder->get()->getResultArray();
        if(!empty($result['targets'])){
            foreach ($result['targets'] as &$target) {
                switch ($target['type']) {
                    case 'advertiser':
                        $targetResult = $this->getSearchCompaniesById($target['id']);    
                        $target['name'] = $targetResult['name'];
                        $target['status'] = $targetResult['status'];
                        break;
                    case 'account':
                        $targetResult = $this->getSearchAccountsById($target['id']);
                        $target['name'] = $targetResult['name'];
                        $target['status'] = $targetResult['status'];
                        break;
                    case 'campaign':
                        $targetResult = $this->getSearchCampaignsById($target['id']);
                        $target['name'] = $targetResult['name'];
                        $target['status'] = $targetResult['status'];
                        break;
                    case 'adgroup':
                        $targetResult = $this->getSearchAdsetsById($target['id']);
                        $target['name'] = $targetResult['name'];
                        $target['status'] = $targetResult['status'];
                        break;
                    case 'ad':
                        $targetResult = $this->getSearchAdsById($target['id']);
                        $target['name'] = $targetResult['name'];
                        $target['status'] = $targetResult['status'];
                        break;
                    default:
                        break;
                }
            }
        }
        
        $conditionsBuilder = $this->zenith->table('aa_conditions aac');
        $conditionsBuilder->where('aac.idx', $result['aa_seq']);
        $result['conditions'] = $conditionsBuilder->get()->getResultArray();

        $executionsBuilder = $this->zenith->table('aa_executions aae');
        $executionsBuilder->where('aae.idx', $result['aa_seq']);
        $result['executions'] = $executionsBuilder->get()->getResultArray();

        if(!empty($result['executions'])){
            foreach ($result['executions'] as &$execution) {
                switch ($execution['type']) {
                    case 'campaign':
                        $executionResult = $this->getSearchCampaignsById($execution['id']);
                        $execution['name'] = $executionResult['name'];
                        $execution['status'] = $executionResult['status'];
                        break;
                    case 'adgroup':
                        $executionResult = $this->getSearchAdsetsById($execution['id']);
                        $execution['name'] = $executionResult['name'];
                        $execution['status'] = $executionResult['status'];
                        break;
                    case 'ad':
                        $executionResult = $this->getSearchAdsById($execution['id']);
                        $execution['name'] = $executionResult['name'];
                        $execution['status'] = $executionResult['status'];
                        break;
                    default:
                        break;
                }
            }
        }

        return $result;
    }

    public function getAutomations()
    {
        $subQueryBuilder = $this->zenith->table('aa_result aar');
        $subQueryBuilder->select('aar.idx, aar.result, MAX(aar.exec_timestamp) as aar_exec_timestamp');
        $subQueryBuilder->groupBy('aar.idx');
        
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('
        aa.seq as aa_seq, 
        aa.slack_webhook as aa_slack_webhook,
        aa.slack_msg as aa_slack_msg,
        aas.idx as aas_idx, 
        aas.schedule_value as aas_schedule_value,
        aas.exec_once as aas_exec_once,  
        aar_sub.aar_exec_timestamp as aar_exec_timestamp,
        (SELECT MAX(aar.exec_timestamp) 
        FROM aa_result aar 
        WHERE aar.idx = aa.seq AND aar.result = "success" AND DATE(aar.exec_timestamp) = CURDATE()) as aar_exec_timestamp_success,
        ');
        $builder->join('aa_schedule_new aas', 'aas.idx = aa.seq', 'left');
        $builder->join("({$subQueryBuilder->getCompiledSelect()}) aar_sub", 'aar_sub.idx = aa.seq', 'left');
        $builder->where('aa.status', 1);
        $builder->groupBy('aa.seq');
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getAutomationTargets($seq)
    {   
        $builder = $this->zenith->table('aa_target');
        $builder->select('
        idx as aat_idx,
        type as aat_type,
        media as aat_media,
        id as aat_id
        ');
        $builder->where('idx', $seq);
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getAutomationResultCount($seq)
    {   
        $builder = $this->zenith->table('aa_result');
        $builder->where('idx', $seq);
        $result = $builder->countAllResults();

        return $result;
    }

    public function getTargetCompany($data)
    {
        $companyBuilder = $this->zenith->table('companies c');
        $companyBuilder->select('c.id, c.status, ca.media, GROUP_CONCAT(DISTINCT ca.ad_account_id) as ad_account_id');
        $companyBuilder->join('company_adaccounts ca', 'c.id = ca.company_id');
        $companyBuilder->where('c.id', $data['aat_id']);
        $companyBuilder->groupBy('ca.media');
        $companies = $companyBuilder->get()->getResultArray();
        $builders = [];
        foreach ($companies as $company) {
            if(!empty($company['media'])){
                switch ($company['media']) {
                    case 'facebook':
                        $facebookBuilder = $this->getFacebookByCompany($data, $company);
                        $builders[] = $facebookBuilder;
                        break;
                    case 'google':
                        $googleBuilder = $this->getGoogleByCompany($data, $company);
                        $builders[] = $googleBuilder;
                        break;
                    case 'kakao':
                        $kakaoBuilder = $this->getKakaoByCompany($data, $company);
                        $builders[] = $kakaoBuilder;
                        break;
                    default:
                        break;
                }
            }
        }

        if(empty($builders)){return false;}
        $unionBuilder = null;
        foreach ($builders as $builder) {
            if ($unionBuilder) {
                $unionBuilder->union($builder);
            } else {
                $unionBuilder = $builder;
            }
        }
        $builder = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
        $result = $builder->get()->getResultArray();
        return $result;
    }

    private function getFacebookByCompany($data, $company)
    {
        $subQuery = $this->zenith->table('z_facebook.fb_ad_insight_history A');
        $subQuery->select(' 
            D.account_id AS customerId,
            D.campaign_id AS id, 
            D.budget AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.spend) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.inline_link_clicks) AS click,
        ');
        $subQuery->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $subQuery->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $subQuery->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
        $subQuery->groupBy('D.campaign_id');

        $facebookBuilder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');
        $facebookBuilder->select('
            SUM(sub.budget) as budget,
            SUM(sub.unique_total) as unique_total, 
            SUM(sub.spend) as spend, 
            SUM(sub.margin) as margin, 
            SUM(sub.sales) as sales, 
            SUM(sub.impressions) as impressions, 
            SUM(sub.click) as click');
        $facebookBuilder->join('z_facebook.fb_ad_account E', 'sub.customerId = E.ad_account_id');
        $facebookBuilder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
        $facebookBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $facebookBuilder->whereIn('E.ad_account_id', explode(",",$company['ad_account_id']));
        $facebookBuilder->where('G.id', $data['aat_id']);
        $facebookBuilder->groupBy('G.id');

        return $facebookBuilder;
    }

    private function getGoogleByCompany($data, $company)
    {
        $subQuery = $this->zenith->table('z_adwords.aw_ad_report_history A');
        $subQuery->select(' 
            D.customerId AS customerId,
            D.id AS id, 
            D.amount AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.clicks) AS click,
        ');
        $subQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $subQuery->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $subQuery->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $subQuery->where('D.status !=', 'NODATA');
        $subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
        $subQuery->groupBy('D.id');
        
        $googleBuilder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');
        $googleBuilder->select('
            SUM(sub.budget) as budget,
            SUM(sub.unique_total) as unique_total, 
            SUM(sub.spend) as spend, 
            SUM(sub.margin) as margin, 
            SUM(sub.sales) as sales, 
            SUM(sub.impressions) as impressions, 
            SUM(sub.click) as click
        ');
        $googleBuilder->join('z_adwords.aw_ad_account E', 'sub.customerId = E.customerId');
        $googleBuilder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
        $googleBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $googleBuilder->whereIn('E.customerId', explode(",",$company['ad_account_id']));
        $googleBuilder->where('G.id', $data['aat_id']);
        $googleBuilder->groupBy('G.id');

        return $googleBuilder;
    }

    private function getKakaoByCompany($data, $company)
    {
        $subQuery = $this->zenith->table('z_moment.mm_creative_report_basic A');
        $subQuery->select(' 
            D.ad_account_id AS customerId,
            D.id AS id, 
            D.dailyBudgetAmount AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.imp) AS impressions,
            SUM(A.click) AS click,
        ');
        $subQuery->join('z_moment.mm_creative B', 'A.id = B.id');
        $subQuery->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
        $subQuery->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        $subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
        $subQuery->groupBy('D.id');

        $kakaoBuilder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');
        $kakaoBuilder->select('
            SUM(sub.budget) as budget,
            SUM(sub.unique_total) as unique_total, 
            SUM(sub.spend) as spend, 
            SUM(sub.margin) as margin, 
            SUM(sub.sales) as sales, 
            SUM(sub.impressions) as impressions, 
            SUM(sub.click) as click
        ');
        $kakaoBuilder->join('z_moment.mm_ad_account E', 'sub.customerId = E.id');
        $kakaoBuilder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
        $kakaoBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $kakaoBuilder->whereIn('E.id', explode(",",$company['ad_account_id']));
        $kakaoBuilder->where('G.id', $data['aat_id']);
        $kakaoBuilder->groupBy('G.id');

        return $kakaoBuilder;
    }


    public function getTargetFacebook($data)
    {
        $subQuery = $this->zenith->table('z_facebook.fb_ad_insight_history A');
		$subQuery->select('
		SUM(A.impressions) AS impressions, 
		SUM(A.inline_link_clicks) AS click, 
		SUM(A.spend) AS spend, 
		SUM(A.sales) as sales, 
		SUM(A.db_count) as unique_total, 
		SUM(A.margin) as margin');
		$subQuery->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
		$subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
		
        if($data['aat_type'] === 'account' || $data['aat_type'] === 'campaign'){
            $subQuery->select('D.account_id as customerId, 
            D.campaign_id AS id, D.status AS status, D.budget AS budget');
            $subQuery->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
            $subQuery->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
            $subQuery->groupBy('D.campaign_id');
        }else if($data['aat_type'] === 'adgroup'){
            $subQuery->select('C.campaign_id as campaign_id, 
            C.adset_id AS id, C.status AS status, C.budget AS budget ');
            $subQuery->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
            $subQuery->groupBy('C.adset_id');  
        }else if($data['aat_type'] === 'ad'){
            $subQuery->select('B.adset_id as adset_id, 
            B.ad_id AS id, B.status AS status, 0 AS budget');
            $subQuery->where('B.ad_id', $data['aat_id']);
            $subQuery->groupBy('B.ad_id');  
        }

        $builder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');

        if($data['aat_type'] === 'account'){
            $builder->select(' 
                SUM(sub.budget) as budget,
                SUM(sub.unique_total) as unique_total, 
                SUM(sub.spend) as spend, 
                SUM(sub.margin) as margin, 
                SUM(sub.sales) as sales, 
                SUM(sub.impressions) as impressions, 
                SUM(sub.click) as click,
                E.status AS status
            ');
            $builder->join('z_facebook.fb_ad_account E', 'sub.customerId = E.ad_account_id');
            $builder->where('E.ad_account_id', $data['aat_id']);      
            $builder->groupBy('E.ad_account_id');  
        }else{
            $builder->select('
                sub.budget as budget,
                sub.unique_total as unique_total, 
                sub.spend as spend, 
                sub.margin as margin, 
                sub.sales as sales, 
                sub.impressions as impressions, 
                sub.click as click, 
                sub.status AS status
            ');
            $builder->where('sub.id', $data['aat_id']);
            $builder->groupBy('sub.id'); 
        }
       
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getTargetGoogle($data)
    {
        $subQuery = $this->zenith->table('z_adwords.aw_ad_report_history A');
		$subQuery->select('
		SUM(A.db_count) AS unique_total,
        SUM(A.cost) AS spend,
        SUM(A.margin) AS margin,
        SUM(A.sales) AS sales,
        SUM(A.impressions) AS impressions,
        SUM(A.clicks) AS click,');
		$subQuery->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
		$subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
		
        if($data['aat_type'] === 'account' || $data['aat_type'] === 'campaign'){
            $subQuery->select('D.customerId as customerId,
            D.id AS id, D.status AS status, D.amount AS budget');
            $subQuery->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
            $subQuery->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
            $subQuery->groupBy('D.id');
        }else if($data['aat_type'] === 'adgroup'){
            $subQuery->select('C.campaignId as campaign_id,
            C.id AS id, C.status AS status, 0 AS budget');
            $subQuery->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
            $subQuery->groupBy('C.id');  
        }else if($data['aat_type'] === 'ad'){
            $subQuery->select('B.adgroupId as adgroupId,
            B.id AS id, B.status AS status, 0 AS budget');
            $subQuery->where('B.id', $data['aat_id']);
            $subQuery->groupBy('B.id');  
        }

        $builder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');

        if($data['aat_type'] === 'account'){
            $builder->select(' 
                SUM(sub.budget) as budget,
                SUM(sub.unique_total) as unique_total, 
                SUM(sub.spend) as spend, 
                SUM(sub.margin) as margin, 
                SUM(sub.sales) as sales, 
                SUM(sub.impressions) as impressions, 
                SUM(sub.click) as click,
                E.status AS status
            ');
            $builder->join('z_adwords.aw_ad_account E', 'sub.customerId = E.customerId');
            $builder->where('E.customerId', $data['aat_id']);      
            $builder->groupBy('E.customerId');  
        }else{
            $builder->select('
                sub.budget as budget,
                sub.unique_total as unique_total, 
                sub.spend as spend, 
                sub.margin as margin, 
                sub.sales as sales, 
                sub.impressions as impressions, 
                sub.click as click, 
                sub.status AS status
            ');
            $builder->where('sub.id', $data['aat_id']);
            $builder->groupBy('sub.id'); 
        }
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getTargetKakao($data)
    {
        $subQuery = $this->zenith->table('z_moment.mm_creative_report_basic A');
		$subQuery->select('
		SUM(A.imp) AS impressions, 
        SUM(A.click) AS click, 
        SUM(A.cost) AS spend, 
        SUM(A.sales) AS sales, 
        SUM(A.db_count) as unique_total,
        SUM(A.margin) as margin');
		$subQuery->join('z_moment.mm_creative B', 'A.id = B.id');
		$subQuery->where('DATE(A.date) >=', date('Y-m-d'));
        $subQuery->where('DATE(A.date) <=', date('Y-m-d'));
		
        if($data['aat_type'] === 'account' || $data['aat_type'] === 'campaign'){
            $subQuery->select('D.ad_account_id as customerId,
            D.id AS id, D.config AS status, D.dailyBudgetAmount AS budget');
            $subQuery->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
		    $subQuery->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
            $subQuery->groupBy('D.id');
        }else if($data['aat_type'] === 'adgroup'){
            $subQuery->select('C.campaign_id as campaign_id, 
            C.id AS id, C.config AS status, C.dailyBudgetAmount AS budget');
            $subQuery->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
            $subQuery->groupBy('C.id');  
        }else if($data['aat_type'] === 'ad'){
            $subQuery->select('B.adgroup_id as adgroup_id, 
            B.id AS id, B.config AS status, 0 AS budget');
            $subQuery->where('B.id', $data['aat_id']);
            $subQuery->groupBy('B.id');  
        }

        $builder = $this->zenith->newQuery()->fromSubquery($subQuery, 'sub');

        if($data['aat_type'] === 'account'){
            $builder->select(' 
                SUM(sub.budget) as budget,
                SUM(sub.unique_total) as unique_total, 
                SUM(sub.spend) as spend, 
                SUM(sub.margin) as margin, 
                SUM(sub.sales) as sales, 
                SUM(sub.impressions) as impressions, 
                SUM(sub.click) as click,
                E.config AS status
            ');
            $builder->join('z_moment.mm_ad_account E', 'sub.customerId = E.id');
            $builder->where('E.id', $data['aat_id']);      
            $builder->groupBy('E.id');  
        }else{
            $builder->select('
                sub.budget as budget,
                sub.unique_total as unique_total, 
                sub.spend as spend, 
                sub.margin as margin, 
                sub.sales as sales, 
                sub.impressions as impressions, 
                sub.click as click, 
                sub.status AS status
            ');
            $builder->where('sub.id', $data['aat_id']);
            $builder->groupBy('sub.id'); 
        }
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getAutomationConditionBySeq($seq)
    {
        $builder = $this->zenith->table('aa_conditions');
        $builder->select('*');
        $builder->where('idx', $seq);
        $builder->orderBy('order', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
    }
    
    public function getExecution($seq)
    {   
        $builder = $this->zenith->table('aa_executions');
        $builder->select('order, media, type, id, exec_type, exec_value, exec_budget_type');
        $builder->where('idx', $seq);
        $builder->orderBy('order', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getLogs($data)
    {   
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('aa.seq, aa.subject, aa.nickname, aar.result, aar.exec_timestamp, aarl.schedule_desc, aarl.target_desc, aarl.conditions_desc, aarl.executions_desc');
        $builder->join('aa_result aar', 'aa.seq = aar.idx');
        $builder->join('aa_result_logs aarl', 'aar.seq = aarl.idx');
        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('aa.subject', $data['stx']);
            $builder->orLike('aa.nickname', $data['stx']);
            $builder->groupEnd();
        }
        if(!empty($data['seq'])){
            $builder->where('aa.seq', $data['seq']);
        }
        $builder->groupBy('aar.seq');
        $builderNoLimit = clone $builder;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "aar.exec_timestamp DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];

        return $result;
    }

    public function getAutomationByAdv($id)
    {   
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select("
            aa.seq as aa_seq,
            aa.subject as aa_subject,
        ");
        $builder->join('aa_target aat', 'aa.seq = aat.idx');
        $builder->join('aa_executions aae', 'aa.seq = aae.idx');

        $builder->where('aat.id', $id);
        $builder->orWhere('aae.id', $id);

        $builder->groupBy('aa.seq');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getLogsByAdv($data, $id)
    {   
        $builder = $this->zenith->table('aa_result aar');
        $builder->select("
            aa.seq as seq,
            aa.subject as subject,
            aa.nickname as nickname,
            IF(aat.id = $id, 'true', 'false') as aat_exist,
            IF(aae.id = $id, 'true', 'false') as aae_exist,
            aar.result as result, 
            aar.exec_timestamp as exec_timestamp, 
            aarl.schedule_desc as schedule_desc, 
            aarl.target_desc as target_desc, 
            aarl.conditions_desc as conditions_desc, 
            aarl.executions_desc as executions_desc
        ");
        $builder->join('aa_result_logs aarl', 'aar.seq = aarl.idx', 'left');
        $builder->join('aa_executions aae', 'aar.idx = aae.idx');
        $builder->join('aa_conditions aac', 'aar.idx = aac.idx');
        $builder->join('aa_target aat', 'aar.idx = aat.idx');
        $builder->join('aa_schedule aas', 'aar.idx = aas.idx');
        $builder->join('admanager_automation aa', 'aar.idx = aa.seq');

        if(!empty($data['aa_seq'])){
            $builder->whereIn('aa.seq', $data['aa_seq']);
        }else{
            $builder->where('aat.id', $id);
            $builder->orWhere('aae.id', $id);
        }

        $builder->groupBy('aar.seq');
        $builderNoLimit = clone $builder;
        
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "aar.exec_timestamp DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];

        return $result;
    }

    public function getLog($data)
    {   
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('aa.seq, aa.subject, aa.nickname, aar.result, aar.exec_timestamp, aarl.schedule_desc, aarl.target_desc, aarl.conditions_desc, aarl.executions_desc');
        $builder->join('aa_result aar', 'aa.seq = aar.idx');
        $builder->join('aa_result_logs aarl', 'aar.seq = aarl.idx');
        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('aa.subject', $data['stx']);
            $builder->orLike('aa.nickname', $data['stx']);
            $builder->groupEnd();
        }
        if(!empty($data['seq'])){
            $builder->where('aa.seq', $data['seq']);
        }
        $builder->groupBy('aar.seq');
        $builderNoLimit = clone $builder;
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "aar.exec_timestamp DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];

        return $result;
    }

    public function createAutomation($data)
    {
        $data['detail']['nickname'] = auth()->user()->nickname ?? '';
        $data['detail']['status'] = 1;
        $data['detail']['mod_datetime'] = date('Y-m-d H:i:s');


        $this->zenith->transStart();
        $aaBuilder = $this->zenith->table('admanager_automation');
        $result = $aaBuilder->insert($data['detail']);
        $seq = $this->zenith->insertID();

        $data['schedule'] = array_filter($data['schedule']);
        $data['schedule']['idx'] = $seq;
        $aasBuilder = $this->zenith->table('aa_schedule_new');
        $aasBuilder->insert($data['schedule']);

        if(!empty($data['target'])){
            foreach ($data['target'] as $target) {
                $data['target'] = array_filter($data['target']);
                $target['idx'] = $seq;
                $aatBuilder = $this->zenith->table('aa_target');
                $aatBuilder->insert($target);
            }
        }
        
        if(!empty($data['condition'])){
            foreach ($data['condition'] as $condition) {
                $data['condition'] = array_filter($data['condition']);
                $condition['idx'] = $seq;
                $aacBuilder = $this->zenith->table('aa_conditions');
                $aacBuilder->insert($condition);
            }
        }

        foreach ($data['execution'] as $execution) {
            $data['execution'] = array_filter($data['execution']);
            $execution['idx'] = $seq;
            $aaeBuilder = $this->zenith->table('aa_executions');
            $aaeBuilder->insert($execution);
        }

        $result = $this->zenith->transComplete();
        return $result;
    }

    public function copyAutomation($data)
    {
        $this->zenith->transStart();
        $aaGetBuilder = $this->zenith->table('admanager_automation aa');
        $aaGetBuilder->select('
        aa.seq as aa_seq, 
        aa.subject as aa_subject,
        aa.description as aa_description,
        aa.status as aa_status,
        aa.slack_webhook as aa_slack_webhook,
        aa.slack_msg as aa_slack_msg,
        aas.schedule_value as schedule_value,
        aas.exec_once as exec_once,
        ');
        $aaGetBuilder->join('aa_schedule_new aas', 'aas.idx = aa.seq', 'left');
        $aaGetBuilder->where('aa.seq', $data['seq']);
        $aaGetResult  = $aaGetBuilder->get()->getRowArray();

        $aatGetBuilder = $this->zenith->table('aa_target');
        $aatGetBuilder->select('type, media, id');
        $aatGetBuilder->where('idx', $data['seq']);
        $aatGetResult  = $aatGetBuilder->get()->getResultArray();

        $aacGetBuilder = $this->zenith->table('aa_conditions');
        $aacGetBuilder->select('order, type, type_value, compare, operation');
        $aacGetBuilder->where('idx', $data['seq']);
        $aacGetResult  = $aacGetBuilder->get()->getResultArray();

        $aaeGetBuilder = $this->zenith->table('aa_executions');
        $aaeGetBuilder->select('order, media, type, id, exec_type, exec_value, exec_budget_type');
        $aaeGetBuilder->where('idx', $data['seq']);
        $aaeGetResult  = $aaeGetBuilder->get()->getResultArray();

        $aaData = [
            'subject' => $aaGetResult['aa_subject']." - 복제",
            'description' => $aaGetResult['aa_description'] ?? null,
            'nickname' => auth()->user()->nickname ?? '',
            'status' => $aaGetResult['aa_status'],
            'slack_webhook' =>$aaGetResult['aa_slack_webhook'],
            'slack_msg' =>$aaGetResult['aa_slack_msg'],
            'mod_datetime' => date('Y-m-d H:i:s'),
        ];
        $aaBuilder = $this->zenith->table('admanager_automation');
        $aaBuilder->insert($aaData);
        $seq = $this->zenith->insertID();
        
        $aasData = [
            'idx' => $seq,
            'schedule_value' => $aaGetResult['schedule_value'],
            'exec_once' => $aaGetResult['exec_once'],
        ];
        $aasBuilder = $this->zenith->table('aa_schedule_new');
        $aasBuilder->insert($aasData);

        if(!empty($aatGetResult)){
            foreach ($aatGetResult as $target) {
                $aatData = [
                    'idx' => $seq,
                    'type' => $target['type'],
                    'media' => $target['media'],
                    'id' => $target['id'],
                ];
                $aatBuilder = $this->zenith->table('aa_target');
                $aatBuilder->insert($aatData);
            }
        }
        
        if(!empty($aacGetResult)){
            foreach ($aacGetResult as $condition) {
                $aacData = [
                    'idx' => $seq,
                    'type' => $condition['type'],
                    'type_value' => $condition['type_value'] ?? '',
                    'compare' => $condition['compare'],
                    'operation' => $condition['operation'],
                ];
                $aacBuilder = $this->zenith->table('aa_conditions');
                $aacBuilder->insert($aacData);
            }
        }

        foreach ($aaeGetResult as $execution) {
            $aaeData = [
                'idx' => $seq,
                'order' => $execution['order'] ?? 0,
                'media' => $execution['media'],
                'type' => $execution['type'],
                'id' => $execution['id'],
                'exec_type' => $execution['exec_type'],
                'exec_value' => $execution['exec_value'] ?? '',
                'exec_budget_type' => $execution['exec_budget_type'] ?? '',
            ];
            $aaeBuilder = $this->zenith->table('aa_executions');
            $aaeBuilder->insert($aaeData);
        }

        $result = $this->zenith->transComplete();
        return $result;
    }

    public function updateAutomation($data)
    {
        $aaData = [
            'subject' => $data['detail']['subject'],
            'description' => $data['detail']['description'],
            'slack_webhook' =>$data['detail']['slack_webhook'],
            'slack_msg' =>$data['detail']['slack_msg'],
            'mod_datetime' => date('Y-m-d H:i:s'),
        ];

        $this->zenith->transStart();
        $aaBuilder = $this->zenith->table('admanager_automation');
        $aaBuilder->where('seq', $data['seq']);
        $result = $aaBuilder->update($aaData);
        
        $aasBuilder = $this->zenith->table('aa_schedule_new');
        $aasBuilder->where('idx', $data['seq']);
        $aasBuilder->update($data['schedule']);

        if(!empty($data['target'])){
            $aatBuilder = $this->zenith->table('aa_target');
            $aatBuilder->where('idx', $data['seq']);
            $aatBuilder->delete();
            foreach ($data['target'] as $target) {
                $data['target'] = array_filter($data['target']);
                $target['idx'] = $data['seq'];
                $aatBuilder->insert($target);
            }
        }
        
        if(!empty($data['condition'])){
            $aacBuilder = $this->zenith->table('aa_conditions');
            $aacBuilder->where('idx', $data['seq']);
            $aacBuilder->delete();
            foreach ($data['condition'] as $condition) {
                $data['condition'] = array_filter($data['condition']);
                $condition['idx'] = $data['seq'];
                $aacBuilder->insert($condition);
            }
        }

        if(!empty($data['execution'])){
            $aaeBuilder = $this->zenith->table('aa_executions');
            $aaeBuilder->where('idx', $data['seq']);
            $aaeBuilder->delete();
            foreach ($data['execution'] as $execution) {
                $data['execution'] = array_filter($data['execution']);
                $execution['idx'] = $data['seq'];
                $aaeBuilder->insert($execution);
            }
        }
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function deleteAutomation($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('admanager_automation');
        $builder->where('seq', $data['seq']);
        $builder->delete();
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function setAutomationStatus($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('admanager_automation');
        $builder->set('status', $data['status']);
        $builder->where('seq', $data['seq']);
        $builder->update();
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function recodeResult($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_result');
        $builder->insert($data);
        $seq = $this->zenith->insertID();
        $result = $this->zenith->transComplete();
       
        if($result == true){
            $builder = $this->zenith->table('aa_result');
            $builder->select('seq, reg_datetime');
            $builder->where('seq', $seq);
            $row = $builder->get()->getRowArray();
        }else{
            return false;
        }

        return $row;
    }

    public function recodeLog($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_result_logs');
        $builder->insert($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    private function setAccountData($datas)
    {
        $total['budget'] = $total['unique_total'] = $total['spend'] = $total['margin'] = $total['sales'] = $total['impressions'] = $total['click'] = 0;

        foreach($datas as $data){
            $total['budget'] +=$data['budget'];
            $total['unique_total'] +=$data['unique_total'];
            $total['spend'] +=$data['spend'];
            $total['margin'] +=$data['margin'];
            $total['sales'] +=$data['sales'];
            $total['impressions'] +=$data['impressions'];
            $total['click'] +=$data['click'];
        }

        $total['status'] = $datas[0]['status'];
        return $total;
    }
}
