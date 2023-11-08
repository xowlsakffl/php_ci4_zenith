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
        aar.exec_timestamp as aar_exec_timestamp
        ');
        $builder->join('aa_result aar', 'aar.idx = aa.seq', 'left');

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

    public function getSearchCompanies($data = null, $seq = null)
    {
        if(!empty($data['adv'])){
            list($media, $type, $id) = explode("_", $data['adv']);
        }

        $builder = $this->zenith->table('companies A');
        $builder->select('A.id, "광고주" AS media, A.type, A.name, A.status');

        if(!empty($data['stx'])){
            $builder->groupStart();
            $builder->like('A.name', $data['stx']);
            $builder->orLike('A.id', $data['stx']);
            $builder->groupEnd();
        }

        if(!empty($data['adv'])){
            if($media == '광고주'){
                $builder->where('A.id', $id);
            }else{
                $builder->join('company_adaccounts AS B', 'A.id = B.company_id');
                if($media == '페이스북'){
                    $builder->join('z_facebook.fb_ad_account AS C', 'B.ad_account_id = C.ad_account_id');
                    $builder->join('z_facebook.fb_campaign AS D', 'C.ad_account_id = D.account_id');
                    $builder->join('z_facebook.fb_adset AS E', 'D.campaign_id = E.campaign_id');
                    $builder->join('z_facebook.fb_ad AS F', 'E.adset_id = F.adset_id');
                    if($type == '캠페인'){
                        $builder->where('D.campaign_id', $id);
                    }else if($type == '광고그룹'){
                        $builder->where('E.adset_id', $id);
                    }else if($type == '광고'){
                        $builder->where('F.ad_id', $id);
                    }
                }
                
                if($media == '구글'){
                    $builder->join('z_adwords.aw_ad_account AS C', 'B.ad_account_id = C.customerId');
                    $builder->join('z_adwords.aw_campaign AS D', 'C.customerId = D.customerId');
                    $builder->join('z_adwords.aw_adgroup AS E', 'D.id = E.campaignId');
                    $builder->join('z_adwords.aw_ad AS F', 'E.id = F.adgroupId');
                    if($type == '캠페인'){
                        $builder->where('D.id', $id);
                    }else if($type == '광고그룹'){
                        $builder->where('E.id', $id);
                    }else if($type == '광고'){
                        $builder->where('F.id', $id);
                    }
                }

                if($media == '카카오'){
                    $builder->join('z_moment.mm_ad_account AS C', 'B.ad_account_id = C.id');
                    $builder->join('z_moment.mm_campaign AS D', 'C.id = D.ad_account_id');
                    $builder->join('z_moment.mm_adgroup AS E', 'D.id = E.campaign_id');
                    $builder->join('z_moment.mm_creative AS F', 'E.id = F.adgroup_id');
                    if($type == '캠페인'){
                        $builder->where('D.id', $id);
                    }else if($type == '광고그룹'){
                        $builder->where('E.id', $id);
                    }else if($type == '광고'){
                        $builder->where('F.id', $id);
                    }
                }
            }
        }
        
        $builder->groupBy('A.id');
        if(!empty($seq)){
            $builder->where('A.id', $seq);
            $result = $builder->get()->getRowArray();
            return $result;
        }

        $builderNoLimit = clone $builder;
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchCampaigns($data = null, $seq = null)
    {
        if(!empty($data['adv'])){
            list($media, $type, $id) = explode("_", $data['adv']);
        }

        $facebookBuilder = $this->zenith->table('z_facebook.fb_campaign A');
        $facebookBuilder->select('A.campaign_id AS id, "페이스북" AS media, "캠페인" AS type, A.campaign_name AS name, A.status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'A.account_id = D.ad_account_id');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id');
                $facebookBuilder->where('E.company_id', $id);
            }else{
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.campaign_id = B.campaign_id');
                $facebookBuilder->join('z_facebook.fb_ad AS C', 'B.adset_id = C.adset_id');

                if($media == '페이스북'){
                    if($type == '캠페인'){
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
        }
        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.campaign_name', $data['stx']);
            $facebookBuilder->orLike('A.campaign_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_campaign A');
        $googleBuilder->select('A.id, "구글" AS media, "캠페인" AS type, A.name, A.status');
        if(!empty($data['adv'])){ 
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'A.customerId = D.customerId');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.id = B.campaignId');
                $googleBuilder->join('z_adwords.aw_ad AS C', 'B.id = C.adgroupId');

                if($media == '구글'){
                    if($type == '캠페인'){
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
        }
        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_campaign A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "캠페인" AS type, A.name, A.config AS status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'A.ad_account_id = D.id');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.id = B.campaign_id');
                $kakaoBuilder->join('z_moment.mm_creative AS C', 'B.id = C.adgroup_id');

                if($media == '카카오'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        if(!empty($seq)){
            $resultQuery->where('adv.id', $seq);
            $result = $resultQuery->get()->getRowArray();
            return $result;
        }

        $builderNoLimit = clone $resultQuery;
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAdsets($data = null, $seq = null)
    {
        if(!empty($data['adv'])){
            list($media, $type, $id) = explode("_", $data['adv']);
        }

        $facebookBuilder = $this->zenith->table('z_facebook.fb_adset A');
        $facebookBuilder->select('A.adset_id AS id, "페이스북" AS media, "광고그룹" AS type, A.adset_name AS name, A.status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_campaign AS B', 'A.campaign_id = B.campaign_id');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'B.account_id = D.ad_account_id');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id');
                $facebookBuilder->where('E.company_id', $id);
            }else{
                $facebookBuilder->join('z_facebook.fb_campaign AS B', 'A.campaign_id = B.campaign_id');
                $facebookBuilder->join('z_facebook.fb_ad AS C', 'A.adset_id = C.adset_id');

                if($media == '페이스북'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.adset_name', $data['stx']);
            $facebookBuilder->orLike('A.adset_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_adgroup A');
        $googleBuilder->select('A.id, "구글" AS media, "광고그룹" AS type, A.name, A.status');
        if(!empty($data['adv'])){ 
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_campaign AS B', 'A.campaignId = B.id');
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'B.customerId = D.customerId');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_campaign AS B', 'A.campaignId = B.id');
                $googleBuilder->join('z_adwords.aw_ad AS C', 'A.id = C.adgroupId');

                if($media == '구글'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_adgroup A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고그룹" AS type, A.name, A.config AS status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_campaign AS B', 'A.campaign_id = B.id');
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'B.ad_account_id = D.id');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_campaign AS B', 'A.campaign_id = B.id');
                $kakaoBuilder->join('z_moment.mm_creative AS C', 'A.id = C.adgroup_id');

                if($media == '카카오'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        if(!empty($seq)){
            $resultQuery->where('adv.id', $seq);
            $result = $resultQuery->get()->getRowArray();
            return $result;
        }

        $builderNoLimit = clone $resultQuery;
        if($data['length'] > 0) $resultQuery->limit($data['length'], $data['start']);
        $result = $resultQuery->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();
        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getSearchAds($data = null, $seq = null)
    {
        if(!empty($data['adv'])){
            list($media, $type, $id) = explode("_", $data['adv']);
        }

        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad A');
        $facebookBuilder->select('A.ad_id AS id, "페이스북" AS media, "광고" AS type, A.ad_name AS name, A.status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.adset_id = B.adset_id');
                $facebookBuilder->join('z_facebook.fb_campaign AS C', 'B.campaign_id = C.campaign_id');
                $facebookBuilder->join('z_facebook.fb_ad_account AS D', 'C.account_id = D.ad_account_id');
                $facebookBuilder->join('zenith.company_adaccounts AS E', 'D.ad_account_id = E.ad_account_id');
                $facebookBuilder->where('E.company_id', $id);
            }else{
                $facebookBuilder->join('z_facebook.fb_adset AS B', 'A.adset_id = B.adset_id');
                $facebookBuilder->join('z_facebook.fb_campaign AS C', 'B.campaign_id = C.campaign_id');
                
                if($media == '페이스북'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $facebookBuilder->groupStart();
            $facebookBuilder->like('A.ad_name', $data['stx']);
            $facebookBuilder->orLike('A.ad_id', $data['stx']);
            $facebookBuilder->groupEnd();
        }

        $googleBuilder = $this->zenith->table('z_adwords.aw_ad A');
        $googleBuilder->select('A.id, "구글" AS media, "광고" AS type, A.name, A.status');
        if(!empty($data['adv'])){ 
            if($type == '광고주'){
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.adgroupId = B.id');
                $googleBuilder->join('z_adwords.aw_campaign AS C', 'B.campaignId = C.id');
                $googleBuilder->join('z_adwords.aw_ad_account AS D', 'C.customerId = D.customerId');
                $googleBuilder->join('zenith.company_adaccounts AS E', 'D.customerId = E.ad_account_id');
                $googleBuilder->where('E.company_id', $id);
            }else{
                $googleBuilder->join('z_adwords.aw_adgroup AS B', 'A.adgroupId = B.id');
                $googleBuilder->join('z_adwords.aw_campaign AS C', 'B.campaignId = C.id');

                if($media == '구글'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $googleBuilder->groupStart();
            $googleBuilder->like('A.name', $data['stx']);
            $googleBuilder->orLike('A.id', $data['stx']);
            $googleBuilder->groupEnd();
        }

        $kakaoBuilder = $this->zenith->table('z_moment.mm_creative A');
        $kakaoBuilder->select('A.id, "카카오" AS media, "광고" AS type, A.name, A.config AS status');
        if(!empty($data['adv'])){
            if($type == '광고주'){
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.adgroup_id = B.id');
                $kakaoBuilder->join('z_moment.mm_campaign AS C', 'B.campaign_id = C.id');
                $kakaoBuilder->join('z_moment.mm_ad_account AS D', 'C.ad_account_id = D.id');
                $kakaoBuilder->join('zenith.company_adaccounts AS E', 'D.id = E.ad_account_id');
                $kakaoBuilder->where('E.company_id', $id);
            }else{
                $kakaoBuilder->join('z_moment.mm_adgroup AS B', 'A.adgroup_id = B.id');
                $kakaoBuilder->join('z_moment.mm_campaign AS C', 'B.campaign_id = C.id');

                if($media == '카카오'){
                    if($type == '캠페인'){
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
        }

        if(!empty($data['stx'])){
            $kakaoBuilder->groupStart();
            $kakaoBuilder->like('A.name', $data['stx']);
            $kakaoBuilder->orLike('A.id', $data['stx']);
            $kakaoBuilder->groupEnd();
        }

        $facebookBuilder->union($googleBuilder)->union($kakaoBuilder);
        $resultQuery = $this->zenith->newQuery()->fromSubquery($facebookBuilder, 'adv');

        $resultQuery->groupBy('adv.id');

        if(!empty($seq)){
            $resultQuery->where('adv.id', $seq);
            $result = $resultQuery->get()->getRowArray();
            return $result;
        }

        $builderNoLimit = clone $resultQuery;
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
        aas.exec_type as aas_exec_type,
        aas.type_value as aas_type_value,
        DATE_FORMAT(aas.exec_time, "%H:%i") as aas_exec_time,
        DATE_FORMAT(aas.ignore_start_time, "%H:%i") as aas_ignore_start_time,
        DATE_FORMAT(aas.ignore_end_time, "%H:%i") as aas_ignore_end_time,
        aas.exec_week as aas_exec_week,
        aas.month_type as aas_month_type,
        aas.month_day as aas_month_day,
        aas.month_week as aas_month_week,
        aat.type as aat_type,
        aat.media as aat_media,
        aat.id as aat_id,
        ');
        $builder->join('aa_schedule aas', 'aas.idx = aa.seq', 'left');
        $builder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $builder->where('aa.seq', $data['id']);
        $builder->groupBy('aa.seq');
        $result  = $builder->get()->getRowArray();

        if(!empty($result['aat_id'])){
            switch ($result['aat_type']) {
                case 'advertiser':
                    $target = $this->getSearchCompanies(null, $result['aat_id']);    
                    $result['aat_name'] = $target['name'];
                    $result['aat_status'] = $target['status'];
                    break;
                case 'campaign':
                    $target = $this->getSearchCampaigns(null, $result['aat_id']);
                    $result['aat_name'] = $target['name'];
                    $result['aat_status'] = $target['status'];
                    break;
                case 'adgroup':
                    $target = $this->getSearchAdsets(null, $result['aat_id']);
                    $result['aat_name'] = $target['name'];
                    $result['aat_status'] = $target['status'];
                    break;
                case 'ad':
                    $target = $this->getSearchAds(null, $result['aat_id']);
                    $result['aat_name'] = $target['name'];
                    $result['aat_status'] = $target['status'];
                    break;
                default:
                    break;
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
                        $executionResult = $this->getSearchCampaigns(null, $execution['id']);
                        $execution['name'] = $executionResult['name'];
                        $execution['status'] = $executionResult['status'];
                        break;
                    case 'adgroup':
                        $executionResult = $this->getSearchAdsets(null, $execution['id']);
                        $execution['name'] = $executionResult['name'];
                        $execution['status'] = $executionResult['status'];
                        break;
                    case 'ad':
                        $executionResult = $this->getSearchAds(null, $execution['id']);
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
        $subQueryBuilder->where('aar.result', 'success');
        $subQueryBuilder->groupBy('aar.idx');
        
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('
        aa.seq as aa_seq, 
        aas.idx as aas_idx, 
        aas.exec_type as aas_exec_type, 
        aas.type_value as aas_type_value, 
        DATE_FORMAT(aas.exec_time, "%H:%i") as aas_exec_time, 
        DATE_FORMAT(aas.ignore_start_time, "%H:%i") as aas_ignore_start_time, 
        DATE_FORMAT(aas.ignore_end_time, "%H:%i") as aas_ignore_end_time, 
        aas.exec_week as aas_exec_week, 
        aas.month_type as aas_month_type, 
        aas.month_day as aas_month_day, 
        aas.month_week as aas_month_week, 
        aas.reg_datetime as aas_reg_datetime, 
        aat.idx as aat_idx,
        aar_sub.aar_exec_timestamp as aar_exec_timestamp,
        ');
        $builder->join('aa_schedule aas', 'aas.idx = aa.seq', 'left');
        $builder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $builder->join("({$subQueryBuilder->getCompiledSelect()}) aar_sub", 'aar_sub.idx = aa.seq', 'left');
        $builder->where('aa.status', 1);
        $builder->groupBy('aa.seq');
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getTarget($seq)
    {   
        $builder = $this->zenith->table('aa_target as aat');
        $builder->select('
        aa.seq as aa_seq,
        aat.idx as aat_idx,
        aat.type as aat_type,
        aat.media as aat_media,
        aat.id as aat_id
        ');
        $builder->join('admanager_automation aa', 'aat.idx = aa.seq');
        $builder->where('aat.idx', $seq);
        $result = $builder->get()->getRowArray();

        return $result;
    }

    public function getTargetCompany($data)
    {
        $companyBuilder = $this->zenith->table('companies c');
        $companyBuilder->select('c.id, ca.media, GROUP_CONCAT(DISTINCT ca.ad_account_id) as ad_account_id');
        $companyBuilder->join('company_adaccounts ca', 'c.id = ca.company_id');
        $companyBuilder->where('c.id', $data['aat_id']);
        $companyBuilder->groupBy('ca.media');
        $companies = $companyBuilder->get()->getResultArray();
        $builders = [];
        foreach ($companies as $company) {
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
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad_insight_history A');
        $facebookBuilder->select(' 
            SUM(D.budget) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.spend) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.inline_link_clicks) AS click,
        ');
        $facebookBuilder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $facebookBuilder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $facebookBuilder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $facebookBuilder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
        $facebookBuilder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
        $facebookBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $facebookBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $facebookBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $facebookBuilder->whereIn('E.ad_account_id', explode(",",$company['ad_account_id']));
        $facebookBuilder->where('G.id', $data['aat_id']);
        $facebookBuilder->groupBy('G.id');

        return $facebookBuilder;
    }

    private function getGoogleByCompany($data, $company)
    {
        $googleBuilder = $this->zenith->table('z_adwords.aw_ad_report_history A');
        $googleBuilder->select('
            SUM(D.amount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.clicks) AS click,
        ');
        $googleBuilder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $googleBuilder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $googleBuilder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $googleBuilder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
        $googleBuilder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
        $googleBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $googleBuilder->where('D.status !=', 'NODATA');
        $googleBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $googleBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $googleBuilder->whereIn('E.customerId', explode(",",$company['ad_account_id']));
        $googleBuilder->where('G.id', $data['aat_id']);
        $googleBuilder->groupBy('G.id');

        return $googleBuilder;
    }

    private function getKakaoByCompany($data, $company)
    {
        $kakaoBuilder = $this->zenith->table('z_moment.mm_creative_report_basic A');
        $kakaoBuilder->select('
            SUM(D.dailyBudgetAmount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.imp) AS impressions,
            SUM(A.click) AS click,
        ');
        $kakaoBuilder->join('z_moment.mm_creative B', 'A.id = B.id');
        $kakaoBuilder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
        $kakaoBuilder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        $kakaoBuilder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $kakaoBuilder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
        $kakaoBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $kakaoBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $kakaoBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $kakaoBuilder->whereIn('E.id', explode(",",$company['ad_account_id']));
        $kakaoBuilder->where('G.id', $data['aat_id']);
        $kakaoBuilder->groupBy('G.id');

        return $kakaoBuilder;
    }

    public function getTargetFacebook($data)
    {
        $builder = $this->zenith->table('z_facebook.fb_ad_insight_history A');
        $builder->select(' 
            SUM(D.budget) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.spend) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.inline_link_clicks) AS click,
        ');
        $builder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $builder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.status AS status');
                $builder->where('E.ad_account_id', $data['aat_id']);      
                $builder->groupBy('E.ad_account_id');      
                break;
            case 'campaign':
                $builder->select('D.status AS status');
                $builder->where('D.campaign_id', $data['aat_id']);
                $builder->groupBy('D.campaign_id');  
                break;
            case 'adgroup':                
                $builder->select('C.status AS status');
                $builder->where('C.adset_id', $data['aat_id']);
                $builder->groupBy('C.adset_id');  
                break;
            case 'ad':
                $builder->select('B.status AS status');
                $builder->where('B.ad_id', $data['aat_id']);
                $builder->groupBy('B.ad_id');  
                break;
            default:
                return;
        }
       
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getTargetGoogle($data)
    {
        $builder = $this->zenith->table('z_adwords.aw_ad_report_history A');
        $builder->select('
            SUM(D.amount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.clicks) AS click,
        ');
        $builder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $builder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.status AS status');
                $builder->where('E.customerId', $data['aat_id']);  
                $builder->groupBy('E.customerId');          
                break;
            case 'campaign':
                $builder->select('D.status AS status');
                $builder->where('D.id', $data['aat_id']);
                $builder->groupBy('D.id');
                break;
            case 'adgroup':
                $builder->select('C.status AS status');
                $builder->where('C.id', $data['aat_id']);
                $builder->groupBy('C.id');
                break;
            case 'ad':
                $builder->select('B.status AS status');
                $builder->where('B.id', $data['aat_id']);
                $builder->groupBy('B.id');
                break;
            default:
                return;
        }
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getTargetKakao($data)
    {
        $builder = $this->zenith->table('z_moment.mm_creative_report_basic A');
        $builder->select('
            SUM(D.dailyBudgetAmount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.imp) AS impressions,
            SUM(A.click) AS click,
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
        $builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
        $builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        $builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.config AS status');
                $builder->where('E.id', $data['aat_id']);            
                $builder->groupBy('E.id');
                break;
            case 'campaign':
                $builder->select('D.config AS status');
                $builder->where('D.id', $data['aat_id']);
                $builder->groupBy('D.id');
                break;
            case 'adgroup':
                $builder->select('C.config AS status');
                $builder->where('C.id', $data['aat_id']);
                $builder->groupBy('C.id');
                break;
            case 'ad':
                $builder->select('B.config AS status');
                $builder->where('B.id', $data['aat_id']);
                $builder->groupBy('B.id');
                break;
            default:
                return;
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
        $builder->select('order, media, type, id, exec_type, exec_value');
        $builder->where('idx', $seq);
        $builder->orderBy('order', 'asc');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function createAutomation($data)
    {
        $aaData = [
            'subject' => $data['detail']['subject'],
            'description' => $data['detail']['description'],
            'nickname' => auth()->user()->nickname,
            'status' => 1,
            'mod_datetime' => date('Y-m-d H:i:s'),
        ];

        $this->zenith->transStart();
        $aaBuilder = $this->zenith->table('admanager_automation');
        $result = $aaBuilder->insert($aaData);
        $seq = $this->zenith->insertID();
        
        $data['schedule'] = array_filter($data['schedule']);
        $data['schedule']['idx'] = $seq;
        $aasBuilder = $this->zenith->table('aa_schedule');
        $aasBuilder->insert($data['schedule']);

        if(!empty($data['target'])){
            $data['target'] = array_filter($data['target']);
            $data['target']['idx'] = $seq;
            $aatBuilder = $this->zenith->table('aa_target');
            $aatBuilder->insert($data['target']);
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
        aas.exec_type as aas_exec_type,
        aas.type_value as aas_type_value,
        aas.exec_time as aas_exec_time,
        aas.ignore_start_time as aas_ignore_start_time,
        aas.ignore_end_time as aas_ignore_end_time,
        aas.exec_week as aas_exec_week,
        aas.month_type as aas_month_type,
        aas.month_day as aas_month_day,
        aas.month_week as aas_month_week,
        aat.type as aat_type,
        aat.media as aat_media,
        aat.id as aat_id
        ');
        $aaGetBuilder->join('aa_schedule aas', 'aas.idx = aa.seq', 'left');
        $aaGetBuilder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $aaGetBuilder->where('aa.seq', $data['seq']);
        $aaGetResult  = $aaGetBuilder->get()->getRowArray();

        $aacGetBuilder = $this->zenith->table('aa_conditions');
        $aacGetBuilder->select('order, type, type_value, compare, operation');
        $aacGetBuilder->where('idx', $data['seq']);
        $aacGetResult  = $aacGetBuilder->get()->getResultArray();

        $aaeGetBuilder = $this->zenith->table('aa_executions');
        $aaeGetBuilder->select('order, media, type, id, exec_type, exec_value');
        $aaeGetBuilder->where('idx', $data['seq']);
        $aaeGetResult  = $aaeGetBuilder->get()->getResultArray();

        $aaData = [
            'subject' => $aaGetResult['aa_subject']." - 복제",
            'description' => $aaGetResult['aa_description'] ?? null,
            'nickname' => auth()->user()->nickname,
            'status' => $aaGetResult['aa_status'],
            'mod_datetime' => date('Y-m-d H:i:s'),
        ];
        $aaBuilder = $this->zenith->table('admanager_automation');
        $aaBuilder->insert($aaData);
        $seq = $this->zenith->insertID();
        
        $aasData = [
            'idx' => $seq,
            'exec_type' => $aaGetResult['aas_exec_type'],
            'type_value' => $aaGetResult['aas_type_value'],
            'exec_time' => $aaGetResult['aas_exec_time'] ?? null,
            'ignore_start_time' => $aaGetResult['aas_ignore_start_time'] ?? null,
            'ignore_end_time' => $aaGetResult['aas_ignore_end_time'] ?? null,
            'exec_week' => $aaGetResult['aas_exec_week'] ?? null,
            'month_type' => $aaGetResult['aas_month_type'] ?? null,
            'month_day' => $aaGetResult['aas_month_day'] ?? null,
            'month_week' => $aaGetResult['aas_month_week'] ?? null,
        ];
        $aasData = array_filter($aasData);
        $aasBuilder = $this->zenith->table('aa_schedule');
        $aasBuilder->insert($aasData);

        if(!empty($aaGetResult['aat_id'])){
            $aatData = [
                'idx' => $seq,
                'type' => $aaGetResult['aat_type'],
                'media' => $aaGetResult['aat_media'],
                'id' => $aaGetResult['aat_id'],
            ];
            $aatData = array_filter($aatData);
            $aatBuilder = $this->zenith->table('aa_target');
            $aatBuilder->insert($aatData);
        }
        
        if(!empty($aacGetResult)){
            foreach ($aacGetResult as $condition) {
                $aacData = [
                    'idx' => $seq,
                    'order' => $condition['order'],
                    'type' => $condition['type'],
                    'type_value' => $condition['type_value'],
                    'compare' => $condition['compare'],
                    'operation' => $condition['operation'],
                ];
                $aacData = array_filter($aacData);
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
                'exec_value' => $execution['exec_value'],
            ];
            $aaeData = array_filter($aaeData);
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
            'mod_datetime' => date('Y-m-d H:i:s'),
        ];

        $this->zenith->transStart();
        $aaBuilder = $this->zenith->table('admanager_automation');
        $aaBuilder->where('seq', $data['seq']);
        $result = $aaBuilder->update($aaData);
        
        $data['schedule'] = array_filter($data['schedule']);
        $aasBuilder = $this->zenith->table('aa_schedule');
        $aasBuilder->where('idx', $data['seq']);
        $aasBuilder->update($data['schedule']);

        if(!empty($data['target'])){
            $data['target'] = array_filter($data['target']);
            $aatBuilder = $this->zenith->table('aa_target');
            $aatBuilder->where('idx', $data['seq']);
            $aatBuilder->update($data['target']);
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
        $builder = $this->zenith->table('aa_result');
        $result = $builder->insert($data);
        $seq = $this->zenith->insertID();
        
        return $seq;
    }

    public function recodeLog($data)
    {
        $builder = $this->zenith->table('aa_result_logs');
        $result = $builder->insert($data);

        return $result;
    }
}
